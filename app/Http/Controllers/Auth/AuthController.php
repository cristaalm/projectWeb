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
use PragmaRX\Google2FA\Google2FA;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Models\IdentityVerification;
use App\Http\Resources\UserResource;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use Jenssegers\Agent\Agent;
use App\Enums\AllianceStatus;

class AuthController extends Controller
{
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

    public function logout(Request $request)
    {
        $validatedData = $request->validate([
            'token' => 'required|string',
        ]);

        $token = $validatedData['token'];

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

    public function generateToken(Request $request)
    {
        try {

            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string',
                'remember_me' => 'boolean',
            ]);
            
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
            $defaultMinutes = config('auth.tokens.default_expiration', 720);      // 1 hora
            $rememberMinutes = config('auth.tokens.remember_expiration', 525600); // 30 días

            $expiresAt = 0;
            
            if ($request->remember_me) {
                $expiresAt = Carbon::now()->addMinutes(config('tokens.remember_expiration_minutes'));
            } else {
                $expiresAt = Carbon::now()->addMinutes(config('tokens.default_expiration_minutes'));
            }


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

    public function login(Request $request)
    {
        try {
            // Validar credenciales y remember_me
            $agent = new Agent();
            $agent->setUserAgent($request->userAgent());
            $agent->setHttpHeaders($request->headers->all());

            $isPhone = $agent->isPhone();
            $isDesktop = $agent->isDesktop();

            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string',
                'remember_me' => 'boolean',
            ]);
            
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

            $user->load('role');

            if ($user->role_id == 4 && !$user->alliance) {
                return $this->apiResponse(false, 'Tu cuenta no es valida, contacta con el administrador.', null, 'El usuario comerciante, no tiene un comercio asignado.', 403);
            }

            $user->load('alliance');

            if ($user->alliance && $user->alliance->status == AllianceStatus::ACTIVE->value) {
                return $this->apiResponse(false, 'El comercio al que perteneces, ya no esta vigente.', null, 'El comercio al que perteneces esta desactivado.', 403);
            }

            // // debug
            // return $this->apiResponse(false, 'Inicio de sesión exitoso.', [
            //     'tokens.default_expiration_minutes' => config('tokens.default_expiration_minutes'),
            //     'tokens.remember_expiration_minutes' => config('tokens.remember_expiration_minutes'),
            // ], null, 500);

            $expiresAt = 0;
            
            if ($request->remember_me) {
                $expiresAt = Carbon::now()->addMinutes(config('tokens.remember_expiration_minutes'));
            } else {
                $expiresAt = Carbon::now()->addMinutes(config('tokens.default_expiration_minutes'));
            }

            $webAccept = ['superadmin', 'moderador', 'admin_merchant'];
            $mobileAccept = ['member','merchant', 'superadmin', 'moderador', 'admin_merchant'];

            // vereificamos si $user->role->name esta dentro de webAccept
            if ($isDesktop && !in_array($user->role->name, $webAccept)) {
                return $this->apiResponse(false, 'No tienes permiso, para acceder al sistema.', null, null, 403);
            }
            
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

            $two_factor_status = $user->two_factor_status;


            // si la sesión actual no tiene verificado el doble factor de autenticación y el usuario tiene habilitado el doble factor de autenticación
            if ($two_factor_status && !(is_array($accessToken->abilities) && in_array('two_factor', $accessToken->abilities))) {

                // two_factor => true // redirigir a la pantalla de doble factor de autenticación
                return $this->apiResponse(true, 'Verifique su sesión', [
                    'two_factor' => true,
                    'user' => new UserResource($user),
                    'abilities' => $accessToken->abilities,
                    'expires_at' => $accessToken->expires_at,
                ], 'Se requiere verificar su sesión', 401);
            }

            // Cargar relaciones
            $user->load('role');

            // Opcional: si en el futuro usas permisos, aquí los cargarías
            // Por ahora, solo devolvemos el usuario

            return $this->apiResponse(true, 'Su sesión es válida.', [
                'user' => new UserResource($user),
                'abilities' => $accessToken->abilities,
                'expires_at' => $accessToken->expires_at,
            ], null, 200);

        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al validar su sesión.', null, $e->getMessage(), 500);
        }
    }

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

    public function generateQR2FA(Request $request)
    {
        try {
            $user = $request->user();
            $app_name = env('APP_NAME');
            $status2FA = $user->two_factor_status;

            if ($status2FA) {
                return $this->apiResponse(true, 'La autenticación de dos factores ya está habilitada.', ['two_factor_status' => $status2FA], null, 409);
            }

            $secret = $user->google2fa_secret;

            $google2fa = app(Google2FA::class);

            if (!$secret) {
                $secret = $google2fa->generateSecretKey();
                $user->google2fa_secret = $secret;
                $user->save();
            }

            $qrCodeUrl = $google2fa->getQRCodeUrl($app_name, $user->email, $secret);
            return $this->apiResponse(true, 'QR code generado correctamente.', ['two_factor_status' => $status2FA, 'qr_code_url' => $qrCodeUrl, 'secret' => $secret], null, 200);
        } catch (Exception $e) {
            return $this->apiResponse(false, 'Ocurrió un error inesperado al generar el QR code.', null, $e->getMessage(), 500);
        }
    }

    public function enable2FA(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'token2FA' => 'required|string|max:6|min:6',
            ]);

            $user = $request->user();
            $token = $validatedData['token2FA'];
            $bearerToken = $request->bearerToken();
            
            $google2fa = app(Google2FA::class);
            $is2faValid = $google2fa->verifyKey($user->google2fa_secret, $token);
            if (!$is2faValid) {
                return $this->apiResponse(false, 'Código de autenticación inválido.', null, null, 403);
            }

            // verificamos el uso de la autenticación de dos factores en la sesión actual del usuario, para evitar que lo saque de su sesión
            $accessToken = PersonalAccessToken::findToken($bearerToken);
            $accessToken->abilities = ['two_factor'];
            $accessToken->save();

            // habilitamos la autenticación de dos factores para el usuario
            $user->two_factor_status = 1;
            $user->save();

            return $this->apiResponse(true, 'Autenticación de dos factores habilitada correctamente.', null, null, 200);
        } catch (Exception $e) {
            return $this->apiResponse(false, 'Ocurrió un error inesperado al verificar la autenticación de dos factores.', null, $e->getMessage(), 500);
        }
    }

    public function verify2FA(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'token2FA' => 'required|string|max:6|min:6',
            ]);

            $user = $request->user();
            $token = $validatedData['token2FA'];
            $bearerToken = $request->bearerToken();
            
            $google2fa = app(Google2FA::class);
            $is2faValid = $google2fa->verifyKey($user->google2fa_secret, $token);
            if (!$is2faValid) {
                return $this->apiResponse(false, 'Código de autenticación inválido.', null, null, 403);
            }

            $accessToken = PersonalAccessToken::findToken($bearerToken);
            $accessToken->abilities = ['two_factor'];
            $accessToken->save();

            return $this->apiResponse(true, 'Código de autenticación válido.', null, null, 200);
        } catch (Exception $e) {
            return $this->apiResponse(false, 'Ocurrió un error inesperado al verificar la autenticación de dos factores.', null, $e->getMessage(), 500);
        }
    }

    public function disable2FA(Request $request)
    {
        try {
            $user = $request->user();
            $user->two_factor_status = 0;
            $user->google2fa_secret = null;
            $user->save();
            
            $accessToken = PersonalAccessToken::findToken($request->bearerToken());
            $accessToken->abilities = ['*'];
            $accessToken->save();

            return $this->apiResponse(true, 'Autenticación de dos factores deshabilitada correctamente.', null, null, 200);
        } catch (Exception $e) {
            return $this->apiResponse(false, 'Ocurrió un error inesperado al deshabilitar la autenticación de dos factores.', null, $e->getMessage(), 500);
        }
    }
}
