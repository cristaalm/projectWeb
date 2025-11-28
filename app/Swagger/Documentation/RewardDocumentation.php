<?php

namespace App\Swagger\Documentation;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Swagger\Schemas\RewardSchema;

#[OA\Tag(name: "Rewards", description: "Gestión de recompensas ofrecidas por alianzas")]
class RewardDocumentation
{
    #[OA\Get(
        path: "/api/reward/getAll",
        operationId: "getAllRewards",
        description: "Obtiene una lista paginada de recompensas con filtros y ordenamiento",
        tags: ["Rewards"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "per_page", in: "query", schema: new OA\Schema(type: "integer", default: 10, minimum: 1, maximum: 100)),
            new OA\Parameter(name: "query", in: "query", schema: new OA\Schema(type: "string"), description: "Búsqueda en nombre, descripción, código o nombre de alianza"),
            new OA\Parameter(name: "key", in: "query", schema: new OA\Schema(type: "string", enum: ["name", "description", "code", "status", "alliance.name"])),
            new OA\Parameter(name: "order", in: "query", schema: new OA\Schema(type: "string", enum: ["asc", "desc"], default: "asc")),
            new OA\Parameter(name: "status", in: "query", schema: new OA\Schema(type: "integer", enum: [0, 1]), description: "1 = activo, 0 = inactivo"),
            new OA\Parameter(name: "alliance_id", in: "query", schema: new OA\Schema(type: "integer"), description: "ID de la alianza, en caso de ser nulo, se devolverán todas las recompensas sin filtro por alianza"),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de recompensas obtenida exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Recompensas obtenidas exitosamente."),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Reward")),
                                new OA\Property(property: "first_page_url", type: "string"),
                                new OA\Property(property: "from", type: "integer"),
                                new OA\Property(property: "last_page", type: "integer"),
                                new OA\Property(property: "last_page_url", type: "string"),
                                new OA\Property(property: "next_page_url", type: "string", nullable: true),
                                new OA\Property(property: "path", type: "string"),
                                new OA\Property(property: "per_page", type: "integer"),
                                new OA\Property(property: "prev_page_url", type: "string", nullable: true),
                                new OA\Property(property: "to", type: "integer"),
                                new OA\Property(property: "total", type: "integer")
                            ],
                            type: "object"
                        ),
                        new OA\Property(property: "error", type: "null"),
                        new OA\Property(property: "status", type: "integer", example: 200)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener las recompensas."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(property: "error", type: "string", example: "Detalles del error interno..."),
                        new OA\Property(property: "status", type: "integer", example: 500)
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function getAll(Request $request){}

    #[OA\Post(
        path: "/api/reward/create",
        operationId: "createReward",
        description: "Crea una nueva recompensa para una alianza",
        tags: ["Rewards"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["alliance_id", "name", "description", "points_required", "is_active"],
                    properties: [
                        new OA\Property(property: "alliance_id", type: "integer"),
                        new OA\Property(property: "name", type: "string", maxLength: 255),
                        new OA\Property(property: "description", type: "string", maxLength: 255),
                        new OA\Property(property: "points_required", type: "integer"),
                        new OA\Property(property: "stock", type: "integer", nullable: true),
                        new OA\Property(property: "is_active", type: "boolean"),
                        new OA\Property(property: "expires_at", type: "string", format: "date", nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Recompensa creada exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Recompensa creada exitosamente."),
                        new OA\Property(property: "data", ref: "#/components/schemas/Reward"),
                        new OA\Property(property: "error", type: "null"),
                        new OA\Property(property: "status", type: "integer", example: 201)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Datos de validación inválidos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Datos inválidos."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(
                            property: "error",
                            type: "object",
                            example: [
                                "name" => ["El campo nombre es obligatorio."]
                            ]
                        ),
                        new OA\Property(property: "status", type: "integer", example: 422)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error interno al crear la recompensa."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(property: "error", type: "string", example: "Error al subir la imagen."),
                        new OA\Property(property: "status", type: "integer", example: 500)
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function create(Request $request){}

    #[OA\Put(
        path: "/api/reward/update/{id}",
        operationId: "updateReward",
        description: "Actualiza una recompensa existente",
        tags: ["Rewards"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "alliance_id", type: "integer"),
                        new OA\Property(property: "name", type: "string", maxLength: 255),
                        new OA\Property(property: "description", type: "string", maxLength: 255),
                        new OA\Property(property: "points_required", type: "integer"),
                        new OA\Property(property: "stock", type: "integer", nullable: true),
                        new OA\Property(property: "is_active", type: "boolean"),
                        new OA\Property(property: "expires_at", type: "string", format: "date", nullable: true),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Recompensa actualizada exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Recompensa actualizada exitosamente."),
                        new OA\Property(property: "data", ref: "#/components/schemas/Reward"),
                        new OA\Property(property: "error", type: "null"),
                        new OA\Property(property: "status", type: "integer", example: 200)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Recompensa no encontrada",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Recompensa no encontrada."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(property: "error", type: "null"),
                        new OA\Property(property: "status", type: "integer", example: 404)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Datos de validación inválidos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Datos inválidos."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(
                            property: "error",
                            type: "object",
                            example: [
                                "reward_id" => ["El reward id seleccionado es inválido."]
                            ]
                        ),
                        new OA\Property(property: "status", type: "integer", example: 422)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error interno al actualizar la recompensa."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(property: "error", type: "string", example: "Error al actualizar la imagen."),
                        new OA\Property(property: "status", type: "integer", example: 500)
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function update(Request $request, $id){}

    #[OA\Delete(
        path: "/api/reward/delete/{id}",
        operationId: "deleteReward",
        description: "Elimina una recompensa de forma logica, para evitar problemas con el historial.",
        tags: ["Rewards"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Recompensa eliminada exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Recompensa eliminada exitosamente."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(property: "error", type: "null"),
                        new OA\Property(property: "status", type: "integer", example: 200)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Recompensa no encontrada",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Recompensa no encontrada."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(property: "error", type: "null"),
                        new OA\Property(property: "status", type: "integer", example: 404)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error interno del servidor",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al eliminar la recompensa."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(property: "error", type: "string", example: "No se pudo eliminar el archivo de imagen."),
                        new OA\Property(property: "status", type: "integer", example: 500)
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function deleteclear(Request $request, $id){}
}
