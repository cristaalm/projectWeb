<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Dashboard', description: 'Panel principal del dashboard web. Cada rol con acceso tiene su propio endpoint bajo /dashboard (auth:sanctum + ensureUserIsActive + role), porque los datos que ve cada uno son distintos; por ahora solo existe el de superadmin. Las cifras por periodo salen de las tablas de resumen diario (daily_scan_stats, daily_user_stats), que llena cada hora `php artisan dashboard:aggregate-daily` con los días ya cerrados; el día en curso, y cualquier día cerrado que el job aún no haya procesado, se calcula en vivo sobre las tablas reales con las mismas consultas (App\Repositories\DashboardRepository), así que el panel nunca va un día atrás.')]
class DashboardDocumentation
{
    #[OA\Get(
        path: '/dashboard/superadmin',
        tags: ['Dashboard'],
        summary: 'Panel principal del superadministrador',
        description: 'Indicadores de uso del sistema para un periodo, sobre App\Services\DashboardService::superadminOverview(). Los días se cortan en la zona horaria de la app. `previous` en cada indicador es el valor de la ventana inmediata anterior con el mismo número de días (period.previous_from a period.previous_to). `timeline` trae un punto por día, o por mes en los periodos anuales, con ceros donde no hubo actividad. `attention`, `kpis.containers`, `kpis.active_users.total` y `recent_scans` reflejan el estado actual y no dependen del periodo, salvo `attention.high_failure_containers` (contenedores con al menos 10 escaneos en el periodo y 50 % o más fallidos).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(
                name: 'period',
                in: 'query',
                description: 'Periodo a consultar. Todos terminan hoy, salvo last_month (mes calendario anterior completo). this_year y last_12_months agrupan la serie por mes.',
                schema: new OA\Schema(type: 'string', enum: ['last_7_days', 'last_30_days', 'this_month', 'last_month', 'this_year', 'last_12_months'], default: 'last_30_days')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Datos del panel.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'period',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'key', type: 'string', example: 'last_30_days'),
                                        new OA\Property(property: 'from', type: 'string', format: 'date', example: '2026-09-03'),
                                        new OA\Property(property: 'to', type: 'string', format: 'date', example: '2026-10-02'),
                                        new OA\Property(property: 'previous_from', type: 'string', format: 'date', example: '2026-08-04'),
                                        new OA\Property(property: 'previous_to', type: 'string', format: 'date', example: '2026-09-02'),
                                        new OA\Property(property: 'granularity', type: 'string', enum: ['day', 'month'], example: 'day'),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'kpis',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'valid_scans', type: 'object', description: 'Reciclajes válidos.', properties: [
                                            new OA\Property(property: 'value', type: 'integer', example: 128),
                                            new OA\Property(property: 'previous', type: 'integer', example: 97),
                                        ]),
                                        new OA\Property(property: 'points_awarded', type: 'object', description: 'Puntos otorgados por reciclaje más insignias reclamadas. No incluye ajustes de administrador.', properties: [
                                            new OA\Property(property: 'value', type: 'integer', example: 2450),
                                            new OA\Property(property: 'previous', type: 'integer', example: 1810),
                                        ]),
                                        new OA\Property(property: 'active_users', type: 'object', description: 'Usuarios con al menos un reciclaje válido en el periodo; `total` son las cuentas registradas que no están dadas de baja.', properties: [
                                            new OA\Property(property: 'value', type: 'integer', example: 34),
                                            new OA\Property(property: 'previous', type: 'integer', example: 29),
                                            new OA\Property(property: 'total', type: 'integer', example: 210),
                                        ]),
                                        new OA\Property(property: 'containers', type: 'object', properties: [
                                            new OA\Property(property: 'active', type: 'integer', example: 6),
                                            new OA\Property(property: 'total', type: 'integer', example: 7),
                                        ]),
                                    ]
                                ),
                                new OA\Property(property: 'timeline', type: 'array', items: new OA\Items(type: 'object', properties: [
                                    new OA\Property(property: 'bucket', type: 'string', description: 'Día (Y-m-d) o mes (Y-m), según period.granularity.', example: '2026-10-01'),
                                    new OA\Property(property: 'valid', type: 'integer', example: 6),
                                    new OA\Property(property: 'failed', type: 'integer', example: 4),
                                ])),
                                new OA\Property(property: 'materials', type: 'array', description: 'Todos los materiales del catálogo, con su actividad en el periodo.', items: new OA\Items(type: 'object', properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 5),
                                    new OA\Property(property: 'name', type: 'string', example: 'Aluminio'),
                                    new OA\Property(property: 'slug', type: 'string', example: 'aluminum'),
                                    new OA\Property(property: 'valid_scans', type: 'integer', example: 3),
                                    new OA\Property(property: 'failed_scans', type: 'integer', example: 0),
                                    new OA\Property(property: 'points', type: 'integer', example: 105),
                                ])),
                                new OA\Property(
                                    property: 'attention',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'pending_rewards', type: 'integer', description: 'Recompensas pendientes de revisión.', example: 2),
                                        new OA\Property(property: 'silent_container_days', type: 'integer', description: 'Días sin actividad a partir de los cuales un contenedor activo aparece en silent_containers.', example: 7),
                                        new OA\Property(property: 'silent_containers', type: 'array', items: new OA\Items(type: 'object', properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 4),
                                            new OA\Property(property: 'name', type: 'string', example: 'Contenedor Plaza'),
                                            new OA\Property(property: 'serial_number', type: 'string', example: 'SN-0004'),
                                            new OA\Property(property: 'last_scan_at', type: 'string', format: 'date-time', nullable: true, description: 'null si nunca ha registrado un escaneo.'),
                                        ])),
                                        new OA\Property(property: 'high_failure_containers', type: 'array', items: new OA\Items(type: 'object', properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 2),
                                            new OA\Property(property: 'name', type: 'string', example: 'Contenedor Parque'),
                                            new OA\Property(property: 'serial_number', type: 'string', example: 'SN-0002'),
                                            new OA\Property(property: 'valid_scans', type: 'integer', example: 2),
                                            new OA\Property(property: 'failed_scans', type: 'integer', example: 14),
                                        ])),
                                    ]
                                ),
                                new OA\Property(property: 'top_containers', type: 'array', description: 'Hasta 5 contenedores con más reciclajes válidos en el periodo.', items: new OA\Items(type: 'object', properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 2),
                                    new OA\Property(property: 'name', type: 'string', example: 'Contenedor Parque'),
                                    new OA\Property(property: 'serial_number', type: 'string', example: 'SN-0002'),
                                    new OA\Property(property: 'valid_scans', type: 'integer', example: 41),
                                    new OA\Property(property: 'points', type: 'integer', example: 835),
                                ])),
                                new OA\Property(property: 'top_users', type: 'array', description: 'Hasta 5 usuarios con más puntos ganados en el periodo (reciclaje + insignias).', items: new OA\Items(type: 'object', properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 39),
                                    new OA\Property(property: 'name', type: 'string', example: 'Ana'),
                                    new OA\Property(property: 'last_name', type: 'string', example: 'López'),
                                    new OA\Property(property: 'avatar', type: 'string', nullable: true),
                                    new OA\Property(property: 'valid_scans', type: 'integer', example: 12),
                                    new OA\Property(property: 'points', type: 'integer', example: 320),
                                ])),
                                new OA\Property(property: 'recent_scans', type: 'array', description: 'Los 8 escaneos más recientes.', items: new OA\Items(ref: '#/components/schemas/Scan')),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'El periodo no es uno de los permitidos.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function superadmin() {}
}
