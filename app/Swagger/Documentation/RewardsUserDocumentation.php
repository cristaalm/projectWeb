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
                    new OA\Property(property: "quantity", type: "integer", example: 1, description: "Cantidad de recompensas a reclamar"),
                    new OA\Property(property: "token", type: "string", nullable: true, example: "eJ9Y_realFCMToken", description: "Token FCM del cliente (opcional; se registra antes de notificar)"),
                    new OA\Property(property: "platform", type: "string", nullable: true, enum: ["android"], example: "android", description: "Plataforma del dispositivo del cliente (opcional)")
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
                            type: "object",
                            properties: [
                                new OA\Property(property: "reward", ref: "#/components/schemas/RewardUser"),
                                new OA\Property(
                                    property: "notifications",
                                    type: "object",
                                    properties: [
                                        new OA\Property(
                                            property: "client",
                                            type: "object",
                                            properties: [
                                                new OA\Property(property: "attempted", type: "integer", example: 1),
                                                new OA\Property(
                                                    property: "payload",
                                                    type: "object",
                                                    properties: [
                                                        new OA\Property(property: "title", type: "string", example: "Compra finalizada"),
                                                        new OA\Property(property: "body", type: "string", example: "Has canjeado 1x Auriculares Gamer RGB por 1500 puntos."),
                                                        new OA\Property(property: "type", type: "string", example: "reward_claim"),
                                                        new OA\Property(property: "reward_id", type: "string", example: "3"),
                                                    ]
                                                ),
                                                new OA\Property(property: "tokens", type: "array", items: new OA\Items(type: "string")),
                                                new OA\Property(
                                                    property: "sent",
                                                    type: "array",
                                                    items: new OA\Items(
                                                        type: "object",
                                                        properties: [
                                                            new OA\Property(property: "token", type: "string", example: "eJ9Y_realFCMToken"),
                                                            new OA\Property(property: "message_id", type: "string", example: "projects/my-project/messages/0:1723532523666667%cccccccccccccccc"),
                                                        ]
                                                    )
                                                ),
                                                new OA\Property(
                                                    property: "errors",
                                                    type: "array",
                                                    items: new OA\Items(
                                                        type: "object",
                                                        properties: [
                                                            new OA\Property(property: "token", type: "string", example: "invalid_token"),
                                                            new OA\Property(property: "error", type: "string", example: "The registration token is not a valid FCM registration token"),
                                                        ]
                                                    )
                                                ),
                                            ]
                                        ),
                                        new OA\Property(
                                            property: "merchant",
                                            type: "object",
                                            properties: [
                                                new OA\Property(property: "attempted", type: "integer", example: 0),
                                                new OA\Property(
                                                    property: "payload",
                                                    type: "object",
                                                    properties: [
                                                        new OA\Property(property: "title", type: "string", example: "Venta confirmada"),
                                                        new OA\Property(property: "body", type: "string", example: "Se procesó el canje de 1x Auriculares Gamer RGB para el usuario #5."),
                                                        new OA\Property(property: "type", type: "string", example: "reward_claim_merchant"),
                                                        new OA\Property(property: "reward_id", type: "string", example: "3"),
                                                    ]
                                                ),
                                                new OA\Property(property: "tokens", type: "array", items: new OA\Items(type: "string")),
                                                new OA\Property(
                                                    property: "sent",
                                                    type: "array",
                                                    items: new OA\Items(
                                                        type: "object",
                                                        properties: [
                                                            new OA\Property(property: "token", type: "string", example: "eJ9Y_realFCMTokenMerchant"),
                                                            new OA\Property(property: "message_id", type: "string", example: "projects/my-project/messages/0:1723532523..."),
                                                        ]
                                                    )
                                                ),
                                                new OA\Property(
                                                    property: "errors",
                                                    type: "array",
                                                    items: new OA\Items(
                                                        type: "object",
                                                        properties: [
                                                            new OA\Property(property: "token", type: "string", example: "invalid_token"),
                                                            new OA\Property(property: "error", type: "string", example: "The registration token is not a valid FCM registration token"),
                                                        ]
                                                    )
                                                ),
                                            ]
                                        ),
                                    ]
                                ),
                            ]
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
