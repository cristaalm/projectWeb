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

class RewardUserController extends Controller
{
    public function claim(Request $request)
    {
        try {
            $authUser = $request->user();

            $validatedData = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'reward_id' => 'required|integer|exists:rewards,id',
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
            if ($user->total_points < $reward->points_required) {
                return $this->apiResponse(false, 'No tiene puntos suficientes.', null, null, 400);
            }

            // la recompensa debe tener stock suficiente, si es null, el stock es ilimitado
            if ($reward->stock !== null && $reward->stock < 1) {
                return $this->apiResponse(false, 'Recompensa agotada.', null, null, 400);
            }

            DB::beginTransaction();

            $rewardUser = RewardsUser::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
            ]);

            $reward->stock !== null ? $reward->stock -= 1 : null;
            $reward->save();

            $user->total_points -= $reward->points_required;
            $user->save();

            $comerciant = $authUser->role_id == 4 ? $authUser : null;

            $history = new HistoryController();
            $history->logHistory($validatedData['user_id'], $comerciant->id, $reward->alliance_id, null, $reward->id, 1, null, $reward->points_required);
            
            DB::commit();
            
            return $this->apiResponse(true, 'Recompensa reclamada exitosamente.', $rewardUser, null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error interno al reclamar la recompensa.', null, $e->getMessage(), 500);
        }
    }
}
