<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\DB;
use App\Models\IdentityVerification;
use Laravel\Sanctum\PersonalAccessToken;
use App\Enums\UserStatus;
use App\Http\Resources\UserResource;
use App\Enums\VerificationStatus;

// notifications
use App\Notifications\UserStatusAccountNotification;

class UserController extends Controller
{
    public function getAll(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $perPage = max(1, min($perPage, 100));
            $query = $request->input('query', '');
            $key = $request->input('key');
            $order = strtolower($request->input('order', 'asc'));
            $status = $request->input('status');

            $userQuery = User::query();

            if (!empty($query)) {
                $userQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('last_name', 'like', '%' . $query . '%')
                      ->orWhere('email', 'like', '%' . $query . '%')
                      ->orWhere('phone', 'like', '%' . $query . '%')
                      ->orWhere('total_points', 'like', '%' . $query . '%')
                      ->orWhere('id', 'like', '%' . $query . '%')
                      ->orWhereHas('role', function ($subQ) use ($query) {
                          $subQ->where('name', 'like', '%' . $query . '%');
                      });
                });
            }

            if (in_array($status, [0, 1])) {
                $userQuery->where('users.status', $status);
            }

            $allowedKeys = ['name', 'last_name', 'email', 'phone', 'total_points', 'created_at'];

            if (in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                $userQuery->orderBy($key, $order);
            }

            $users = $userQuery->with('role')->paginate($perPage);
            $data = $this->unsetDataPagination($users);
            return $this->apiResponse(true, 'Usuarios obtenidos exitosamente.', $data, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener los usuarios.', null, $e->getMessage(), 500);
        }
    }

    public function registerUser(Request $request)
    {
        try {
            $validateData = $request->validate([
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'phone' => 'required|string|max:255',
                'curp' => 'required|string|max:20',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if (User::where('email', $validateData['email'])->exists()) {
                return $this->apiResponse(false, 'El correo electrónico ya está en uso.', null, null, 422);
            }

            if (User::where('phone', $validateData['phone'])->exists()) {
                return $this->apiResponse(false, 'El número de teléfono ya está en uso.', null, null, 422);
            }

            if (User::where('curp', $validateData['curp'])->exists()) {
                return $this->apiResponse(false, 'El CURP ya está en uso.', null, null, 422);
            }

            $digits12 = str_pad(random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
            $checkDigit = User::calculateEan13CheckDigit($digits12);

            DB::beginTransaction();

            $user = User::create([
                'name' => $validateData['name'],
                'last_name' => $validateData['last_name'],
                'email' => $validateData['email'],
                'phone' => $validateData['phone'],
                'curp' => $validateData['curp'],
                'password' => Hash::make($validateData['password']),
                'code_identity' => $digits12 . $checkDigit,
                'role_id' => Role::firstWhere('name', 'user')?->id,
                'google2fa_secret' => (new Google2FA())->generateSecretKey(),
            ]);
            $user->save();

            IdentityVerification::create([
                'user_id' => $user->id,
                'status' => VerificationStatus::EMPTY->value,
            ]);

            DB::commit();

            return $this->apiResponse(true, 'Usuario registrado exitosamente.', $user, null, 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al registrar el usuario.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al registrar el usuario.', null, $e->getMessage(), 500);
        }
    }

    public function toggleStatusAccount(Request $request)
    {
        try {
            $authUser = $request->user();

            $validateData = $request->validate([
                'id' => 'required|exists:users,id',
                'status' => 'required|boolean',
                'justification' => 'nullable|string',
            ]);

            $user = User::findOrFail($validateData['id']);
            $user->status = $validateData['status'];
            $user->save();
            
            $description = $validateData['status'] ? 'activo' : 'desactivo';
            try {
                $user->notify(new UserStatusAccountNotification($validateData['status'], $validateData['justification']));
            } catch (\Exception $e) {
                return $this->apiResponse(true, 'Se ' . $description . ' la cuenta, pero no se pudo enviar notificación al usuario.', null, $e->getMessage(), 200);
            }

            Log::info($authUser->name . ' toggleStatusAccount to ' . $user->name . ' ' . $description);

            return $this->apiResponse(true, 'La cuenta de ' . $user->name . ' se ' . $description . ' exitosamente.', null, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al actualizar el estado de la cuenta.', null, $e->getMessage(), 500);
        }
    }

    public function identityUser(Request $request)
    {
        try {
            // token del usuario
            $validateData = $request->validate([
                'token' => 'required|string',
            ]);

            // buscamos el token
            $accessToken = PersonalAccessToken::findToken($validateData['token']);

            if (!$accessToken) {
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', null, null, 404);
            }

            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                $accessToken->delete();
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', null, 'Token expirado.', 401);
            }

            $user = $accessToken->tokenable;

            if (! $user) {
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', null, 'Usuario no encontrado.', 401);
            }

            if ($user->status !== UserStatus::ACTIVE) {
                return $this->apiResponse(false, 'La cuenta del usuario no está activa.', null, 'Cuenta desactivada.', 403);
            }

            return $this->apiResponse(true, 'Se identifico al usuario exitosamente.', [
                'user' => new UserResource($user),
            ], null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al identificar al usuario.', null, $e->getMessage(), 500);
        }
    }

    public function identityUserCode(Request $request) 
    {
        try {
            // token del usuario
            $validateData = $request->validate([
                'code' => 'required|string',
            ]);

            // buscamos el token
            $user = User::where('code_identity', $validateData['code'])->first();

            if (!$user) {
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', $validateData['code'], 'Usuario no encontrado.', 404);
            }

            if ($user->status !== UserStatus::ACTIVE) {
                return $this->apiResponse(false, 'La cuenta del usuario no está activa.', null, 'Cuenta desactivada.', 403);
            }

            return $this->apiResponse(true, 'Se identifico al usuario exitosamente.', [
                'user' => new UserResource($user),
            ], null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al identificar al usuario.', null, $e->getMessage(), 500);
        }
    }
}
