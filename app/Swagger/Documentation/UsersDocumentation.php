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
use App\Swagger\Schemas\IdentityVerificationSchema;

#[OA\Tag(name: 'Usuarios', description: 'Endpoints para gestión de usuarios')]
class UsersDocumentation
{
    #[OA\Get(
        path: "/api/users/getAll",
        tags: ["Usuarios"],
        summary: "Obtener lista paginada de usuarios",
        description: "Devuelve una lista paginada de usuarios con búsqueda global, filtrado por estado y ordenamiento opcional.",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "per_page",
                description: "Número de registros por página (mín: 1, máx: 100, por defecto: 10)",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 10)
            ),
            new OA\Parameter(
                name: "query",
                description: "Búsqueda global en: nombre, apellido, correo, teléfono, dirección o nombre del rol",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: "juan")
            ),
            new OA\Parameter(
                name: "key",
                description: "Campo por el cual ordenar. Valores permitidos: 'users.name', 'users.last_name', 'users.email', 'users.phone', 'users.total_points'",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["users.name", "users.last_name", "users.email", "users.phone", "users.total_points"],
                    example: "users.name"
                )
            ),
            new OA\Parameter(
                name: "order",
                description: "Orden de los resultados. Valores permitidos: 'asc', 'desc'",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["asc", "desc"], example: "asc")
            ),
            new OA\Parameter(
                name: "status",
                description: "Filtrar por estado. 1 = Activo, 0 = Inactivo",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", enum: [0, 1], example: 1)
            ),
            new OA\Parameter(
                name: "page",
                description: "Número de página para paginación",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de usuarios obtenida correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Usuarios obtenidos exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "data", type: "array", items: new OA\Items(properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Juan"),
                                new OA\Property(property: "last_name", type: "string", example: "Pérez"),
                                new OA\Property(property: "email", type: "string", example: "juan@example.com"),
                                new OA\Property(property: "phone", type: "string", example: "+57 300 1234567"),
                                new OA\Property(property: "total_points", type: "integer", example: 150),
                                new OA\Property(property: "status", type: "integer", example: 1, description: "1 = Activo, 0 = Inactivo"),
                                new OA\Property(property: "verification_status", type: "integer", example: 1, description: "Estado de verificación de identidad"),
                                new OA\Property(property: "avatar", type: "string", example: "avatar.jpg", nullable: true),
                                new OA\Property(property: "two_factor_status", type: "boolean", example: true),
                                new OA\Property(property: "role_id", type: "integer", example: 2),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-01-01T10:00:00.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T15:30:00.000000Z"),
                                new OA\Property(property: "role", properties: [
                                    new OA\Property(property: "id", type: "integer", example: 2),
                                    new OA\Property(property: "name", type: "string", example: "user"),
                                    new OA\Property(property: "display_name", type: "string", example: "Usuario"),
                                    new OA\Property(property: "description", type: "string", example: "Rol estándar para usuarios registrados."),
                                    new OA\Property(property: "is_active", type: "boolean", example: true),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-01-01T00:00:00.000000Z"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-01-01T00:00:00.000000Z"),
                                ]),
                            ])),
                            new OA\Property(property: "current_page", type: "integer", example: 1),
                            new OA\Property(property: "first_page_url", type: "string", example: "http://localhost/api/users/getAll?page=1"),
                            new OA\Property(property: "from", type: "integer", example: 1),
                            new OA\Property(property: "last_page", type: "integer", example: 3),
                            new OA\Property(property: "last_page_url", type: "string", example: "http://localhost/api/users/getAll?page=3"),
                            new OA\Property(property: "next_page_url", type: "string", nullable: true, example: "http://localhost/api/users/getAll?page=2"),
                            new OA\Property(property: "path", type: "string", example: "http://localhost/api/users/getAll"),
                            new OA\Property(property: "per_page", type: "integer", example: 10),
                            new OA\Property(property: "prev_page_url", type: "string", nullable: true, example: null),
                            new OA\Property(property: "to", type: "integer", example: 10),
                            new OA\Property(property: "total", type: "integer", example: 25),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token de autenticación no válido o no proporcionado",
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
            new OA\Response(
                response: 500,
                description: "Error inesperado al obtener los usuarios",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener los usuarios."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function getAll(Request $request) {}

    #[OA\Post(
        path: "/api/users/updateField/{field}/{userId}",
        tags: ["Usuarios"],
        summary: "Actualizar un campo específico del perfil de usuario",
        description: "Permite actualizar un campo individual del perfil de un usuario (nombre, apellido, correo, teléfono o CURP).",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "field",
                description: "Nombre del campo a actualizar",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", enum: ["name", "last_name", "email", "phone", "curp"], example: "email")
            ),
            new OA\Parameter(
                name: "userId",
                description: "ID del usuario a actualizar",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 5)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["value"],
                properties: [
                    new OA\Property(
                        property: "value",
                        type: "string",
                        example: "nuevo.email@example.com",
                        description: "Nuevo valor para el campo especificado"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Campo actualizado exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Campo actualizado exitosamente."),
                        new OA\Property(property: "data", ref: "#/components/schemas/User"),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token de autenticación no válido o no proporcionado",
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
            new OA\Response(
                response: 403,
                description: "No autorizado para actualizar el perfil del usuario",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No tienes permiso para actualizar este perfil."),
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
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar el campo."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "field", type: "array", items: new OA\Items(type: "string", example: "The selected field is invalid.")),
                            new OA\Property(property: "value", type: "array", items: new OA\Items(type: "string", example: "The value field is required.")),
                            new OA\Property(property: "user_id", type: "array", items: new OA\Items(type: "string", example: "The selected user id is invalid.")),
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
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar el campo."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function updateField(Request $request, string $field, int $userId) {}

    #[OA\Post(
        path: "/api/users/toggleStatusAccount",
        tags: ["Usuarios"],
        summary: "Cambiar el estado de activación de una cuenta de usuario",
        description: "Permite a un usuario autorizado (ej. administrador) activar o desactivar la cuenta de otro usuario. Opcionalmente, se puede incluir una justificación que se enviará al usuario afectado por notificación.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["id", "status"],
                properties: [
                    new OA\Property(
                        property: "id",
                        type: "integer",
                        example: 5,
                        description: "ID del usuario cuya cuenta se desea activar/desactivar"
                    ),
                    new OA\Property(
                        property: "status",
                        type: "boolean",
                        example: false,
                        description: "true = activar cuenta, false = desactivar cuenta"
                    ),
                    new OA\Property(
                        property: "justification",
                        type: "string",
                        example: "Violación de términos de uso.",
                        description: "Justificación opcional para la desactivación (solo se envía si existe)",
                        nullable: true
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Estado de la cuenta actualizado correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "La cuenta de Juan Pérez se desactivo exitosamente."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token de autenticación no válido o no proporcionado",
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
            new OA\Response(
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar el estado de la cuenta."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "id", type: "array", items: new OA\Items(type: "string", example: "The selected id is invalid.")),
                            new OA\Property(property: "status", type: "array", items: new OA\Items(type: "string", example: "The status field is required.")),
                        ]),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al actualizar el estado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar el estado de la cuenta."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function toggleStatusAccount(Request $request) {}

    #[OA\Post(
        path: "/api/users/register",
        tags: ["Usuarios"],
        summary: "Registrar un nuevo usuario",
        description: "Crea una nueva cuenta de usuario en el sistema. El correo electrónico y el número de teléfono deben ser únicos.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "last_name", "email", "phone", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Juan", description: "Nombre del usuario"),
                    new OA\Property(property: "last_name", type: "string", example: "Pérez", description: "Apellido del usuario"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "juan.perez@example.com", description: "Correo electrónico único"),
                    new OA\Property(property: "phone", type: "string", example: "+57 300 1234567", description: "Número de teléfono único"),
                    new OA\Property(property: "curp", type: "string", example: "CURP", description: "CURP del usuario"),
                    new OA\Property(property: "password", type: "string", example: "password123", description: "Contraseña (mínimo 8 caracteres)"),
                    new OA\Property(property: "password_confirmation", type: "string", example: "password123", description: "Confirmación de la contraseña"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Usuario registrado exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Usuario registrado exitosamente."),
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
                response: 422,
                description: "Error de validación o datos duplicados",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al registrar el usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", oneOf: [
                            // Caso 1: Validación de campos
                            new OA\Schema(type: "object", properties: [
                                new OA\Property(property: "email", type: "array", items: new OA\Items(type: "string", example: "The email field is required.")),
                                new OA\Property(property: "password", type: "array", items: new OA\Items(type: "string", example: "The password must be at least 8 characters.")),
                            ]),
                            // Caso 2: Email duplicado
                            new OA\Schema(type: "null", example: null),
                            // Caso 3: Teléfono duplicado
                            new OA\Schema(type: "null", example: null),
                        ], description: "Puede ser un objeto de errores de validación o null si el error es por duplicado"),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al registrar el usuario",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al registrar el usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function registerUser(Request $request) {}

    #[OA\Post(
        path: "/api/users/identityUser",
        tags: ["Usuarios"],
        summary: "Identificar al usuario",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["token"],
                properties: [
                    new OA\Property(property: "token", type: "string", example: "1|as87cwe4t98wea5a91sda65sd1va6sd74n2ta98waw56", description: "Token de sesión"),
                    new OA\Property(property: "with_identity", type: "boolean", example: false, description: "Indica si se desea obtener la información de la verificación de identidad"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Sesión válida",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Usuario identificado exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "user", ref: "#/components/schemas/User"),
                            new OA\Property(property: "identityVerification", ref: "#/components/schemas/IdentityVerification"),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token inválido, expirado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true), // Nota: éxito parcial
                        new OA\Property(property: "message", type: "string", example: "No se pudo identificar al usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Usuario no encontrado."),
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
                        new OA\Property(property: "message", type: "string", example: "La cuenta del usuario no está activa."),
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
                        new OA\Property(property: "message", type: "string", example: "Error al identificar al usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function identityUser() {}

    #[OA\Post(
        path: "/api/users/identityUserCode",
        tags: ["Usuarios"],
        summary: "Identificar al usuario",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["code"],
                properties: [
                    new OA\Property(property: "code", type: "string", example: "4075224740324", description: "Código de identificación de 13 digitos"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Se identifico al usuario exitosamente.",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Usuario identificado exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "user", ref: "#/components/schemas/User"),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "El codigo esta mal o el usuario no existe",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "No se pudo identificar al usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Usuario no encontrado."),
                        new OA\Property(property: "status", type: "integer", example: 404),
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
                        new OA\Property(property: "message", type: "string", example: "La cuenta del usuario no está activa."),
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
                        new OA\Property(property: "message", type: "string", example: "Error al identificar al usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function identityUserCode() {}

    #[OA\Post(
        path: "/api/users/tourComplete/{userId}",
        tags: ["Usuarios"],
        summary: "Marcar el tour introductorio como completado",
        description: "Marca el tour introductorio de la aplicación como completado para un usuario específico. Esto evita que se muestre nuevamente al iniciar sesión.",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "userId",
                description: "ID del usuario para el que se marca el tour como completado",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 5)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id"],
                properties: [
                    new OA\Property(
                        property: "user_id",
                        type: "integer",
                        example: 5,
                        description: "ID del usuario (debe coincidir con el parámetro de la URL)"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Tour completado exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Se completo el tour del usuario exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "user", ref: "#/components/schemas/User"),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token de autenticación no válido o no proporcionado",
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
            new OA\Response(
                response: 403,
                description: "No autorizado para modificar el tour del usuario",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No tienes permiso para modificar el tour de este usuario."),
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
                        new OA\Property(property: "message", type: "string", example: "Ocurrio un error al intentar completar el tour del usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "user_id", type: "array", items: new OA\Items(type: "string", example: "The selected user id is invalid.")),
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
                        new OA\Property(property: "message", type: "string", example: "Ocurrio un error al intentar completar el tour del usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function tourComplete(Request $request, int $userId) {}

    #[OA\Get(
        path: "/api/users/getStreak",
        tags: ["Usuarios"],
        summary: "Obtener la racha actual de escaneos diarios",
        description: "Devuelve el número de días consecutivos en los que el usuario ha realizado al menos un escaneo válido. La racha no se reinicia si el usuario aún no ha escaneado hoy. También indica si la racha está activa (si ya escaneó hoy).",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Racha calculada exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Racha calculada exitosamente."),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "streak", type: "integer", example: 5, description: "Número de días consecutivos con al menos un escaneo válido"),
                                new OA\Property(property: "is_active", type: "boolean", example: true, description: "Indica si el usuario ya realizó al menos un escaneo hoy")
                            ]
                        ),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200)
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token de autenticación no válido o no proporcionado",
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
            new OA\Response(
                response: 500,
                description: "Error inesperado al calcular la racha",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al calcular la racha."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function getStreak(Request $request) {}

    #[OA\Get(
        path: "/api/users/getScansByDayOfWeek",
        tags: ["Usuarios"],
        summary: "Obtener cantidad de escaneos por día de la semana",
        description: "Devuelve un listado con la cantidad de escaneos válidos realizados por el usuario en cada día de la semana (lunes a domingo), basado en la semana actual (del lunes al domingo). Si un día no tiene escaneos, su conteo será 0.",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Escaneos por día obtenidos exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Escaneos por día de la semana."),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "day", type: "string", example: "Lunes", description: "Nombre del día en español"),
                                    new OA\Property(property: "scans_count", type: "integer", example: 3, description: "Cantidad de escaneos válidos realizados ese día")
                                ]
                            )
                        ),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200)
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Token de autenticación no válido o no proporcionado",
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
            new OA\Response(
                response: 500,
                description: "Error inesperado al obtener los escaneos por día",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener los escaneos por día."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function getScansByDayOfWeek(Request $request) {}

    #[OA\Post(
        path: '/api/users/update-badge',
        summary: 'Actualizar una insignia para un usuario',
        description: 'Permite intentar desbloquear una insignia si el usuario cumple con los puntos mensuales requeridos. Las insignias disponibles son: Eco Warrior (100), Recycler Pro (500), Green Hero (1000), Planet Saver (2500). Se inicializa automáticamente si el campo badge es nulo.',
        tags: ['Usuarios'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['user_id', 'badge'],
                properties: [
                    new OA\Property(property: 'user_id', type: 'integer', example: 5, description: 'ID del usuario'),
                    new OA\Property(
                        property: 'badge',
                        type: 'string',
                        enum: ['Eco Warrior', 'Recycler Pro', 'Green Hero', 'Planet Saver'],
                        example: 'Recycler Pro',
                        description: 'Nombre de la insignia a desbloquear'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Insignia actualizada exitosamente',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: '¡Felicidades! Has obtenido la insignia \'Recycler Pro\'.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            description: 'Estado actualizado de todas las insignias',
                            properties: [
                                new OA\Property(property: 'Eco Warrior', type: 'boolean', example: true),
                                new OA\Property(property: 'Recycler Pro', type: 'boolean', example: false),
                                new OA\Property(property: 'Green Hero', type: 'boolean', example: false),
                                new OA\Property(property: 'Planet Saver', type: 'boolean', example: false),
                            ],
                            example: [
                                'Eco Warrior' => true,
                                'Recycler Pro' => false,
                                'Green Hero' => false,
                                'Planet Saver' => false,
                            ]
                        ),
                        new OA\Property(property: 'errors', type: 'null', example: null),
                        new OA\Property(property: 'status', type: 'integer', example: 200)
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'No cumple con los requisitos de puntos',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'No cumples con los puntos necesarios para obtener la insignia \'Recycler Pro\'. Necesitas al menos 500 puntos este mes.'
                        ),
                        new OA\Property(property: 'data', type: 'null', example: null),
                        new OA\Property(property: 'errors', type: 'null', example: null),
                        new OA\Property(property: 'status', type: 'integer', example: 403)
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Usuario no encontrado',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Usuario no encontrado.'),
                        new OA\Property(property: 'data', type: 'null', example: null),
                        new OA\Property(property: 'errors', type: 'null', example: null),
                        new OA\Property(property: 'status', type: 'integer', example: 404)
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Datos inválidos',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Datos inválidos.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            additionalProperties: true,
                            example: ['badge' => ['El campo badge debe ser una de las opciones permitidas.']]
                        ),
                        new OA\Property(property: 'status', type: 'integer', example: 422)
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Ocurrió un error al intentar actualizar el badge del usuario.'),
                        new OA\Property(property: 'data', type: 'null', example: null),
                        new OA\Property(property: 'errors', type: 'string', example: 'PDOException: SQL error...'),
                        new OA\Property(property: 'status', type: 'integer', example: 500)
                    ]
                )
            )
        ]
    )]
    public function updateBadge(Request $request) {}
}
