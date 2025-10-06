<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Verificación de Identidad', description: 'Endpoints para gestión de documentos de verificación de identidad')]
class IdentityVerificationDocumentation
{
    #[OA\Post(
        path: "/api/users/uploadDocuments",
        tags: ["Verificación de Identidad"],
        summary: "Subir documentos de identidad (frente y reverso)",
        description: "Permite a un usuario subir los documentos de identidad (INE o equivalente) para iniciar el proceso de verificación. Solo usuarios con permisos pueden subir documentos para otros usuarios.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: "multipart/form-data",
                    schema: new OA\Schema(
                        required: ["user_id", "document_front", "document_back"],
                        properties: [
                            new OA\Property(
                                property: "user_id",
                                type: "integer",
                                example: 5,
                                description: "ID del usuario para el que se suben los documentos"
                            ),
                            new OA\Property(
                                property: "document_front",
                                type: "string",
                                format: "binary",
                                description: "Archivo de imagen del frente del documento (jpeg, png, jpg). Máx. 5MB."
                            ),
                            new OA\Property(
                                property: "document_back",
                                type: "string",
                                format: "binary",
                                description: "Archivo de imagen del reverso del documento (jpeg, png, jpg). Máx. 5MB."
                            ),
                        ]
                    )
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Documentos subidos exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Documentos subidos exitosamente."),
                        new OA\Property(property: "data", ref: "#/components/schemas/IdentityVerification"),
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
                description: "No autorizado para subir documentos",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No tienes permiso para subir documentos."),
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
                        new OA\Property(property: "message", type: "string", example: "Error al subir los documentos."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "user_id", type: "array", items: new OA\Items(type: "string", example: "The selected user id is invalid.")),
                            new OA\Property(property: "document_front", type: "array", items: new OA\Items(type: "string", example: "The document front must be an image.")),
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
                        new OA\Property(property: "message", type: "string", example: "Error al subir los documentos."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function uploadDocuments(Request $request) {}

    #[OA\Post(
        path: "/api/users/uploadSelfie",
        tags: ["Verificación de Identidad"],
        summary: "Subir selfie de verificación",
        description: "Permite a un usuario subir una selfie para completar el proceso de verificación de identidad. Solo usuarios con permisos pueden subir selfies para otros usuarios.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: "multipart/form-data",
                    schema: new OA\Schema(
                        required: ["user_id", "selfie"],
                        properties: [
                            new OA\Property(
                                property: "user_id",
                                type: "integer",
                                example: 5,
                                description: "ID del usuario para el que se sube la selfie"
                            ),
                            new OA\Property(
                                property: "selfie",
                                type: "string",
                                format: "binary",
                                description: "Archivo de imagen de la selfie (jpeg, png, jpg). Máx. 5MB."
                            ),
                        ]
                    )
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Selfie subido exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Selfie subido exitosamente."),
                        new OA\Property(property: "data", ref: "#/components/schemas/IdentityVerification"),
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
                description: "No autorizado para subir selfie",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No tienes permiso para subir selfie."),
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
                        new OA\Property(property: "message", type: "string", example: "Error al subir el selfie."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "user_id", type: "array", items: new OA\Items(type: "string", example: "The selected user id is invalid.")),
                            new OA\Property(property: "selfie", type: "array", items: new OA\Items(type: "string", example: "The selfie must be an image.")),
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
                        new OA\Property(property: "message", type: "string", example: "Error al subir el selfie."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function uploadSelfie(Request $request) {}

    #[OA\Get(
        path: "/api/users/documents/{type}/{userId}",
        tags: ["Verificación de Identidad"],
        summary: "Obtener documento de verificación",
        description: "Devuelve un documento de verificación (frente, reverso o selfie) de un usuario. Solo accesible para administradores y moderadores.",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "type",
                description: "Tipo de documento a obtener",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", enum: ["front", "back", "selfie"], example: "front")
            ),
            new OA\Parameter(
                name: "userId",
                description: "ID del usuario propietario del documento",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 5)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Documento obtenido correctamente",
                content: new OA\MediaType(
                    mediaType: "image/*",
                    schema: new OA\Schema(type: "string", format: "binary")
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
                description: "No autorizado para acceder a los documentos",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No tienes permiso para obtener los documentos."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 403),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Documento no encontrado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Documento no encontrado."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 404),
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
                        new OA\Property(property: "message", type: "string", example: "Error al obtener los documentos."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function getDocument(Request $request, string $type, int $userId) {}

    #[OA\Post(
        path: "/api/users/list-docs",
        tags: ["Verificación de Identidad"],
        summary: "Obtener estado de documentos de verificación",
        description: "Verifica qué documentos de verificación (frente, reverso, selfie) tiene subidos un usuario específico.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id"],
                properties: [
                    new OA\Property(
                        property: "user_id",
                        type: "integer",
                        example: 5,
                        description: "ID del usuario del que se quiere verificar los documentos"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Estado de documentos obtenido correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Lista de documentos obtenida exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "front", type: "boolean", example: true, description: "Indica si el usuario ha subido el frente del documento"),
                            new OA\Property(property: "back", type: "boolean", example: false, description: "Indica si el usuario ha subido el reverso del documento"),
                            new OA\Property(property: "selfie", type: "boolean", example: true, description: "Indica si el usuario ha subido la selfie"),
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
                response: 422,
                description: "Error de validación",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener la lista de documentos."),
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
                        new OA\Property(property: "message", type: "string", example: "Error al obtener la lista de documentos."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function getListDocs(Request $request) {}

    #[OA\Post(
        path: "/api/users/verification-user",
        tags: ["Verificación de Identidad"],
        summary: "Aprobar o rechazar la verificación de identidad de un usuario",
        description: "Permite a un administrador o moderador aprobar o rechazar la verificación de identidad de un usuario. Si se rechaza, se requiere una justificación.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["user_id", "status"],
                properties: [
                    new OA\Property(
                        property: "user_id",
                        type: "integer",
                        example: 5,
                        description: "ID del usuario cuya verificación se desea aprobar o rechazar"
                    ),
                    new OA\Property(
                        property: "status",
                        type: "integer",
                        example: 2,
                        enum: [1, 2],
                        description: "1 = Aprobado, 2 = Rechazado"
                    ),
                    new OA\Property(
                        property: "justification",
                        type: "string",
                        example: "Documento ilegible o no coincide con la selfie.",
                        description: "Justificación del rechazo (requerida si status = 2)",
                        nullable: true
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Verificación actualizada exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Usuario verificado exitosamente."),
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
                        new OA\Property(property: "message", type: "string", example: "Justificación es requerida para rechazar la verificación."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
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
                        new OA\Property(property: "message", type: "string", example: "Error al verificar el usuario."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function verificationUser(Request $request) {}
}
