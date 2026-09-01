<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\AuthException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Verifica firma/expiración/aud de un idToken de Google contra el JWKS público
 * de Google — no usa Socialite (no sirve para este flujo, ver
 * docs/context/CONTEXTO_LOGIN_SOCIAL.md). `firebase/php-jwt` ya está instalado
 * como dependencia transitiva de kreait/firebase-php, se reusa esa instalación
 * en vez de declararlo aparte (ver conversación de decisión de esa advisory).
 */
class GoogleIdTokenVerifier
{
    private const JWKS_URL = 'https://www.googleapis.com/oauth2/v3/certs';

    private const VALID_ISSUERS = ['accounts.google.com', 'https://accounts.google.com'];

    private const JWKS_CACHE_TTL_MINUTES = 60;

    /**
     * @return array{sub: string, email: string, given_name: string, family_name: string}
     */
    public function verify(string $idToken): array
    {
        $jwks = Cache::remember('google_jwks', self::JWKS_CACHE_TTL_MINUTES * 60, function () {
            return Http::get(self::JWKS_URL)->throw()->json();
        });

        try {
            $claims = (array) JWT::decode($idToken, JWK::parseKeySet($jwks));
        } catch (Throwable $e) {
            throw new AuthException('El token de Google no es válido o expiró.', 401);
        }

        if (! in_array($claims['iss'] ?? null, self::VALID_ISSUERS, true)) {
            throw new AuthException('El token de Google no es válido.', 401);
        }

        if (! in_array($claims['aud'] ?? null, config('services.google.client_ids'), true)) {
            throw new AuthException('El token de Google no es válido.', 401);
        }

        if (empty($claims['email_verified'])) {
            throw new AuthException('Tu correo de Google no está verificado.', 403);
        }

        return [
            'sub' => $claims['sub'],
            'email' => $claims['email'],
            'given_name' => $claims['given_name'] ?? ($claims['name'] ?? 'Usuario'),
            'family_name' => $claims['family_name'] ?? '',
        ];
    }
}
