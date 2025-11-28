<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Notificaciones', description: 'Gestión de tokens FCM y envío de notificaciones push')]
class NotificationsDocumentation
{
    #[OA\Post(
        path: "/api/notifications/registerToken",
        tags: ["Notificaciones"],
        summary: "Registrar/actualizar token FCM del usuario (Android)",
        description: "Guarda o actualiza el token de dispositivo FCM para el usuario autenticado. Solo se permite 'android' por ahora.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id", "token", "platform"],
                properties: [
                    new OA\Property(property: "user_id", type: "integer", example: 6, description: "ID del usuario autenticado"),
                    new OA\Property(property: "token", type: "string", example: "fcm_token", description: "Token FCM del dispositivo"),
                    new OA\Property(property: "platform", type: "string", enum: ["android"], example: "android", description: "Plataforma del dispositivo")
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Token registrado/actualizado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "registered"),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "code", type: "integer", example: 200)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 403,
                description: "No autorizado para registrar el token",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No autorizado para registrar este token."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "code", type: "integer", example: 403)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Datos inválidos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Datos inválidos para registrar token."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            example: ["user_id" => ["El campo user_id es obligatorio."], "token" => ["El campo token es obligatorio."], "platform" => ["El campo platform es obligatorio."]]
                        ),
                        new OA\Property(property: "code", type: "integer", example: 422)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error interno al registrar token",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al registrar token."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Detalle del error"),
                        new OA\Property(property: "code", type: "integer", example: 500)
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function docRegisterToken() {}

    #[OA\Post(
        path: "/api/notifications/send",
        tags: ["Notificaciones"],
        summary: "Enviar notificación push al usuario (Android)",
        description: "Envía una notificación FCM a todos los dispositivos Android registrados del usuario. Límite de título 100 caracteres, mensaje 500.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id", "title", "message"],
                properties: [
                    new OA\Property(property: "user_id", type: "integer", example: 6, description: "ID del usuario autenticado"),
                    new OA\Property(property: "title", type: "string", example: "Bienvenido", description: "Título de la notificación (<=100)"),
                    new OA\Property(property: "message", type: "string", example: "Tu cuenta fue actualizada", description: "Cuerpo de la notificación (<=500)")
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Intento de envío realizado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "sent"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "attempted", type: "integer", example: 1),
                                new OA\Property(
                                    property: "errors",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "token", type: "string", example: "fcm_token"),
                                            new OA\Property(property: "error", type: "string", example: "MismatchSenderId")
                                        ],
                                        type: "object"
                                    ),
                                    example: []
                                )
                            ]
                        ),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "code", type: "integer", example: 200)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Sin dispositivos registrados",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No hay dispositivos registrados para el usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Sin tokens"),
                        new OA\Property(property: "code", type: "integer", example: 404)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Datos inválidos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Datos inválidos para enviar notificación."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "object", example: ["user_id" => ["El campo user_id es obligatorio."], "title" => ["El campo title es obligatorio."], "message" => ["El campo message es obligatorio."]]),
                        new OA\Property(property: "code", type: "integer", example: 422)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error interno al enviar notificación",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al enviar notificación."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Detalle del error"),
                        new OA\Property(property: "code", type: "integer", example: 500)
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function docSend() {}

    #[OA\Delete(
        path: "/api/notifications/registerToken",
        tags: ["Notificaciones"],
        summary: "Eliminar token FCM del usuario",
        description: "Elimina un token específico o todos los tokens de una plataforma del usuario autenticado. Enviar solo 'token' o solo 'platform'.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                oneOf: [
                    new OA\Schema(
                        required: ["user_id", "token"],
                        properties: [
                            new OA\Property(property: "user_id", type: "integer", example: 6, description: "ID del usuario autenticado"),
                            new OA\Property(property: "token", type: "string", example: "fcm_token", description: "Token FCM a eliminar")
                        ],
                        type: "object"
                    ),
                    new OA\Schema(
                        required: ["user_id", "platform"],
                        properties: [
                            new OA\Property(property: "user_id", type: "integer", example: 6, description: "ID del usuario autenticado"),
                            new OA\Property(property: "platform", type: "string", enum: ["android"], example: "android", description: "Plataforma cuyos tokens se eliminarán")
                        ],
                        type: "object"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Token(s) eliminado(s)",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "deleted"),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "deleted", type: "integer", example: 1)
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "code", type: "integer", example: 200)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 404,
                description: "Token no encontrado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "token_not_found"),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "code", type: "integer", example: 404)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validación: proporcionar token o platform, no ambos",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Debe proporcionar token o platform."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "object", example: ["token" => ["Se requiere token o platform"]]),
                        new OA\Property(property: "code", type: "integer", example: 422)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 403,
                description: "No autorizado para eliminar tokens del usuario",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No autorizado para eliminar tokens de este usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "code", type: "integer", example: 403)
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error interno al eliminar token",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al eliminar token."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Detalle del error"),
                        new OA\Property(property: "code", type: "integer", example: 500)
                    ],
                    type: "object"
                )
            )
        ]
    )]
    public function docDeleteToken() {}
}
