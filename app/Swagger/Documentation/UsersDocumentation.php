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
                        new OA\Property(property: "data", ref: "#/components/schemas/User"),
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
}
