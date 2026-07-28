<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Auth', description: 'Autenticación dual (sesión por cookie para la SPA web, token Bearer para clientes móviles). Ver App\Services\Auth\AuthService.')]
class AuthDocumentation
{
    #[OA\Post(
        path: '/auth/login',
        tags: ['Auth'],
        summary: 'Iniciar sesión',
        description: "Si la petición trae sesión de Sanctum activa (dominio SPA en SANCTUM_STATEFUL_DOMAINS), inicia sesión por cookie y exige que el rol del usuario sea de staff (superadmin, moderador o admin_merchant). Si no hay sesión (cliente móvil), emite un Personal Access Token abierto a cualquier rol activo. En ambos casos, si el rol requiere alianza (admin_merchant, merchant), se valida que tenga un comercio activo asignado.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@renova.mx'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret123'),
                    new OA\Property(property: 'remember_me', type: 'boolean', description: 'Solo aplica al modo token: extiende la expiración del access token.', example: false),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login exitoso. La forma de "data" depende de si la petición fue por sesión (web) o por token (móvil).',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/SuccessResponse'),
                    ],
                    examples: [
                        new OA\Examples(
                            example: 'sesion_web',
                            summary: 'Login por cookie (SPA)',
                            value: ['success' => true, 'message' => 'Inicio de sesión exitoso.', 'data' => ['user' => ['id' => 1, 'name' => 'Eduardo', 'email' => 'admin@renova.mx']], 'errors' => null, 'code' => 200]
                        ),
                        new OA\Examples(
                            example: 'token_movil',
                            summary: 'Login por token (móvil)',
                            value: ['success' => true, 'message' => 'Inicio de sesión exitoso.', 'data' => ['access_token' => '1|abcdef...', 'token_type' => 'Bearer', 'expires_at' => '2026-08-27T00:00:00.000000Z', 'user' => ['id' => 1, 'name' => 'Eduardo', 'email' => 'admin@renova.mx']], 'errors' => null, 'code' => 200]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Credenciales incorrectas.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Cuenta desactivada, rol sin permiso para la sesión web, o comercio sin alianza activa asignada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación de los campos enviados.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function login()
    {
    }

    #[OA\Post(
        path: '/auth/register',
        tags: ['Auth'],
        summary: 'Registrar un nuevo usuario',
        description: 'Crea un usuario con el rol de registro por defecto (App\Repositories\UserRepository::defaultRegistrationRole()) y genera su code_identity (EAN-13) y secreto 2FA. Pensado para una futura app móvil; el frontend web actual no tiene pantalla de registro. Igual que en login, responde con sesión por cookie o con token según si la petición trae sesión activa de Sanctum.',
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
                description: 'Usuario registrado. Misma variación de "data" (sesión vs. token) que en login.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 422, description: 'Error de validación (email/teléfono duplicados, contraseñas no coinciden, etc.).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function register()
    {
    }

    #[OA\Get(
        path: '/auth/me',
        tags: ['Auth'],
        summary: '¿Hay una sesión válida?',
        description: "Único endpoint para verificar el estado de autenticación actual (sesión por cookie o token Bearer, según lo que envíe el cliente). IMPORTANTE: si el usuario tiene 2FA pendiente de verificar en esta sesión/token, responde con HTTP 401 pero success:true y two_factor:true — no tratar todo 401 como \"no autenticado\".",
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Sesión válida, sin 2FA pendiente.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'valida', summary: 'Sesión válida', value: ['success' => true, 'message' => 'Su sesión es válida.', 'data' => ['two_factor' => false, 'user' => ['id' => 1, 'name' => 'Eduardo']], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No hay sesión, o hay sesión pero con 2FA pendiente de verificar (ver success:true + two_factor:true en el body).',
                content: new OA\JsonContent(
                    examples: [
                        new OA\Examples(example: 'sin_sesion', summary: 'Sin autenticar', value: ['message' => 'No autenticado', 'status' => 401]),
                        new OA\Examples(example: '2fa_pendiente', summary: '2FA pendiente de verificar', value: ['success' => true, 'message' => 'Verifique su sesión.', 'data' => ['two_factor' => true, 'user' => ['id' => 1, 'name' => 'Eduardo']], 'errors' => 'Se requiere verificar su sesión', 'code' => 401]),
                    ]
                )
            ),
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
        description: 'Genera (o reutiliza) el secreto Google2FA del usuario autenticado y devuelve la URL del QR para escanear con una app autenticadora (Google Authenticator, Authy, etc.).',
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
        description: 'Confirma el código generado por la app autenticadora contra el secreto pendiente (de generateQR2FA) y activa 2FA en la cuenta. También marca la sesión/token actual como ya verificado en 2FA.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['token2FA'],
                properties: [new OA\Property(property: 'token2FA', type: 'string', minLength: 6, maxLength: 6, example: '123456')]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: '2FA habilitado.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Código de 6 dígitos inválido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación (token2FA ausente o con longitud incorrecta).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function enable2FA()
    {
    }

    #[OA\Post(
        path: '/auth/verify-2fa',
        tags: ['Auth'],
        summary: 'Verificar código 2FA de la sesión/token actual',
        description: 'Para cuentas que ya tienen 2FA habilitado: valida el código de la app autenticadora y marca la sesión (web) o el token (móvil) actual como verificado en 2FA, destrabando el resto de la API (antes de esto, GET /auth/me devuelve two_factor_pending true).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['token2FA'],
                properties: [new OA\Property(property: 'token2FA', type: 'string', minLength: 6, maxLength: 6, example: '123456')]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Código válido; sesión/token quedan marcados como verificados en 2FA.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Código de 6 dígitos inválido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación (token2FA ausente o con longitud incorrecta).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function verify2FA()
    {
    }

    #[OA\Post(
        path: '/auth/disable-2fa',
        tags: ['Auth'],
        summary: 'Deshabilitar 2FA',
        description: 'Desactiva 2FA en la cuenta del usuario autenticado y limpia su secreto Google2FA. No requiere reenviar el código actual.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(response: 200, description: '2FA deshabilitado.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function disable2FA()
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
