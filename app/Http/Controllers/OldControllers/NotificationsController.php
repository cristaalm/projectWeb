<?php

namespace App\Http\Controllers\OldControllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationsController extends Controller
{
    public function registerToken(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'token' => 'required|string|max:255',
                'platform' => 'required|string|in:android',
            ]);

            $authUser = $request->user();
            if (!$authUser || (int) $validated['user_id'] !== (int) $authUser->id) {
                return $this->apiResponse(false, 'No autorizado para registrar este token.', null, null, 403);
            }

            DeviceToken::updateOrCreate(
                [
                    'user_id' => $validated['user_id'],
                    'platform' => $validated['platform'],
                ],
                [
                    'token' => $validated['token'],
                ]
            );

            return $this->apiResponse(true, 'registered', null, null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos para registrar token.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('FCM registerToken error', ['error' => $e->getMessage()]);
            return $this->apiResponse(false, 'Error al registrar token.', null, $e->getMessage(), 500);
        }
    }

    public function send(Request $request, Messaging $messaging)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'title' => 'required|string|max:100',
                'message' => 'required|string|max:500',
            ]);

            $authUser = $request->user();
            if (!$authUser || (int) $validated['user_id'] !== (int) $authUser->id) {
                return $this->apiResponse(false, 'No autorizado para enviar notificaciones para este usuario.', null, null, 403);
            }

            $tokens = DeviceToken::where('user_id', $validated['user_id'])
                ->where('platform', 'android')
                ->pluck('token')
                ->all();

            if (empty($tokens)) {
                return $this->apiResponse(false, 'No hay dispositivos registrados para el usuario.', null, 'Sin tokens', 404);
            }

            $title = $validated['title'];
            $body = $validated['message'];

            $errors = [];
            foreach ($tokens as $token) {
                try {
                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification(Notification::create($title, $body))
                        ->withData(['title' => $title, 'body' => $body]);

                    $messaging->send($message);
                } catch (\Throwable $e) {
                    Log::warning('FCM send error', ['token' => $token, 'error' => $e->getMessage()]);
                    $errors[] = ['token' => $token, 'error' => $e->getMessage()];
                }
            }

            if (!empty($errors) && count($errors) === count($tokens)) {
                return $this->apiResponse(false, 'Error al enviar notificaciones.', null, $errors, 500);
            }

            return $this->apiResponse(true, 'sent', [
                'attempted' => count($tokens),
                'errors' => $errors,
            ], null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos para enviar notificación.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('FCM send error', ['error' => $e->getMessage()]);
            return $this->apiResponse(false, 'Error al enviar notificación.', null, $e->getMessage(), 500);
        }
    }

    public function deleteToken(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'token' => 'nullable|string|max:255',
                'platform' => 'nullable|string|in:android',
            ]);

            $authUser = $request->user();
            if (!$authUser || (int) $validated['user_id'] !== (int) $authUser->id) {
                return $this->apiResponse(false, 'No autorizado para eliminar tokens de este usuario.', null, null, 403);
            }

            $hasToken = !empty($validated['token'] ?? null);
            $hasPlatform = !empty($validated['platform'] ?? null);

            if (!$hasToken && !$hasPlatform) {
                return $this->apiResponse(false, 'Debe proporcionar token o platform.', null, ['token' => ['Se requiere token o platform']], 422);
            }
            if ($hasToken && $hasPlatform) {
                return $this->apiResponse(false, 'Proporcione solo token o solo platform.', null, ['token' => ['No puede enviar ambos']], 422);
            }

            $deleted = 0;
            if ($hasToken) {
                $deleted = DeviceToken::where('user_id', $validated['user_id'])
                    ->where('token', $validated['token'])
                    ->delete();
            } else {
                $deleted = DeviceToken::where('user_id', $validated['user_id'])
                    ->where('platform', $validated['platform'])
                    ->delete();
            }

            if ($deleted === 0) {
                return $this->apiResponse(false, 'token_not_found', null, null, 404);
            }

            return $this->apiResponse(true, 'deleted', ['deleted' => $deleted], null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos para eliminar token.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('FCM deleteToken error', ['error' => $e->getMessage()]);
            return $this->apiResponse(false, 'Error al eliminar token.', null, $e->getMessage(), 500);
        }
    }
}