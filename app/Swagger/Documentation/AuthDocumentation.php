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
        security: [
            new OA\SecurityScheme(
                securityScheme: "bearerAuth",
                type: "http",
                scheme: "bearer",
                bearerFormat: "JWT"
            )
        ],
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
                            new OA\Property(property: "user", properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Usuario"),
                                new OA\Property(property: "last_name", type: "string", example: "de prueba"),
                                new OA\Property(property: "phone", type: "string", example: "123456789"),
                                new OA\Property(property: "email", type: "string", example: "test@example.com"),
                                new OA\Property(property: "status", type: "integer", example: 1),
                                new OA\Property(property: "verification_status", type: "integer", example: 1),
                                new OA\Property(property: "total_points", type: "integer", example: 100),
                                new OA\Property(property: "role", properties: [
                                    new OA\Property(property: "id", type: "integer", example: 2),
                                    new OA\Property(property: "display_name", type: "string", example: "Administrador"),
                                    new OA\Property(property: "name", type: "string", example: "admin"),
                                    new OA\Property(property: "is_active", type: "boolean", example: true),
                                ]),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-12-31T23:59:59.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-12-31T23:59:59.000000Z"),
                            ]),
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
                            new OA\Property(property: "user", properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Usuario"),
                                new OA\Property(property: "last_name", type: "string", example: "de prueba"),
                                new OA\Property(property: "phone", type: "string", example: "123456789"),
                                new OA\Property(property: "email", type: "string", example: "test@example.com"),
                                new OA\Property(property: "status", type: "integer", example: 1),
                                new OA\Property(property: "verification_status", type: "integer", example: 1),
                                new OA\Property(property: "total_points", type: "integer", example: 100),
                                new OA\Property(property: "role", properties: [
                                    new OA\Property(property: "id", type: "integer", example: 2),
                                    new OA\Property(property: "display_name", type: "string", example: "Administrador"),
                                    new OA\Property(property: "name", type: "string", example: "admin"),
                                    new OA\Property(property: "is_active", type: "boolean", example: true),
                                ]),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-12-31T23:59:59.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-12-31T23:59:59.000000Z"),
                            ]),
                            new OA\Property(property: "expires_at", type: "string", format: "date-time", example: "2025-12-31T23:59:59.000000Z"),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token inválido o expirado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Su sesión es inválida."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Token expirado."),
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
}
