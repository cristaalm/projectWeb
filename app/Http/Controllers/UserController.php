<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\DB;
use App\Models\IdentityVerification;
use Laravel\Sanctum\PersonalAccessToken;
use App\Enums\UserStatus;
use App\Http\Resources\UserResource;
use App\Enums\VerificationStatus;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Sanctum\TransientToken;

// notifications
use App\Notifications\UserStatusAccountNotification;
use App\Mail\PointsModifiedMail;
use App\Notifications\UserCredentialsNotification;

class UserController extends Controller
{
    public function getAll(Request $request)
    {
        try {
            $normalize = function ($value) {
                if (is_string($value) && strtolower($value) === 'null') {
                    return null;
                }
                return $value;
            };
    
            $perPage = (int) ($normalize($request->input('per_page')) ?? 10);
            $perPage = max(1, min($perPage, 100));
            $query = $normalize($request->input('query')) ?? '';
            $key = $normalize($request->input('key')) ?? 'updated_at';
            $order = strtolower($normalize($request->input('order')) ?? 'desc');
            $status = $normalize($request->input('status'));
            $role = $normalize($request->input('tipo'));
            $verificationStatus = $normalize($request->input('verification_status'));

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
                          $subQ->where('display_name', 'like', '%' . $query . '%');
                      });
                });
            }

            if ($role != null && in_array($role, [1, 2, 3, 4])) {
                $userQuery->where('role_id', $role);
            }

            if ($status != null && in_array($status, [0, 1])) {
                $userQuery->where('status', $status);
            }

            if ($verificationStatus != null && in_array($verificationStatus, [0,1,2,3])) {
                $userQuery->where('verification_status', $verificationStatus);
            }

            $allowedKeys = ['name', 'last_name', 'email', 'phone', 'total_points', 'updated_at'];

            if ($key != null && in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                $userQuery->orderBy($key, $order);
            }

            $users = $userQuery->with('role')->paginate($perPage);
            $data = $this->unsetDataPagination($users);
            return $this->apiResponse(true, 'Usuarios obtenidos exitosamente.', $data, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener los usuarios.', null, $e->getMessage(), 500);
        }
    }

    public function updateField(Request $request, string $field, int $userId)
    {
        try {
            $authUser = $request->user();

            $request->merge([
                'field' => $field,
                'user_id' => $userId,
            ]);
            
            $validateData = $request->validate([
                'field' => 'required|in:name,last_name,email,phone,curp',
                'value' => 'required',
                'user_id' => 'required|exists:users,id',
            ]);
            
            $user = User::findOrFail($userId);

            DB::beginTransaction();

            $user->$field = $validateData['value'];
            $user->save();

            DB::commit();
            
            return $this->apiResponse(true, 'Campo actualizado exitosamente.', $user, null, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al actualizar el campo.', null, $e->getMessage(), 500);
        }
    }

    public function create(Request $request) 
    {
        try {
            $authUser = $request->user();
    
            $validateData = $request->validate([
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'phone' => 'nullable|string|max:255',
                'curp' => 'required|string|max:20|unique:users,curp',
                'role' => 'required|integer|in:3,4',
                'alliance' => 'nullable|integer|exists:alliances,id',
            ]);
    
            $plainPassword = User::generatePassword();
    
            $digits12 = str_pad(random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
            $checkDigit = User::calculateEan13CheckDigit($digits12);
    
            DB::beginTransaction();
    
            $user = User::create([
                'alliance_id' => $validateData['alliance'] ?? null,
                'name' => $validateData['name'],
                'last_name' => $validateData['last_name'],
                'email' => $validateData['email'],
                'phone' => $validateData['phone'],
                'curp' => $validateData['curp'],
                'password' => Hash::make($plainPassword),
                'code_identity' => $digits12 . $checkDigit,
                'verification_status' => VerificationStatus::APPROVED->value,
                'role_id' => $validateData['role'],
                'google2fa_secret' => (new Google2FA())->generateSecretKey(),
            ]);
    
            IdentityVerification::create([
                'user_id' => $user->id,
                'status' => VerificationStatus::APPROVED->value,
                'verified_by' => $authUser->id,
                'verified_at' => Carbon::now(),
            ]);
    
            DB::commit();

            $user->notify(new UserCredentialsNotification($plainPassword));
    
            return $this->apiResponse(true, 'Usuario creado exitosamente.', $user, null, 201);
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return $this->apiResponse(false, $firstError, null, $e->getMessage(), 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al registrar el usuario.', null, $e->getMessage(), 500);
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

            // generamos un token para el usuario

            $rememberMinutes = config('auth.tokens.remember_expiration', 525600); // 30 días

            $expiresAt = Carbon::now()->addMinutes($rememberMinutes);

            $user->load('role');

            // Generar token SIN abilities
            $token = $user->createToken(
                'auth-token',
                expiresAt: $expiresAt // Laravel 10+ soportar named params
            );

            return $this->apiResponse(true, 'Usuario registrado exitosamente.', [
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt,
                'user' => new UserResource($user),
            ], null, 200);
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
                'with_identity' => 'nullable|boolean',
            ]);

            // buscamos el token
            $accessToken = PersonalAccessToken::findToken($validateData['token']);

            if (!$accessToken) {
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', null, 'Token inválido.', 401);
            }

            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                $accessToken->delete();
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', null, 'Token expirado.', 401);
            }

            $user = $accessToken->tokenable;

            if (!$user) {
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', null, 'Usuario no encontrado.', 401);
            }

            if ($user->status !== UserStatus::ACTIVE) {
                return $this->apiResponse(false, 'La cuenta del usuario no está activa.', null, 'Cuenta desactivada.', 403);
            }

            if (!empty($validateData['with_identity'])) {
                $user->load('identityVerification');
            }

            return $this->apiResponse(true, 'Se identifico al usuario exitosamente.', [
                'user' => new UserResource($user),
                'identityVerification' => $validateData['with_identity'] ? $user->identityVerification : null,
            ], null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al identificar al usuario.', null, $e->getMessage(), 500);
        }
    }

    public function identityUserCode(Request $request) 
    {
        try {
            $validateData = $request->validate([
                'code' => 'required|string',
                'token' => 'nullable|string',
                'with_identity' => 'nullable|boolean',
            ]);

            // localizar usuario por código
            $user = User::where('code_identity', $validateData['code'])->first();

            if (!$user) {
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', $validateData['code'], 'Usuario no encontrado.', 404);
            }

            if ($user->status !== UserStatus::ACTIVE) {
                return $this->apiResponse(false, 'La cuenta del usuario no está activa.', null, 'Cuenta desactivada.', 403);
            }

            // tomar token del body o del Authorization Bearer
            $incomingToken = $validateData['token'] ?? null;
            $bearerToken = $request->bearerToken();
            $tokenString = $incomingToken ?: $bearerToken;

            if (!$tokenString) {
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', null, 'Necesita iniciar sesión.', 401);
            }

            $accessToken = PersonalAccessToken::findToken($tokenString);
            if (!$accessToken) {
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', null, 'Token inválido.', 401);
            }
            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                $accessToken->delete();
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', null, 'Token expirado.', 401);
            }
            
            // el token debe pertenecer al usuario identificado por código
            $tokenUser = $accessToken->tokenable;
            if (!$tokenUser || $tokenUser->id !== $user->id) {
                return $this->apiResponse(false, 'No se pudo identificar al usuario.', null, 'Necesita iniciar sesión.', 401);
            }

            if (!empty($validateData['with_identity'])) {
                $user->load('identityVerification');
            }

            return $this->apiResponse(true, 'Se identifico al usuario exitosamente.', [
                'user' => new UserResource($user),
                'identityVerification' => !empty($validateData['with_identity']) ? $user->identityVerification : null,
                'access_token' => $tokenString,
                'token_type' => 'Bearer',
                'expires_at' => $accessToken->expires_at,
            ], null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al identificar al usuario.', null, $e->getMessage(), 500);
        }
    }

    public function modifyPoints(Request $request) 
    {
        try {
            $validateData = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'new_points' => 'required|integer',
                'description' => 'required|string',
            ]);

            $user = User::findOrFail($validateData['user_id']);

            $originalPoints = $user->total_points;

            $user->total_points = $validateData['new_points'];
            $user->save();

            $history = new HistoryController();
            $history->logHistory($validateData['user_id'], null, null, null, null, 3, null, null, $validateData['new_points'], $validateData['description']);

            $sendMail = true;
            try {
                Mail::to($user->email)->send(new PointsModifiedMail($user, $originalPoints, $user->total_points, $validateData['description']));
            } catch (\Exception $e) {
                $sendMail = false;
            }

            return $this->apiResponse(true, 'Se modificaron los puntos del usuario exitosamente.', [
                'user' => new UserResource($user),
                'sendMail' => $sendMail,
            ], null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Ocurrio un error al intentar modificar los puntos del usuario.', null, $e->getMessage(), 500);
        }
    }

    public function tourComplete(Request $request, int $userId)
    {
        try {
            // merge
            $request->merge([
                'user_id' => $userId,
            ]);

            $validateData = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $user = User::findOrFail($validateData['user_id']);

            $user->tour = true;
            $user->save();

            return $this->apiResponse(true, 'Se completó el tour del usuario exitosamente.', [
                'user' => new UserResource($user),
            ], null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Ocurrio un error al intentar completar el tour del usuario.', null, $e->getMessage(), 500);
        }
    }

    public function updateAvatar(Request $request, int $id)
    {
        try {
            $request->merge([
                'avatar' => !$request->hasFile('avatar') ? null : $request->avatar,
                'delete' => $request->delete === 'true',
            ]);

            $validateData = $request->validate([
                'avatar' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:2048',
                'delete' => 'nullable|boolean',
            ]);

            $delete = $validateData['delete'] ?? false;
            $user;

            try {
                $user = User::findOrFail($id);
            } catch (\Exception $e) {
                return $this->apiResponse(false, 'El usuario no existe.', null, $e->getMessage(), 404);
            }

            if ($user->avatar || $delete) {
                Storage::delete($user->avatar);
            }

            if (!$delete && $request->hasFile('avatar')) {
                $path = "users/user_$user->id";
    
                $avatar = $request->file('avatar');
                $avatarName = 'avatar.' . $avatar->getClientOriginalExtension();
                $filePath = "$path/$avatarName";
                $avatar->storeAs($path, $avatarName, 'public');
    
                $user->avatar = $filePath;
            } else {
                $user->avatar = null;
            }
            
            $user->save();

            return $this->apiResponse(true, 'El avatar se actualizo exitosamente.', [
                'avatar_url' => $user->avatar,
            ], null, 200);
        } catch (ValidationException $e) {
            // Extraer el primer mensaje de error para devolverlo de forma clara
            $errors = $e->validator->errors()->all();
            $firstError = $errors[0] ?? 'Error de validación en los documentos.';
            
            // Mensaje específico si el error es por tamaño
            if (str_contains($firstError, 'ser mayor')) {
                $firstError = 'El tamaño de la imagen no debe exceder los 2 MB.';
            }
    
            return $this->apiResponse(false, $firstError, null, null, 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Ocurrio un error al intentar actualizar el avatar del usuario.', null, $e->getMessage(), 500);
        }
    }

    public function updateAccount(Request $request)
    {
        try {
            $validateData = $request->validate([
                'id' => 'required|integer|exists:users,id',
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'curp' => 'required|string|max:18|min:18'
            ]);

            $user = User::findOrFail($validateData['id']);

            $user->name = $validateData['name'];
            $user->last_name = $validateData['last_name'];
            $user->phone = $validateData['phone'];
            $user->curp = $validateData['curp'];
            $user->save();

            return $this->apiResponse(true, 'Se actualizo la cuenta del usuario exitosamente.', [
                'user' => new UserResource($user),
            ], null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Ocurrio un error al intentar actualizar la cuenta del usuario.', null, $e->getMessage(), 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $authUser = $request->user();
    
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);
    
            if (!Hash::check($request->current_password, $authUser->password)) {
                return $this->apiResponse(false, 'Contraseña actual incorrecta.', null, null, 401);
            }

            // la nueva contraseña, no puede ser igual a la actual
            if (Hash::check($request->password, $authUser->password)) {
                return $this->apiResponse(false, 'La nueva contraseña, no puede ser igual a la actual', null, null, 401);
            }
    
            $authUser->password = Hash::make($request->password);
            $authUser->save();
    
            // Eliminar todos los tokens del usuario, excepto el actual (solo si es un token real)
            $currentToken = $authUser->currentAccessToken();
            if (! $currentToken instanceof TransientToken) {
                $authUser->tokens()->where('id', '!=', $currentToken->id)->delete();
            }
    
            return $this->apiResponse(true, 'Contraseña restablecida exitosamente.', null, null, 200);
    
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos para restablecer la contraseña.', null, $e->errors(), 422);
        } catch (Exception $e) {
            return $this->apiResponse(false, 'Ocurrió un error inesperado al restablecer la contraseña.', null, $e->getMessage(), 500);
        }
    }
}
