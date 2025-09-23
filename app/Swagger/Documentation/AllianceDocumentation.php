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

#[OA\Tag(name: 'Alianzas', description: 'Endpoints para gestión de alianzas / comercios')]
class AllianceDocumentation
{
    #[OA\Get(
        path: "/api/alianzas/getAll",
        tags: ["Alianzas"],
        summary: "Obtener lista paginada de alianzas",
        security: [
            new OA\SecurityScheme(
                securityScheme: "bearerAuth",
                type: "http",
                scheme: "bearer",
                bearerFormat: "JWT"
            )
        ],
        description: "Devuelve una lista paginada de alianzas con filtros y ordenamiento opcionales.",
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
                description: "Búsqueda global en: nombre, contacto, email, teléfono o dirección",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", example: "empresa")
            ),
            new OA\Parameter(
                name: "key",
                description: "Campo por el cual ordenar. Valores permitidos: 'name', 'status'",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["name", "status"], example: "name")
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
                description: "Lista de alianzas obtenida correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Alianzas obtenidas exitosamente."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Alliance")),
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
                description: "Error inesperado al obtener las alianzas",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al obtener las alianzas."),
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
        path: "/api/alianzas/create",
        tags: ["Alianzas"],
        summary: "Crear una nueva alianza",
        security: [
            new OA\SecurityScheme(
                securityScheme: "bearerAuth",
                type: "http",
                scheme: "bearer",
                bearerFormat: "JWT"
            )
        ],
        description: "Registra una nueva alianza en el sistema con los datos proporcionados.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "contact_name", "contact_email", "phone", "address", "status"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Empresa Ejemplo S.A."),
                    new OA\Property(property: "contact_name", type: "string", example: "Juan Pérez"),
                    new OA\Property(property: "contact_email", type: "string", format: "email", example: "juan@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "+57 300 1234567"),
                    new OA\Property(property: "address", type: "string", example: "Calle 123 #45-67, Bogotá, Colombia"),
                    new OA\Property(property: "status", type: "boolean", example: true, description: "true = Activo, false = Inactivo"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Alianza creada exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Alianza creada exitosamente."),
                        new OA\Property(property: "data", ref: "#/components/schemas/Alliance"),
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
                        new OA\Property(property: "message", type: "string", example: "Error al crear la alianza."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "name", type: "array", items: new OA\Items(type: "string", example: "The name field is required.")),
                            new OA\Property(property: "contact_email", type: "array", items: new OA\Items(type: "string", example: "The contact email must be a valid email address.")),
                        ]),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al crear la alianza",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al crear la alianza."),
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
        path: "/api/alianzas/update/{id}",
        tags: ["Alianzas"],
        summary: "Actualizar una alianza existente",
        security: [
            new OA\SecurityScheme(
                securityScheme: "bearerAuth",
                type: "http",
                scheme: "bearer",
                bearerFormat: "JWT"
            )
        ],
        description: "Actualiza completamente los datos de una alianza por su ID. Todos los campos son requeridos.",
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID de la alianza a actualizar",
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
                    new OA\Property(property: "status", type: "boolean", example: false, description: "true = Activo, false = Inactivo"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Alianza actualizada exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Alianza actualizada exitosamente."),
                        new OA\Property(property: "data", ref: "#/components/schemas/Alliance"),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Alianza no encontrada",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar la alianza."),
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
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar la alianza."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "name", type: "array", items: new OA\Items(type: "string", example: "The name field is required.")),
                            new OA\Property(property: "phone", type: "array", items: new OA\Items(type: "string", example: "The phone field is required.")),
                        ]),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al actualizar la alianza",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar la alianza."),
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
        path: "/api/alianzas/delete/{id}",
        tags: ["Alianzas"],
        summary: "Eliminar una alianza por su ID",
        security: [
            new OA\SecurityScheme(
                securityScheme: "bearerAuth",
                type: "http",
                scheme: "bearer",
                bearerFormat: "JWT"
            )
        ],
        description: "Elimina permanentemente una alianza. Falla si la alianza no existe o tiene relaciones activas (como recompensas asociadas).",
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID de la alianza a eliminar",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Alianza eliminada exitosamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Comercio eliminado exitosamente."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Alianza no encontrada",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Alianza no encontrada."),
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
                        new OA\Property(property: "message", type: "string", example: "No se puede eliminar el comercio, por que ya esta relacionado con otros elementos."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 422),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Error inesperado al eliminar la alianza",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al eliminar el comercio."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function delete(Request $request, $id) {}

    #[OA\Post(
        path: "/api/alianzas/logo/{id}",
        tags: ["Alianzas"],
        summary: "Actualizar o eliminar el logo de una alianza",
        description: "Sube un nuevo logo para la alianza. Si no se envía archivo, se elimina el logo actual.",
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "ID de la alianza",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: "logo",
                            type: "string",
                            format: "binary",
                            description: "Archivo de imagen (jpeg, png, jpg, gif, svg, webp). Máx. 2MB."
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Logo actualizado o eliminado correctamente",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Logo actualizado exitosamente."),
                        new OA\Property(property: "data", ref: "#/components/schemas/Alliance"),
                        new OA\Property(property: "errors", type: "null", example: null),
                        new OA\Property(property: "status", type: "integer", example: 200),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Alianza no encontrada",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar el logo."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Modelo no encontrado."),
                        new OA\Property(property: "status", type: "integer", example: 404),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Error de validación (formato o tamaño de archivo)",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar el logo."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", properties: [
                            new OA\Property(property: "logo", type: "array", items: new OA\Items(type: "string", example: "The logo must be a file of type: jpeg, png, jpg, gif, svg, webp.")),
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
                        new OA\Property(property: "message", type: "string", example: "Error al actualizar el logo."),
                        new OA\Property(property: "data", type: "null", example: null),
                        new OA\Property(property: "errors", type: "string", example: "Error interno del servidor."),
                        new OA\Property(property: "status", type: "integer", example: 500),
                    ]
                )
            ),
        ]
    )]
    public function updateLogo(Request $request, $id) {}
}
