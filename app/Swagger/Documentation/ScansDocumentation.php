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
use App\Swagger\Schemas\ScansSchema;
use App\Swagger\Schemas\IAResponseSchema;

#[OA\Tag(name: 'Escaneos', description: 'Endpoints para gestión de escaneos')]
class ScansDocumentation
{

    #[OA\Post(
        path: "/api/scans/scan",
        tags: ["Escaneos"],
        summary: "Analiza una imagen mediante un modelo de IA, registra y retorna el resultado.",
        description: "Analiza una imagen mediante un modelo de IA, registra y retorna el resultado.",
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: "multipart/form-data",
                    schema: new OA\Schema(
                        required: ["image", "container_id", "user_id"],
                        properties: [
                            new OA\Property(
                                property: "image",
                                description: "Archivo de imagen a escanear",
                                type: "string",
                                format: "binary", // ✅ Indica que es un archivo binario
                            ),
                            new OA\Property(
                                property: "container_id",
                                description: "ID del contenedor donde se realiza el escaneo",
                                type: "integer",
                                example: 1
                            ),
                            new OA\Property(
                                property: "user_id",
                                description: "ID del usuario que realiza el escaneo",
                                type: "integer",
                                example: 1
                            ),
                        ]
                    )
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Escaneo exitoso.",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Escaneo exitoso."),
                        
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación o procesamiento",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al escanear."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", oneOf: [
                            new OA\Schema(type: "string", example: "El usuario no está activo."),
                            new OA\Schema(type: "string", example: "La IA no pudo procesar la imagen correctamente."),
                            new OA\Schema(type: "string", example: "El tipo de material no es válido."),
                        ]),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error al escanear",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al escanear."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "array", items: new OA\Items(type: "string")),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function scan(Request $request) {}

    #[OA\Get(
        path: "/api/scans/total-type-scans",
        tags: ["Escaneos"],
        summary: "Obtener el total de escaneos por tipo de material para el usuario autenticado",
        description: "Devuelve el conteo total de escaneos exitosos agrupados por tipo de material (plástico y aluminio) del usuario actual.",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Totales obtenidos correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Total de escaneos de cada tipo"),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "plastic", type: "integer", example: 24, description: "Número total de escaneos de plástico (material_type_id = 1)"),
                            new OA\Property(property: "aluminum", type: "integer", example: 18, description: "Número total de escaneos de aluminio (material_type_id = 2)"),
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
                description: "Error inesperado al obtener los totales",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener el total de escaneos de cada tipo"),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function totalTypeScans(Request $request) {}
}
