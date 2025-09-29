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
use App\Swagger\Schemas\AllianceSchema;

#[OA\Tag(name: 'Historial', description: 'Endpoints para gestión del historial de acciones del usuario')]
class HistoryDocumentation
{
    #[OA\Get(
        path: "/api/history/getAll",
        tags: ["Historial"],
        summary: "Obtener lista paginada del historial de acciones del usuario",
        description: "Devuelve un listado paginado del historial de acciones (como canjes o escaneos) con información del comercio y tipo de material asociado.",
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
                name: "key",
                description: "Campo por el cual ordenar. Valores permitidos: 'material_type.name', 'created_at'",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["material_type.name", "created_at"], example: "material_type.name")
            ),
            new OA\Parameter(
                name: "order",
                description: "Orden de los resultados. Valores permitidos: 'asc', 'desc'",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["asc", "desc"], example: "asc")
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
                description: "Historial obtenido correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Historial obtenido exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "data", type: "array", items: new OA\Items(properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "user_id", type: "integer", example: 5),
                                new OA\Property(property: "type_history", type: "integer", example: 1, description: "1 = Escaneo, 2 = Canje, etc."),
                                new OA\Property(property: "material_type_id", type: "integer", example: 1),
                                new OA\Property(property: "points", type: "integer", example: 10),
                                new OA\Property(property: "alliance_id", type: "integer", example: 3),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z"),
                                new OA\Property(property: "alliance", properties: [
                                    new OA\Property(property: "id", type: "integer", example: 3),
                                    new OA\Property(property: "name", type: "string", example: "Supermercado Éxito"),
                                    new OA\Property(property: "contact_name", type: "string", example: "Carlos López"),
                                    new OA\Property(property: "contact_email", type: "string", example: "carlos@exito.com"),
                                    new OA\Property(property: "phone", type: "string", example: "+57 300 1234567"),
                                    new OA\Property(property: "address", type: "string", example: "Calle 100 #20-30, Bogotá"),
                                    new OA\Property(property: "logo", type: "boolean", example: true),
                                    new OA\Property(property: "type_shop_id", type: "integer", example: 2),
                                    new OA\Property(property: "ext", type: "string", example: "png"),
                                    new OA\Property(property: "status", type: "integer", example: 1),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-03-01T08:00:00.000000Z"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-03-01T08:00:00.000000Z"),
                                ]),
                                new OA\Property(property: "materialType", properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Plástico PET"),
                                    new OA\Property(property: "slug", type: "string", example: "plastico-pet"),
                                    new OA\Property(property: "points", type: "integer", example: 10),
                                    new OA\Property(property: "is_active", type: "boolean", example: true),
                                    new OA\Property(property: "description", type: "string", example: "Botellas y envases de plástico reciclable."),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-01-01T00:00:00.000000Z"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-01-01T00:00:00.000000Z"),
                                ]),
                            ])),
                            new OA\Property(property: "current_page", type: "integer", example: 1),
                            new OA\Property(property: "first_page_url", type: "string", example: "http://localhost/api/history/getAll?page=1"),
                            new OA\Property(property: "from", type: "integer", example: 1),
                            new OA\Property(property: "last_page", type: "integer", example: 5),
                            new OA\Property(property: "last_page_url", type: "string", example: "http://localhost/api/history/getAll?page=5"),
                            new OA\Property(property: "next_page_url", type: "string", nullable: true, example: "http://localhost/api/history/getAll?page=2"),
                            new OA\Property(property: "path", type: "string", example: "http://localhost/api/history/getAll"),
                            new OA\Property(property: "per_page", type: "integer", example: 10),
                            new OA\Property(property: "prev_page_url", type: "string", nullable: true, example: null),
                            new OA\Property(property: "to", type: "integer", example: 10),
                            new OA\Property(property: "total", type: "integer", example: 48),
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
                description: "Error inesperado al obtener el historial",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener el historial."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function getAll(Request $request) {}
}
