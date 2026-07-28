<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Auth\AuthException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Auth\DisableTwoFactorRequest;
use App\Http\Requests\Auth\EnableTwoFactorRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegenerateRecoveryCodesRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyTwoFactorChallengeRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = $this->authService->attemptCredentials(
                $request->validated('email'),
                $request->validated('password')
            );

            if ($request->hasSession()) {
                $this->authService->assertRoleAllowedForSession($user);
            }

            if ($user->two_factor_status) {
                [$challengeToken, $expiresAt] = $this->authService->issueTwoFactorChallenge(
                    $user,
                    (bool) $request->boolean('remember_me')
                );

                return $this->apiResponse(true, 'Ingresa el código de tu app de autenticación.', [
                    'two_factor_required' => true,
                    'challenge_token' => $challengeToken,
                    'expires_at' => $expiresAt,
                ], null, 200);
            }

            if ($request->hasSession()) {
                $this->authService->loginSession($request, $user);

                return $this->apiResponse(true, 'Inicio de sesión exitoso.', [
                    'user' => new UserResource($user),
                ], null, 200);
            }

            [$token, $expiresAt] = $this->authService->loginToken($user, (bool) $request->boolean('remember_me'));

            return $this->apiResponse(true, 'Inicio de sesión exitoso.', [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt,
                'user' => new UserResource($user),
            ], null, 200);
        } catch (AuthException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());
            $user->load('role');

            if ($request->hasSession()) {
                $this->authService->loginSession($request, $user);

                return $this->apiResponse(true, 'Usuario registrado exitosamente.', [
                    'user' => new UserResource($user),
                ], null, 201);
            }

            [$token, $expiresAt] = $this->authService->loginToken($user, true);

            return $this->apiResponse(true, 'Usuario registrado exitosamente.', [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt,
                'user' => new UserResource($user),
            ], null, 201);
        } catch (AuthException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    /**
     * Resuelve el challenge temporal emitido por login() cuando el usuario tiene
     * 2FA activo. No requiere sesión ni token — el challenge_token es la
     * credencial temporal. En éxito, establece la sesión/token real, igual que
     * un login exitoso.
     */
    public function verifyTwoFactorChallenge(VerifyTwoFactorChallengeRequest $request)
    {
        try {
            $result = $this->authService->resolveTwoFactorChallenge(
                $request->validated('challenge_token'),
                $request->validated('token2FA'),
                $request->validated('recovery_code'),
            );

            $user = $result['user'];

            if ($request->hasSession()) {
                $this->authService->assertRoleAllowedForSession($user);
                $this->authService->loginSession($request, $user);

                return $this->apiResponse(true, 'Inicio de sesión exitoso.', [
                    'user' => new UserResource($user),
                ], null, 200);
            }

            [$token, $expiresAt] = $this->authService->loginToken($user, $result['remember_me']);

            return $this->apiResponse(true, 'Inicio de sesión exitoso.', [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt,
                'user' => new UserResource($user),
            ], null, 200);
        } catch (AuthException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return $this->apiResponse(true, 'Sesión cerrada correctamente.', null, null, 200);
    }

    public function me(Request $request)
    {
        try {
            $result = $this->authService->validateSession($request);

            return $this->apiResponse(true, 'Su sesión es válida.', [
                'user' => new UserResource($result['user']),
            ], null, 200);
        } catch (AuthException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $this->authService->forgotPassword($request->validated('email'));

            return $this->apiResponse(true, 'Enlace de restablecimiento de contraseña enviado correctamente.', null, null, 200);
        } catch (AuthException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->authService->resetPassword($request->validated());

            return $this->apiResponse(true, 'Contraseña restablecida correctamente.', null, null, 200);
        } catch (AuthException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function generateQR2FA(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_status) {
            return $this->apiResponse(true, 'La autenticación de dos factores ya está habilitada.', ['two_factor_status' => true], null, 409);
        }

        $secret = $user->google2fa_secret;
        $google2fa = app(Google2FA::class);

        if (! $secret) {
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);

        return $this->apiResponse(true, 'QR code generado correctamente.', [
            'two_factor_status' => false,
            'qr_code_url' => $qrCodeUrl,
            'secret' => $secret,
        ], null, 200);
    }

    public function enable2FA(EnableTwoFactorRequest $request)
    {
        $user = $request->user();

        $google2fa = app(Google2FA::class);

        if (! $google2fa->verifyKey($user->google2fa_secret, $request->validated('token2FA'))) {
            return $this->apiResponse(false, 'Código de autenticación inválido.', null, null, 403);
        }

        $user->two_factor_status = true;
        $user->save();

        $recoveryCodes = $this->authService->generateRecoveryCodes($user);

        return $this->apiResponse(true, 'Autenticación de dos factores habilitada correctamente.', [
            'recovery_codes' => $recoveryCodes,
        ], null, 200);
    }

    public function disable2FA(DisableTwoFactorRequest $request)
    {
        try {
            $this->authService->disableTwoFactor(
                $request->user(),
                $request->validated('token2FA'),
                $request->validated('recovery_code'),
            );

            return $this->apiResponse(true, 'Autenticación de dos factores deshabilitada correctamente.', null, null, 200);
        } catch (AuthException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function regenerateRecoveryCodes(RegenerateRecoveryCodesRequest $request)
    {
        $user = $request->user();

        $google2fa = app(Google2FA::class);

        if (! $google2fa->verifyKey($user->google2fa_secret, $request->validated('token2FA'))) {
            return $this->apiResponse(false, 'Código de autenticación inválido.', null, null, 403);
        }

        $recoveryCodes = $this->authService->generateRecoveryCodes($user);

        return $this->apiResponse(true, 'Códigos de recuperación regenerados correctamente.', [
            'recovery_codes' => $recoveryCodes,
        ], null, 200);
    }
}
