<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Reward;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\RewardsUser;
use App\Models\User;
use App\Enums\VerificationStatus;
use App\Enums\UserStatus;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class RewardUserController extends Controller
{
    public function claim(Request $request)
    {
        try {
            $authUser = $request->user();

            $validatedData = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'reward_id' => 'required|integer|exists:rewards,id',
                'quantity' => 'required|integer',
            ]);
    
            $user = User::findOrFail($validatedData['user_id']);
            $reward = Reward::findOrFail($validatedData['reward_id']);

            if ($user->status !== UserStatus::ACTIVE) {
                return $this->apiResponse(false, 'No puede reclamar la recompensa.', null, null, 400);
            }

            // el usuario debe tener un verification_status igual a APPROVED
            if ($user->verification_status !== VerificationStatus::APPROVED) {
                return $this->apiResponse(false, 'Favor de validar su identidad, para poder reclamar la recompensa.', null, null, 400);
            }

            // la fecha de expiracion debe ser mayor a la fecha actual, si es null, la recompensa no tiene fecha de expiracion
            if ($reward->expires_at !== null && $reward->expires_at < now()) {
                return $this->apiResponse(false, 'Recompensa expirada.', null, null, 400);
            }

            // el usuario debe tener puntos suficientes
            if ($user->total_points < $reward->points_required * $validatedData['quantity']) {
                return $this->apiResponse(false, 'No tiene puntos suficientes.', null, null, 400);
            }

            // la recompensa debe tener stock suficiente, si es null, el stock es ilimitado
            if ($reward->stock !== null && ($reward->stock <= 0 || $reward->stock < $validatedData['quantity'])) {
                return $this->apiResponse(false, 'Recompensa agotada.', null, null, 400);
            }

            DB::beginTransaction();

            $rewardUser = RewardsUser::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'quantity' => $validatedData['quantity'],
            ]);

            $reward->stock !== null ? $reward->stock -= $validatedData['quantity'] : null;
            $reward->save();

            $user->total_points -= $reward->points_required * $validatedData['quantity'];
            $user->save();

            $comerciant = $authUser->role_id == 4 ? $authUser : null;
            $comerciant_id = $comerciant ? $comerciant->id : null;

            $points = $reward->points_required * $validatedData['quantity'];
            $points *= -1;

            $history = new HistoryController();
            $history->logHistory($validatedData['user_id'], $comerciant_id, $reward->alliance_id, null, $reward->id, 1, null, $validatedData['quantity'], $points, null);
            
            DB::commit();

            // Envío automático de notificaciones FCM (no afecta el resultado del claim)
            $notifications = [
                'client' => ['attempted' => 0, 'errors' => []],
                'merchant' => ['attempted' => 0, 'errors' => []],
            ];

            try {
                /** @var Messaging $messaging */
                $messaging = app(Messaging::class);

                // Notificación al cliente
                $clientTokens = DeviceToken::where('user_id', $user->id)
                    ->where('platform', 'android')
                    ->pluck('token')
                    ->all();
                $notifications['client']['attempted'] = count($clientTokens);

                $clientTitle = 'Compra finalizada';
                $clientBody = sprintf(
                    'Has canjeado %dx %s por %d puntos.',
                    (int) $validatedData['quantity'],
                    $reward->name,
                    (int) ($reward->points_required * $validatedData['quantity'])
                );

                foreach ($clientTokens as $token) {
                    try {
                        $message = CloudMessage::withTarget('token', $token)
                            ->withNotification(Notification::create($clientTitle, $clientBody))
                            ->withData(['title' => $clientTitle, 'body' => $clientBody, 'type' => 'reward_claim', 'reward_id' => (string) $reward->id]);
                        $messaging->send($message);
                    } catch (\Throwable $e) {
                        Log::warning('FCM send client error', ['token' => $token, 'error' => $e->getMessage()]);
                        $notifications['client']['errors'][] = ['token' => $token, 'error' => $e->getMessage()];
                    }
                }

                // Notificación al comerciante (si aplica)
                if ($comerciant_id) {
                    $merchantTokens = DeviceToken::where('user_id', $comerciant_id)
                        ->where('platform', 'android')
                        ->pluck('token')
                        ->all();
                    $notifications['merchant']['attempted'] = count($merchantTokens);

                    $merchantTitle = 'Venta confirmada';
                    $merchantBody = sprintf(
                        'Se procesó el canje de %dx %s para el usuario #%d.',
                        (int) $validatedData['quantity'],
                        $reward->name,
                        (int) $user->id
                    );

                    foreach ($merchantTokens as $token) {
                        try {
                            $message = CloudMessage::withTarget('token', $token)
                                ->withNotification(Notification::create($merchantTitle, $merchantBody))
                                ->withData(['title' => $merchantTitle, 'body' => $merchantBody, 'type' => 'reward_claim_merchant', 'reward_id' => (string) $reward->id]);
                            $messaging->send($message);
                        } catch (\Throwable $e) {
                            Log::warning('FCM send merchant error', ['token' => $token, 'error' => $e->getMessage()]);
                            $notifications['merchant']['errors'][] = ['token' => $token, 'error' => $e->getMessage()];
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error('FCM send post-claim error', ['error' => $e->getMessage()]);
            }
            
            return $this->apiResponse(true, 'Recompensa reclamada exitosamente.', [
                'reward' => $rewardUser,
                'notifications' => $notifications,
            ], null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error interno al reclamar la recompensa.', null, $e->getMessage(), 500);
        }
    }
}
