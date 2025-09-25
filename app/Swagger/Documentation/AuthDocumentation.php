<?php

/*
*
*   ======================================== IMPORTANTE ========================================
*
*   Esta es la documentación de el controlador
*   App\Http\Controllers\Auth\AuthController
*
*   Puedes generar mas rapido la documentación de los enpoinds
*   pasando a la IA tu metodo y con el siguiente prompt:
*
*
*
*
*   Genera la documentación de Swagger para mi metodo [Nombre del metodo/funcion que deseas documentar]
*
*   documenta usando atributos de PHP 8+ (como #[OA\Post(...)], #[OA\Property(...)], etc.)
*
*   [Tu metodo/funcion]
*
*   ============================================================================================
*
*/

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

use App\Swagger\Schemas\UserSchema;

#[OA\Tag(name: 'Autenticación', description: 'Endpoints para gestión de autenticación y tokens')]
class AuthDocumentation
{
    #[OA\Post(
        path: "/api/auth/passHash",
        tags: ["Autenticación"],
        summary: "Generar hash de una contraseña",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["pass"],
                properties: [
                    new OA\Property(property: "pass", type: "string", example: "12345678"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Hash generado correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Hash generado correctamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "hashed_password", type: "string", example: "\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi"),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Datos inválidos",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Datos inválidos para generar hash."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "pass", type: "array", items: new OA\Items(type: "string", example: "The pass field is required.")),
                        ]),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado al generar el hash."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function passHash() {}

    #[OA\Post(
        path: "/api/auth/logout",
        tags: ["Autenticación"],
        summary: "Cerrar sesión y revocar token",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "token", type: "string", example: "your_token_here"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Sesión cerrada correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Sesión cerrada correctamente."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token inválido o no proporcionado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Token de autenticación no proporcionado."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 401),
                    ]
                )
            ),
        ]
    )]
    public function logout() {}

    #[OA\Post(
        path: "/api/auth/login",
        tags: ["Autenticación"],
        summary: "Iniciar sesión",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "test@example.com"),
                    new OA\Property(property: "password", type: "string", example: "12345678"),
                    new OA\Property(property: "remember_me", type: "boolean", example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Inicio de sesión exitoso",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Inicio de sesión exitoso."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"),
                            new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                            new OA\Property(property: "expires_at", type: "string", format: "date-time", example: "2025-12-31T23:59:59.000000Z"),
                            new OA\Property(property: "user", type: "array", items: new OA\Items(ref: "#/components/schemas/User")),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Credenciales inválidas",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Correo electrónico o contraseña incorrectos."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 401),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Cuenta desactivada o sin permisos",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Tu cuenta ha sido desactivada por un administrador."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 403),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Correo electrónico o contraseña incorrectos."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "email", type: "array", items: new OA\Items(type: "string", example: "The selected email is invalid.")),
                        ]),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado al intentar iniciar sesión."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function login() {}

    #[OA\Post(
        path: "/api/auth/validateToken",
        tags: ["Autenticación"],
        summary: "Validar token de sesión",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Sesión válida",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Su sesión es válida."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "user", ref: "#/components/schemas/User"),
                            new OA\Property(property: "abilities", type: "array", items: new OA\Items(type: "string", example: "two_factor")),
                            new OA\Property(property: "expires_at", type: "string", format: "date-time", example: "2025-12-31T23:59:59.000000Z"),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token inválido, expirado o requiere 2FA",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true), // Nota: éxito parcial
                        new OA\Property(property: "message", type: "string", example: "Verifique su sesión"),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "two_factor", type: "boolean", example: true),
                            new OA\Property(property: "user", ref: "#/components/schemas/User"),
                            new OA\Property(property: "abilities", type: "array", items: new OA\Items(type: "string")),
                            new OA\Property(property: "expires_at", type: "string", format: "date-time", example: "2025-12-31T23:59:59.000000Z"),
                        ]),
                        new OA\Property(property: "errors", type: "string", example: "Se requiere verificar su sesión"),
                        new OA\Property(property: "status", type: "integer", example: 401),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Cuenta desactivada",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Tu cuenta no está activa."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Cuenta desactivada."),
                        new OA\Property(property: "status", type: "integer", example: 403),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al validar su sesión."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function validateToken() {}

    #[OA\Post(
        path: "/api/auth/forgot-password",
        tags: ["Autenticación"],
        summary: "Solicitar enlace de restablecimiento de contraseña",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "test@example.com"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Enlace de restablecimiento enviado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Enlace de restablecimiento de contraseña enviado correctamente."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Correo no registrado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "El correo electrónico no está registrado en el sistema."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 404),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Correo electrónico inválido o no registrado."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "email", type: "array", items: new OA\Items(type: "string", example: "We can't find a user with that email address.")),
                        ]),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado al enviar el enlace de restablecimiento."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function forgotPassword() {}

    #[OA\Post(
        path: "/api/auth/reset-password",
        tags: ["Autenticación"],
        summary: "Restablecer contraseña con token generado por el sistema",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password", "password_confirmation", "token"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "test@example.com"),
                    new OA\Property(property: "password", type: "string", example: "newpassword123"),
                    new OA\Property(property: "password_confirmation", type: "string", example: "newpassword123"),
                    new OA\Property(property: "token", type: "string", example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Contraseña restablecida correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Contraseña restablecida correctamente."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Datos inválidos para restablecer la contraseña."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "email", type: "array", items: new OA\Items(type: "string", example: "We can't find a user with that email address.")),
                            new OA\Property(property: "password", type: "array", items: new OA\Items(type: "string", example: "The password must be at least 8 characters.")),
                            new OA\Property(property: "token", type: "array", items: new OA\Items(type: "string", example: "This password reset token is invalid.")),
                        ]),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado al restablecer la contraseña."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function resetPassword() {}

    #[OA\Get(
        path: "/api/auth/generateQR2FA",
        tags: ["Autenticación"],
        summary: "Generar QR y secreto para configurar 2FA",
        description: "Genera un código QR y un secreto para que el usuario configure la autenticación de dos factores en su app (Google Authenticator, Authy, etc.).",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "QR generado correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "QR code generado correctamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "two_factor_status", type: "boolean", example: false),
                            new OA\Property(property: "qr_code_url", type: "string", example: "otpauth://totp/AppName:user@example.com?secret=JBSWY3DPEHPK3PXP&issuer=AppName"),
                            new OA\Property(property: "secret", type: "string", example: "JBSWY3DPEHPK3PXP"),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: "2FA ya está habilitada",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "La autenticación de dos factores ya está habilitada."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "two_factor_status", type: "boolean", example: true),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 409),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado al generar el QR code."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function generateQR2FA(Request $request) {}
    
    #[OA\Post(
        path: "/api/auth/enable-2fa",
        tags: ["Autenticación"],
        summary: "Habilitar autenticación de dos factores",
        description: "Habilita la autenticación de dos factores para el usuario, verificando un token TOTP válido.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["token2FA"],
                properties: [
                    new OA\Property(property: "token2FA", type: "string", example: "123456", description: "Código de 6 dígitos de la app de autenticación"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "2FA habilitada correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Autenticación de dos factores habilitada correctamente."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Token inválido",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Código de autenticación inválido."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 403),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado al verificar la autenticación de dos factores."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function enable2FA(Request $request) {}
    
    #[OA\Post(
        path: "/api/auth/verify-2fa",
        tags: ["Autenticación"],
        summary: "Verificar token de 2FA (usado en login)",
        description: "Verifica un token TOTP durante el proceso de inicio de sesión para completar la autenticación de dos factores.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["token2FA"],
                properties: [
                    new OA\Property(property: "token2FA", type: "string", example: "654321", description: "Código de 6 dígitos de la app de autenticación"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Token válido",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Código de autenticación válido."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Token inválido",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Código de autenticación inválido."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 403),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado al verificar la autenticación de dos factores."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function verify2FA(Request $request) {}
    
    #[OA\Post(
        path: "/api/auth/disable-2fa",
        tags: ["Autenticación"],
        summary: "Deshabilitar autenticación de dos factores",
        description: "Deshabilita la autenticación de dos factores para el usuario y elimina el secreto.",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "2FA deshabilitada correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Autenticación de dos factores deshabilitada correctamente."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ocurrió un error inesperado al deshabilitar la autenticación de dos factores."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function disable2FA(Request $request) {}
}
