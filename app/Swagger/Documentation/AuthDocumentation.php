<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Auth', description: 'Autenticación dual (sesión por cookie para la SPA web, token Bearer para clientes móviles) con 2FA basado en un challenge temporal. Ver App\Services\Auth\AuthService.')]
class AuthDocumentation
{
    #[OA\Post(
        path: '/auth/login',
        tags: ['Auth'],
        summary: 'Iniciar sesión',
        description: "Si la petición trae sesión de Sanctum activa (dominio SPA en SANCTUM_STATEFUL_DOMAINS), exige que el rol del usuario sea de staff (superadmin, moderador o admin_merchant). Si el rol requiere alianza (admin_merchant, merchant), se valida que tenga un comercio activo asignado. Si el usuario tiene 2FA activo, NO se establece sesión/token todavía: se emite un challenge_token temporal (5 minutos) que debe resolverse en POST /auth/verify-2fa. Limitado a 6 intentos por minuto.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@renova.mx'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
                    new OA\Property(property: 'remember_me', type: 'boolean', description: 'Modo token: extiende la expiración del access token. También se respeta si el login termina resolviéndose vía 2FA.', example: false),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login exitoso directo, o challenge de 2FA pendiente de verificar — la forma de "data" varía según el caso.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [
                        new OA\Examples(
                            example: 'sesion_web',
                            summary: 'Login directo por cookie (SPA, sin 2FA)',
                            value: ['success' => true, 'message' => 'Inicio de sesión exitoso.', 'data' => ['user' => ['id' => 1, 'name' => 'Eduardo', 'email' => 'admin@renova.mx']], 'errors' => null, 'code' => 200]
                        ),
                        new OA\Examples(
                            example: 'token_movil',
                            summary: 'Login directo por token (móvil, sin 2FA)',
                            value: ['success' => true, 'message' => 'Inicio de sesión exitoso.', 'data' => ['access_token' => '1|abcdef...', 'token_type' => 'Bearer', 'expires_at' => '2026-08-27T00:00:00.000000Z', 'user' => ['id' => 1, 'name' => 'Eduardo', 'email' => 'admin@renova.mx']], 'errors' => null, 'code' => 200]
                        ),
                        new OA\Examples(
                            example: 'dos_factores_pendiente',
                            summary: '2FA activo: aún no hay sesión/token',
                            value: ['success' => true, 'message' => 'Ingresa el código de tu app de autenticación.', 'data' => ['two_factor_required' => true, 'challenge_token' => 'a1b2c3...', 'expires_at' => '2026-07-28T00:05:00.000000Z'], 'errors' => null, 'code' => 200]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Credenciales incorrectas.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Cuenta desactivada, rol sin permiso para la sesión web, o comercio sin alianza activa asignada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación de los campos enviados.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 429, description: 'Demasiados intentos de login (throttle: 6 por minuto).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function login()
    {
    }

    #[OA\Post(
        path: '/auth/social',
        tags: ['Auth'],
        summary: 'Iniciar sesión con un proveedor social (Google)',
        description: "Verifica la firma/expiración/aud de id_token contra el JWKS público del proveedor (App\Services\Auth\GoogleIdTokenVerifier) — nunca confía en datos enviados en el body más allá del token. Busca el usuario por la cuenta social ya vinculada o, si no existe, por el email del token (y lo vincula sin pisar otros proveedores ya vinculados en user_social_accounts). Si la petición trae sesión de Sanctum activa (dashboard web), NUNCA autocrea una cuenta nueva: si no hay ninguna cuenta EcoSort con ese correo, responde 403. Si no hay sesión (móvil), sí crea una cuenta incompleta (phone null, rol member) la primera vez. Mismas reglas de rol/alianza/2FA que POST /auth/login. Limitado a 6 intentos por minuto.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['provider', 'id_token'],
                properties: [
                    new OA\Property(property: 'provider', type: 'string', enum: ['google'], example: 'google'),
                    new OA\Property(property: 'id_token', type: 'string', description: 'idToken JWT firmado por el proveedor, obtenido en el cliente (Google Identity Services / SDK nativo).', example: 'eyJhbGciOi...'),
                    new OA\Property(property: 'remember_me', type: 'boolean', description: 'Modo token: extiende la expiración del access token.', example: false),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login exitoso directo, o challenge de 2FA pendiente de verificar — la forma de "data" varía según el caso.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [
                        new OA\Examples(
                            example: 'sesion_web',
                            summary: 'Login directo por cookie (SPA, sin 2FA), cuenta de staff ya existente vinculada',
                            value: ['success' => true, 'message' => 'Inicio de sesión exitoso.', 'data' => ['user' => ['id' => 1, 'name' => 'Eduardo', 'email' => 'admin@renova.mx']], 'errors' => null, 'code' => 200]
                        ),
                        new OA\Examples(
                            example: 'token_movil_cuenta_nueva',
                            summary: 'Login directo por token (móvil, sin 2FA), cuenta creada en este mismo request (phone null)',
                            value: ['success' => true, 'message' => 'Inicio de sesión exitoso.', 'data' => ['access_token' => '1|abcdef...', 'token_type' => 'Bearer', 'expires_at' => '2026-08-27T00:00:00.000000Z', 'user' => ['id' => 5, 'name' => 'Eduardo', 'phone' => null, 'email' => 'eduardo@gmail.com']], 'errors' => null, 'code' => 200]
                        ),
                        new OA\Examples(
                            example: 'dos_factores_pendiente',
                            summary: '2FA activo en la cuenta vinculada: aún no hay sesión/token',
                            value: ['success' => true, 'message' => 'Ingresa el código de tu app de autenticación.', 'data' => ['two_factor_required' => true, 'challenge_token' => 'a1b2c3...', 'expires_at' => '2026-07-28T00:05:00.000000Z'], 'errors' => null, 'code' => 200]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'idToken de Google inválido, expirado, o con aud/iss no reconocidos.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Correo de Google no verificado; cuenta desactivada, rol sin permiso o comercio sin alianza activa; o (solo en web) no existe ninguna cuenta EcoSort con ese correo.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'provider no soportado (por ahora solo "google") o campos faltantes.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 429, description: 'Demasiados intentos (throttle: 6 por minuto).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function loginSocial()
    {
    }

    #[OA\Post(
        path: '/auth/register',
        tags: ['Auth'],
        summary: 'Registrar un nuevo usuario',
        description: 'Crea un usuario con el rol de registro por defecto (App\Repositories\UserRepository::defaultRegistrationRole()) y genera su code_identity (EAN-13). Los usuarios nuevos siempre inician con 2FA desactivado, así que la respuesta es siempre login directo (sesión o token, según si la petición trae sesión activa de Sanctum). Pensado para una futura app móvil; el frontend web actual no tiene pantalla de registro.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'last_name', 'email', 'phone', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Eduardo'),
                    new OA\Property(property: 'last_name', type: 'string', maxLength: 255, example: 'Arcega'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'nuevo@renova.mx'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 255, example: '5555555555'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'secret123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'secret123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuario registrado. Misma variación de "data" (sesión vs. token) que un login directo.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 422, description: 'Error de validación (email/teléfono duplicados, contraseñas no coinciden, etc.).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function register()
    {
    }

    #[OA\Post(
        path: '/auth/verify-2fa',
        tags: ['Auth'],
        summary: 'Resolver el challenge de 2FA del login',
        description: "Endpoint PÚBLICO — no requiere sesión ni token, la credencial temporal es el challenge_token emitido por POST /auth/login (expira a los 5 minutos, un solo uso). Se debe enviar exactamente uno de token2FA (código TOTP de 6 dígitos) o recovery_code. En éxito, establece la sesión por cookie o emite el token Bearer, igual que un login directo (mismo shape de respuesta, respetando el remember_me original). Limitado a 10 intentos por minuto.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['challenge_token'],
                properties: [
                    new OA\Property(property: 'challenge_token', type: 'string', example: 'a1b2c3d4...'),
                    new OA\Property(property: 'token2FA', type: 'string', minLength: 6, maxLength: 6, nullable: true, example: '123456'),
                    new OA\Property(property: 'recovery_code', type: 'string', nullable: true, example: 'AB12C-3DE45'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Código válido: sesión/token establecidos.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'El challenge_token no existe o ya expiró — hay que iniciar sesión de nuevo.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Código TOTP o de recuperación inválido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Falta challenge_token, o no se envió exactamente uno de token2FA/recovery_code.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 429, description: 'Demasiados intentos (throttle: 10 por minuto).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function verifyTwoFactorChallenge()
    {
    }

    #[OA\Get(
        path: '/auth/me',
        tags: ['Auth'],
        summary: '¿Hay una sesión válida?',
        description: 'Único endpoint para verificar el estado de autenticación actual (sesión por cookie o token Bearer). Como una sesión/token solo se establece después de resolver el 2FA (si aplica), este endpoint ya no tiene ninguna rama especial de 2FA pendiente: o hay sesión válida, o hay un 401 normal de "no autenticado".',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Sesión válida.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'valida', summary: 'Sesión válida', value: ['success' => true, 'message' => 'Su sesión es válida.', 'data' => ['user' => ['id' => 1, 'name' => 'Eduardo']], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(examples: [new OA\Examples(example: 'sin_sesion', summary: 'Sin autenticar', value: ['message' => 'No autenticado', 'status' => 401])])),
            new OA\Response(response: 403, description: 'La cuenta del usuario ya no está activa.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function me()
    {
    }

    #[OA\Post(
        path: '/auth/logout',
        tags: ['Auth'],
        summary: 'Cerrar sesión',
        description: 'Invalida la sesión web (cookie) o revoca el access token actual (móvil), según el modo con el que se autenticó la petición.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Sesión cerrada.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function logout()
    {
    }

    #[OA\Get(
        path: '/auth/generateQR2FA',
        tags: ['Auth'],
        summary: 'Generar código QR para habilitar 2FA',
        description: 'Genera (o reutiliza) el secreto Google2FA del usuario autenticado y devuelve la URL del QR para escanear con una app autenticadora. El secreto se guarda encriptado en la base de datos.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'QR generado (o secreto ya existente reutilizado).',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'qr', summary: 'QR generado', value: ['success' => true, 'message' => 'QR code generado correctamente.', 'data' => ['two_factor_status' => false, 'qr_code_url' => 'otpauth://totp/RENOVA:admin@renova.mx?secret=...', 'secret' => 'JBSWY3DPEHPK3PXP'], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 409, description: 'El usuario ya tiene 2FA habilitado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function generateQR2FA()
    {
    }

    #[OA\Post(
        path: '/auth/enable-2fa',
        tags: ['Auth'],
        summary: 'Habilitar 2FA',
        description: 'Confirma el código generado por la app autenticadora contra el secreto pendiente (de generateQR2FA) y activa 2FA en la cuenta. Genera y devuelve 8 códigos de recuperación de un solo uso — es la única vez que se pueden leer en texto plano, guardarlos es responsabilidad del cliente.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['token2FA'],
                properties: [new OA\Property(property: 'token2FA', type: 'string', minLength: 6, maxLength: 6, example: '123456')]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '2FA habilitado; incluye los códigos de recuperación generados.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'habilitado', summary: '2FA habilitado', value: ['success' => true, 'message' => 'Autenticación de dos factores habilitada correctamente.', 'data' => ['recovery_codes' => ['AB12C-3DE45', 'FG67H-8IJ90', '...']], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Código de 6 dígitos inválido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación (token2FA ausente o con longitud incorrecta).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function enable2FA()
    {
    }

    #[OA\Post(
        path: '/auth/disable-2fa',
        tags: ['Auth'],
        summary: 'Deshabilitar 2FA',
        description: 'Desactiva 2FA en la cuenta del usuario autenticado, limpia su secreto Google2FA y borra sus códigos de recuperación. Exige reconfirmar identidad con el 2FA vigente (código TOTP o código de recuperación) — nunca se puede desactivar con un simple POST sin body.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'token2FA', type: 'string', minLength: 6, maxLength: 6, nullable: true, example: '123456'),
                    new OA\Property(property: 'recovery_code', type: 'string', nullable: true, example: 'AB12C-3DE45'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: '2FA deshabilitado.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Código TOTP o de recuperación inválido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'No se envió exactamente uno de token2FA/recovery_code.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function disable2FA()
    {
    }

    #[OA\Post(
        path: '/auth/recovery-codes/regenerate',
        tags: ['Auth'],
        summary: 'Regenerar códigos de recuperación de 2FA',
        description: 'Invalida los códigos de recuperación existentes y genera 8 nuevos. Exige el código TOTP vigente (no se acepta un recovery_code aquí, para evitar que un código de recuperación se auto-renueve indefinidamente).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['token2FA'],
                properties: [new OA\Property(property: 'token2FA', type: 'string', minLength: 6, maxLength: 6, example: '123456')]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Códigos regenerados.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Código de 6 dígitos inválido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function regenerateRecoveryCodes()
    {
    }

    #[OA\Post(
        path: '/auth/forgot-password',
        tags: ['Auth'],
        summary: 'Solicitar enlace de restablecimiento de contraseña',
        description: 'Envía (vía Laravel Password broker) un correo con enlace de restablecimiento al email indicado. En local llega a Mailpit, no a un proveedor real.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@renova.mx')]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Enlace enviado.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 404, description: 'El correo no está registrado en el sistema.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación, o el broker de Laravel rechazó el envío (p. ej. throttling).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function forgotPassword()
    {
    }

    #[OA\Post(
        path: '/auth/reset-password',
        tags: ['Auth'],
        summary: 'Restablecer contraseña con token de correo',
        description: 'Consume el token recibido por correo (enlace de forgot-password) para establecer una nueva contraseña. Notifica al usuario tras el cambio (ResetPasswordNotification).',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'password_confirmation', 'token'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@renova.mx'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'nuevoSecret123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'nuevoSecret123'),
                    new OA\Property(property: 'token', type: 'string', example: 'a1b2c3...'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Contraseña restablecida.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 422, description: 'Token inválido/expirado, error de validación, o contraseñas no coinciden.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function resetPassword()
    {
    }
}
