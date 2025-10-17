<?php

namespace App\Swagger\Documentation;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Rewards", description: "Gestión de recompensas ofrecidas por alianzas")]
class RewardsUserDocumentation
{
    #[OA\Post(
        path: "/api/reward/claim",
        operationId: "claimReward",
        description: "Permite a un usuario reclamar una recompensa si cumple con los requisitos (puntos, verificación, stock, vigencia)",
        tags: ["Rewards"],
        security: [["bearerAuth" => []]], // Asegúrate de que coincida con tu SecurityScheme
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id", "reward_id", "quantity"],
                properties: [
                    new OA\Property(property: "user_id", type: "integer", example: 5, description: "ID del usuario que reclama la recompensa"),
                    new OA\Property(property: "reward_id", type: "integer", example: 3, description: "ID de la recompensa a reclamar"),
                    new OA\Property(property: "quantity", type: "integer", example: 1, description: "Cantidad de recompensas a reclamar")
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Recompensa reclamada exitosamente",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Recompensa reclamada exitosamente."),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "user_id", type: "integer", example: 5),
                                new OA\Property(property: "reward_id", type: "integer", example: 3),
                                new OA\Property(property: "quantity", type: "integer", example: 1),
                                new OA\Property(property: "redeemed_at", type: "string", format: "date-time", example: "2025-04-05T14:30:00.000000Z")
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
                response: 400,
                description: "Condiciones no cumplidas (usuario inactivo, no verificado, sin puntos, recompensa expirada o agotada)",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No tiene puntos suficientes."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(property: "error", type: "null"),
                        new OA\Property(property: "status", type: "integer", example: 400)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Datos de validación inválidos (IDs inexistentes)",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Datos inválidos."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(
                            property: "error",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "user_id",
                                    type: "array",
                                    items: new OA\Items(type: "string", example: "The selected user id is invalid.")
                                )
                            ],
                            example: [
                                "user_id" => ["The selected user id is invalid."]
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
                        new OA\Property(property: "message", type: "string", example: "Error interno al reclamar la recompensa."),
                        new OA\Property(property: "data", type: "null"),
                        new OA\Property(property: "error", type: "string", example: "Error al procesar la transacción."),
                        new OA\Property(property: "status", type: "integer", example: 500)
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function claim(Request $request) {}
}
