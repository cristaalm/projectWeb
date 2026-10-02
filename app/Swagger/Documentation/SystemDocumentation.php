<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'System', description: 'Diagnóstico del servidor: consulta de los logs sin acceso a la consola del hosting. Rutas bajo /system, protegidas por auth:sanctum + ensureUserIsActive + role:superadmin y limitadas a 60 peticiones por minuto. Los logs pueden traer datos sensibles (correos, consultas, trazas), por eso solo los ve el superadmin. Solo se pueden leer los archivos de una lista fija (App\Services\SystemLogService::sources()): el endpoint recibe una clave, nunca una ruta. Los logs de supervisor (scheduler, queue, apache, supervisor) existen en la imagen de producción; en el entorno local de Sail esos servicios escriben a la salida de Docker. Los archivos viven dentro del contenedor: se reinician en cada deploy.')]
class SystemDocumentation
{
    #[OA\Get(
        path: '/system/logs',
        tags: ['System'],
        summary: 'Listar los logs consultables y su estado',
        description: 'Devuelve cada log de la lista fija con su tamaño y fecha de última escritura en este servidor. `available: false` significa que el archivo todavía no existe o no se puede leer (por ejemplo, el log de errores de un proceso que nunca ha fallado).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logs disponibles.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'logs', type: 'array', items: new OA\Items(type: 'object', properties: [
                                    new OA\Property(property: 'source', type: 'string', description: 'Clave para GET /system/logs/{source}.', example: 'scheduler'),
                                    new OA\Property(property: 'label', type: 'string', example: 'Tareas programadas'),
                                    new OA\Property(property: 'available', type: 'boolean', example: true),
                                    new OA\Property(property: 'size_bytes', type: 'integer', nullable: true, example: 18234),
                                    new OA\Property(property: 'modified_at', type: 'string', format: 'date-time', nullable: true),
                                ])),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function index() {}

    #[OA\Get(
        path: '/system/logs/{source}',
        tags: ['System'],
        summary: 'Leer las últimas líneas de un log',
        description: 'Devuelve las últimas `lines` líneas del log, de la más antigua a la más reciente, sobre App\Services\SystemLogService::read(). El archivo se lee de atrás hacia adelante por bloques, sin cargarlo completo, y se recorren como máximo 2 MB desde el final (`scanned_bytes`). Con `search` solo se devuelven las líneas que contienen ese texto (sin distinguir mayúsculas), buscando hacia atrás dentro de ese mismo tope — útil en `scheduler`, donde casi todo es "No scheduled commands are ready to run": con search=Running quedan solo las ejecuciones reales. `reached_start: true` indica que se revisó el archivo completo y no hay nada más antiguo.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'source', in: 'path', required: true, description: 'Clave del log.', schema: new OA\Schema(type: 'string', enum: ['laravel', 'supervisor', 'scheduler', 'scheduler-error', 'queue', 'queue-error', 'apache', 'apache-error'])),
            new OA\Parameter(name: 'lines', in: 'query', description: 'Cuántas líneas devolver.', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 1000, default: 200)),
            new OA\Parameter(name: 'search', in: 'query', description: 'Texto que deben contener las líneas.', schema: new OA\Schema(type: 'string', maxLength: 100)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Líneas del log.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'source', type: 'string', example: 'scheduler'),
                                new OA\Property(property: 'label', type: 'string', example: 'Tareas programadas'),
                                new OA\Property(property: 'size_bytes', type: 'integer', example: 18234),
                                new OA\Property(property: 'modified_at', type: 'string', format: 'date-time'),
                                new OA\Property(property: 'search', type: 'string', nullable: true, example: 'Running'),
                                new OA\Property(property: 'reached_start', type: 'boolean', example: true),
                                new OA\Property(property: 'scanned_bytes', type: 'integer', example: 18234),
                                new OA\Property(property: 'count', type: 'integer', example: 2),
                                new OA\Property(property: 'lines', type: 'array', items: new OA\Items(type: 'string'), example: [
                                    "2026-10-03 00:00:00 Running ['artisan' inspire] .............. 179.06ms DONE",
                                    "2026-10-03 00:00:00 Running ['artisan' dashboard:aggregate-daily]  189.07ms DONE",
                                ]),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'La clave no está en la lista de logs, o ese log todavía no existe en este servidor.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: '`lines` fuera de rango o `search` demasiado largo.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 429, description: 'Más de 60 peticiones por minuto.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function show() {}
}
