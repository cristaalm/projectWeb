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
use App\Swagger\Schemas\ContainerSchema;

#[OA\Tag(name: 'Contenedores', description: 'Endpoints para gestión de contenedores')]
class ContainerDocumentation
{

    #[OA\Get(
        path: "/api/containers/catalog",
        tags: ["Contenedores"],
        summary: "Obtener catalogo de contenedores",
        security: [["bearerAuth" => []]],
        description: "Obtiene un listado de contenedores activos.",
        responses: [
            new OA\Response(
                response: 200,
                description: "Listado de contenedores obtenido exitosamente.",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Listado de contenedores obtenido exitosamente."),
                        new OA\Property(property: "data", type: "array", items: new OA\Items(
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Contenedor Ejemplo"),
                                new OA\Property(property: "location", type: "string", example: "Bogotá"),
                            ]
                        )),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al obtener el catalogo de contenedores",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener el catalogo de contenedores."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function catalog(Request $request) {}
    
    #[OA\Get(
        path: "/api/containers/getAll",
        tags: ["Contenedores"],
        summary: "Obtener lista paginada de contenedores",
        security: [["bearerAuth" => []]],
        description: "Devuelve una lista paginada de contenedores con filtros y ordenamiento opcionales.",
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
                description: "Búsqueda global en: nombre, serial_number, location",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: "empresa")
            ),
            new OA\Parameter(
                name: "key",
                description: "Campo por el cual ordenar. Valores permitidos: 'name', 'serial_number', 'location'",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["name", "serial_number", "location"], example: "name")
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
                description: "Lista de contenedores obtenida correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Contenedores obtenidos exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Container")),
                            new OA\Property(property: "last_page", type: "integer", example: 3),
                            new OA\Property(property: "total", type: "integer", example: 25),
                        ]),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al obtener los contenedores",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener los contenedores."),
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
        path: "/api/containers/create",
        tags: ["Contenedores"],
        summary: "Crear un nuevo contenedor",
        security: [["bearerAuth" => []]],
        description: "Registra un nuevo contenedor en el sistema con los datos proporcionados.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "serial_number", "location", "latitude", "longitude", "status"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Contenedor Ejemplo"),
                    new OA\Property(property: "serial_number", type: "string", example: "123456789"),
                    new OA\Property(property: "location", type: "string", example: "Bogotá"),
                    new OA\Property(property: "latitude", type: "string", example: "4.609711"),
                    new OA\Property(property: "longitude", type: "string", example: "-74.085772"),
                    new OA\Property(property: "status", type: "boolean", example: true, description: "true = Activo, false = Inactivo"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Contenedor creado exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Contenedor creado exitosamente."),
                        new OA\Property(property: "data", ref: "#/components/schemas/Container"),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 201),
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
                        new OA\Property(property: "message", type: "string", example: "Error al crear el contenedor."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "name", type: "array", items: new OA\Items(type: "string", example: "The name field is required.")),
                            new OA\Property(property: "serial_number", type: "array", items: new OA\Items(type: "string", example: "The serial number field is required.")),
                            new OA\Property(property: "location", type: "array", items: new OA\Items(type: "string", example: "The location field is required.")),
                            new OA\Property(property: "latitude", type: "array", items: new OA\Items(type: "string", example: "The latitude field is required.")),
                            new OA\Property(property: "longitude", type: "array", items: new OA\Items(type: "string", example: "The longitude field is required.")),
                        ]),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al crear el contenedor",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al crear el contenedor."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function create(Request $request) {}

    #[OA\Put(
        path: "/api/containers/update/{id}",
        tags: ["Contenedores"],
        summary: "Actualizar un contenedor existente",
        security: [["bearerAuth" => []]],
        description: "Actualiza completamente los datos de un contenedor por su ID. Todos los campos son requeridos.",
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID del contenedor a actualizar",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "contact_name", "contact_email", "phone", "address", "status"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Empresa Actualizada S.A."),
                    new OA\Property(property: "contact_name", type: "string", example: "Carlos Gómez"),
                    new OA\Property(property: "contact_email", type: "string", format: "email", example: "carlos@actualizado.com"),
                    new OA\Property(property: "phone", type: "string", example: "+57 310 9876543"),
                    new OA\Property(property: "address", type: "string", example: "Avenida Siempre Viva 742, Medellín, Colombia"),
                    new OA\Property(property: "type_shop_id", type: "integer", example: 1),
                    new OA\Property(property: "status", type: "boolean", example: false, description: "true = Activo, false = Inactivo"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Contenedor actualizado exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Contenedor actualizado exitosamente."),
                        new OA\Property(property: "data", ref: "#/components/schemas/Container"),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Contenedor no encontrado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar el contenedor."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Modelo no encontrado."),
                        new OA\Property(property: "status", type: "integer", example: 404),
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
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar el contenedor."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "name", type: "array", items: new OA\Items(type: "string", example: "The name field is required.")),
                            new OA\Property(property: "serial_number", type: "array", items: new OA\Items(type: "string", example: "The serial number field is required.")),
                            new OA\Property(property: "location", type: "array", items: new OA\Items(type: "string", example: "The location field is required.")),
                            new OA\Property(property: "latitude", type: "array", items: new OA\Items(type: "string", example: "The latitude field is required.")),
                            new OA\Property(property: "longitude", type: "array", items: new OA\Items(type: "string", example: "The longitude field is required.")),
                        ]),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al actualizar el contenedor",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar el contenedor."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function update(Request $request, $id) {}

    #[OA\Delete(
        path: "/api/containers/delete/{id}",
        tags: ["Contenedores"],
        summary: "Eliminar un contenedor por su ID",
        security: [["bearerAuth" => []]],
        description: "Elimina permanentemente un contenedor. Falla si el contenedor no existe o tiene relaciones activas (como recompensas asociadas).",
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID del contenedor a eliminar",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Contenedor eliminado exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Contenedor eliminado exitosamente."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Contenedor no encontrado",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Contenedor no encontrado."),
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
                        new OA\Property(property: "message", type: "string", example: "No se puede eliminar el contenedor, por que ya esta relacionado con otros elementos."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al eliminar el contenedor",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al eliminar el contenedor."),
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
