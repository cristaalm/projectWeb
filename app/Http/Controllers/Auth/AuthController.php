<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;
use App\Models\RolePermissions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Storage;


use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Models\IdentityVerification;
use App\Http\Resources\UserResource;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/passHash",
     *     tags={"Autenticación"},
     *     summary="Generar hash de una contraseña",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"pass"},
     *             @OA\Property(property="pass", type="string", example="12345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hash generado correctamente"
     *     ),
     *     @OA\Response(response=422, description="Datos inválidos"),
     *     @OA\Response(response=500, description="Error inesperado")
     * )
     */
    public function passHash(Request $request)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'pass' => 'required',
            ]);

            // Generar el hash de la contraseña
            $hashedPassword = Hash::make($request->pass);

            return $this->apiResponse(true, 'Hash generado correctamente.', ['hashed_password' => $hashedPassword], null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos para generar hash.', null, $e->errors(), 422);
        } catch (Exception $e) {
            return $this->apiResponse(false, 'Ocurrió un error inesperado al generar el hash.', null, $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Autenticación"},
     *     summary="Cerrar sesión y revocar token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sesión cerrada correctamente"
     *     ),
     *     @OA\Response(response=401, description="Token inválido o no proporcionado")
     * )
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return $this->apiResponse(false, 'Token de autenticación no proporcionado.', null, null, 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return $this->apiResponse(false, 'Token inválido o no encontrado.', null, null, 401);
        }

        $accessToken->delete();

        return $this->apiResponse(true, 'Sesión cerrada correctamente.', null, null, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Autenticación"},
     *     summary="Iniciar sesión",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="test@example.com"),
     *             @OA\Property(property="password", type="string", example="12345678"),
     *             @OA\Property(property="remember_me", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso"
     *     ),
     *     @OA\Response(response=401, description="Credenciales inválidas"),
     *     @OA\Response(response=403, description="Cuenta desactivada o sin permisos"),
     *     @OA\Response(response=422, description="Error de validación"),
     *     @OA\Response(response=500, description="Error inesperado")
     * )
     */
    public function login(Request $request)
    {
        try {
            // Validar credenciales y remember_me
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string',
                'remember_me' => 'boolean',
            ]);

            // debug
            
            // Buscar usuario
            $user = User::where('email', $request->email)->first();
            
            // Validar credenciales
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return $this->apiResponse(false, 'Correo electrónico o contraseña incorrectos.', null, null, 401);
            }

            // Verificar estado del usuario
            if ($user->status !== UserStatus::ACTIVE) {
                return $this->apiResponse(false, 'Tu cuenta ha sido desactivada por un administrador.', null, null, 403);
            }

            // Verificar rol activo
            if (! $user->role || ! $user->role->is_active) {
                return $this->apiResponse(false, 'Tu cuenta no tiene permiso para acceder al sistema.', null, null, 403);
            }

            // Configurar expiración según remember_me
            $defaultMinutes = config('auth.tokens.default_expiration', 60);      // 1 hora
            $rememberMinutes = config('auth.tokens.remember_expiration', 10080); // 7 días

            $expiresAt = $request->remember_me
                ? Carbon::now()->addMinutes($rememberMinutes)
                : Carbon::now()->addMinutes($defaultMinutes);

            // Cargar relaciones
            $user->load('role');

            // Generar token SIN abilities
            $token = $user->createToken(
                'auth-token',
                expiresAt: $expiresAt // Laravel 10+ soportar named params
            );

            return $this->apiResponse(true, 'Inicio de sesión exitoso.', [
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt,
                'user' => new UserResource($user),
            ], null, 200);

        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Correo electrónico o contraseña incorrectos.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Ocurrió un error inesperado al intentar iniciar sesión.', null, $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/validateToken",
     *     tags={"Autenticación"},
     *     summary="Validar token de sesión",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sesión válida"
     *     ),
     *     @OA\Response(response=401, description="Token inválido o expirado"),
     *     @OA\Response(response=403, description="Cuenta desactivada"),
     *     @OA\Response(response=500, description="Error inesperado")
     * )
     */
    public function validateToken(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (! $token) {
                return $this->apiResponse(false, 'Su sesión es inválida.', null, 'Token no proporcionado.', 401);
            }

            // Buscar el token (Sanctum ya desencripta el hash)
            $accessToken = PersonalAccessToken::findToken($token);

            if (! $accessToken) {
                return $this->apiResponse(false, 'Su sesión es inválida.', null, 'Token no encontrado.', 401);
            }

            // Verificar si el token ha expirado
            if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
                // Opcional: eliminar token expirado
                $accessToken->delete();
                return $this->apiResponse(false, 'Su sesión ha expirado.', null, 'Token expirado.', 401);
            }

            // Cargar usuario con su rol
            $user = $accessToken->tokenable;

            if (! $user) {
                return $this->apiResponse(false, 'Su sesión es inválida.', null, 'Usuario no encontrado.', 401);
            }

            // Verificar estado del usuario (activo)
            if ($user->status !== UserStatus::ACTIVE) {
                return $this->apiResponse(false, 'Tu cuenta no está activa.', null, 'Cuenta desactivada.', 403);
            }

            // Cargar relaciones
            $user->load('role');

            // Opcional: si en el futuro usas permisos, aquí los cargarías
            // Por ahora, solo devolvemos el usuario

            return $this->apiResponse(true, 'Su sesión es válida.', [
                'user' => new UserResource($user),
                'expires_at' => $accessToken->expires_at,
            ], null, 200);

        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al validar su sesión.', null, $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     tags={"Autenticación"},
     *     summary="Solicitar enlace de restablecimiento de contraseña",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="test@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enlace de restablecimiento enviado"
     *     ),
     *     @OA\Response(response=404, description="Correo no registrado"),
     *     @OA\Response(response=422, description="Error de validación"),
     *     @OA\Response(response=500, description="Error inesperado")
     * )
     */
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->apiResponse(false, 'El correo electrónico no está registrado en el sistema.', null, null, 404);
            }

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return $this->apiResponse(true, 'Enlace de restablecimiento de contraseña enviado correctamente.', null, null, 200);
            }

            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Correo electrónico inválido o no registrado.', null, $e->errors(), 422);
        } catch (Exception $e) {
            return $this->apiResponse(false, 'Ocurrió un error inesperado al enviar el enlace de restablecimiento.', null, $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/reset-password",
     *     tags={"Autenticación"},
     *     summary="Restablecer contraseña con token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password","password_confirmation","token"},
     *             @OA\Property(property="email", type="string", example="test@example.com"),
     *             @OA\Property(property="password", type="string", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", example="newpassword123"),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contraseña restablecida correctamente"
     *     ),
     *     @OA\Response(response=422, description="Error de validación"),
     *     @OA\Response(response=500, description="Error inesperado")
     * )
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
                'token' => 'required',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();

                    // Enviar correo de restablecimiento de contraseña
                    $user->notify(new ResetPasswordNotification());
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return $this->apiResponse(true, 'Contraseña restablecida correctamente.', null, null, 200);
            }

            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos para restablecer la contraseña.', null, $e->errors(), 422);
        } catch (Exception $e) {
            return $this->apiResponse(false, 'Ocurrió un error inesperado al restablecer la contraseña.', null, $e->getMessage(), 500);
        }
    }

    // public function verifyEmail(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'email' => 'required|email',
    //         ]);

    //         $user = User::where('email', $request->email)->first();
    //         if (!$user) {
    //             return $this->apiResponse(false, 'El correo electrónico no está registrado en el sistema.', null, null, 404);
    //         }

    //         $user->email_verified = true;
    //         $user->save();

    //         return $this->apiResponse(true, 'Correo electrónico verificado correctamente.', null, null, 200);
    //     } catch (ValidationException $e) {
    //         return $this->apiResponse(false, 'Correo electrónico inválido o no registrado.', null, $e->errors(), 422);
    //     } catch (Exception $e) {
    //         return $this->apiResponse(false, 'Ocurrió un error inesperado al verificar el correo electrónico.', null, $e->getMessage(), 500);
    //     }
    // }
}
