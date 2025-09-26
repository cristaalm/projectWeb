<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Categorías de Comercios', description: 'Endpoints para gestión de categorías de comercios / tipos de tienda')]
class TypeShopDocumentation
{
    #[OA\Get(
        path: "/api/typeShop/getAll",
        tags: ["Categorías de Comercios"],
        summary: "Obtener lista paginada de categorías",
        description: "Devuelve una lista paginada de categorías con filtros y ordenamiento opcionales.",
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
                description: "Búsqueda por nombre",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: "restaurante")
            ),
            new OA\Parameter(
                name: "key",
                description: "Campo por el cual ordenar. Valores permitidos: 'name'",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["name"], example: "name")
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
                description: "Lista de categorías obtenida correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Categorias obtenidas exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "data", type: "array", items: new OA\Items(properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Restaurante"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z"),
                            ])),
                            new OA\Property(property: "current_page", type: "integer", example: 1),
                            new OA\Property(property: "first_page_url", type: "string", example: "http://localhost/api/typeShop/getAll?page=1"),
                            new OA\Property(property: "from", type: "integer", example: 1),
                            new OA\Property(property: "last_page", type: "integer", example: 3),
                            new OA\Property(property: "last_page_url", type: "string", example: "http://localhost/api/typeShop/getAll?page=3"),
                            new OA\Property(property: "next_page_url", type: "string", nullable: true, example: "http://localhost/api/typeShop/getAll?page=2"),
                            new OA\Property(property: "path", type: "string", example: "http://localhost/api/typeShop/getAll"),
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
                response: 500,
                description: "Error inesperado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener las categorias."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function getAll(Request $request) {}

    #[OA\Get(
        path: "/api/typeShop/catalog",
        tags: ["Categorías de Comercios"],
        summary: "Obtener catálogo simple de categorías",
        description: "Devuelve un listado simple de categorías (solo id y nombre) para usar en selects o catálogos.",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Catálogo obtenido correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Categorias obtenidas exitosamente."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example: "Restaurante"),
                        ])),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
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
                        new OA\Property(property: "message", type: "string", example: "Error al obtener las categorias."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function catalog(Request $request) {}

    #[OA\Post(
        path: "/api/typeShop/create",
        tags: ["Categorías de Comercios"],
        summary: "Crear una nueva categoría",
        description: "Registra una nueva categoría de comercio en el sistema.",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Supermercado"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Categoría creada exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Categoria creado exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "id", type: "integer", example: 5),
                            new OA\Property(property: "name", type: "string", example: "Supermercado"),
                            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z"),
                            new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z"),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 201),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Nombre duplicado o error de validación",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Ya existe una categoria con el mismo nombre."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", oneOf: [
                            new OA\Schema(type: "null"),
                            new OA\Schema(type: "object", properties: [
                                new OA\Property(property: "name", type: "array", items: new OA\Items(type: "string", example: "The name field is required.")),
                            ]),
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
                        new OA\Property(property: "message", type: "string", example: "Error al crear la categoria."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function create(Request $request) {}

    #[OA\Delete(
        path: "/api/typeShop/delete/{id}",
        tags: ["Categorías de Comercios"],
        summary: "Eliminar una categoría por su ID",
        description: "Elimina permanentemente una categoría. Falla si la categoría tiene relaciones activas.",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID de la categoría a eliminar",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Categoría eliminada exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Categoria eliminada exitosamente."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Categoría no encontrada",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Categoria no encontrada."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 404),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "No se puede eliminar por restricción de integridad referencial",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "No se puede eliminar la categoria, por que ya esta relacionado con otros elementos."),
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
                        new OA\Property(property: "message", type: "string", example: "Error al eliminar la categoria."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function delete(Request $request, $id) {}
}
