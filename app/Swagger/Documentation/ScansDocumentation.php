<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Scans', description: 'Flujo de escaneo/reciclaje. POST /scans lo llama el software del contenedor (que ya clasificó el material con su modelo de visión en el borde), autenticado por API key compartida (App\Http\Middleware\EnsureValidServiceApiKey) — no un usuario logueado. Un escaneo válido acredita puntos (point_earnings), avanza el progreso mensual de insignias y la racha diaria del usuario. GET /scans y GET /material-types/catalog son de consulta administrativa (auth:sanctum + ensureUserIsActive + role:superadmin,moderador).')]
class ScansDocumentation
{
    #[OA\Post(
        path: '/scans',
        tags: ['Scans'],
        summary: 'Registrar un escaneo reportado por un contenedor',
        description: 'Resuelve el contenedor por serial_number y el usuario por code_identity, y registra el reciclaje sobre App\Services\ScanService::register(). Los puntos son siempre los del material (material_types.points). Si el material está inactivo o vale 0 puntos (ej. "other"), el escaneo se guarda igualmente con status 0 y sin acreditar nada. `event_id` hace la llamada idempotente: reintentar con el mismo valor devuelve el escaneo original (HTTP 200, duplicate=true) sin volver a dar puntos. Se puede enviar como JSON (sin imagen) o como multipart/form-data (con imagen).',
        security: [['eviApiKey' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['event_id', 'code_identity', 'container_serial_number', 'material'],
                    properties: [
                        new OA\Property(property: 'event_id', type: 'string', format: 'uuid', description: 'UUID generado por el contenedor, uno por escaneo.', example: 'ad62c108-7c63-451d-af7e-59be2b426346'),
                        new OA\Property(property: 'code_identity', type: 'string', maxLength: 30, example: '5760307480459'),
                        new OA\Property(property: 'container_serial_number', type: 'string', maxLength: 255, example: 'SN-0001'),
                        new OA\Property(property: 'material', type: 'string', description: 'Slug del material clasificado (material_types.slug).', example: 'plastic'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary', nullable: true, description: 'Opcional. JPEG o PNG, máximo 5 MB.'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Escaneo registrado (válido o rechazado — ver data.scan.status).',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'scan', ref: '#/components/schemas/Scan'),
                                new OA\Property(property: 'total_points', type: 'integer', description: 'Saldo del usuario después del escaneo.', example: 365),
                                new OA\Property(property: 'points_month', type: 'integer', description: 'Puntos ganados en el mes calendario en curso (reciclaje + insignias).', example: 55),
                                new OA\Property(
                                    property: 'streak',
                                    type: 'object',
                                    description: 'Racha de días consecutivos con al menos un reciclaje válido.',
                                    properties: [
                                        new OA\Property(property: 'current_streak', type: 'integer', example: 5),
                                        new OA\Property(property: 'best_streak', type: 'integer', example: 9),
                                        new OA\Property(property: 'streak_status', type: 'boolean', description: 'true si la racha sigue viva (recicló hoy o ayer).', example: true),
                                    ]
                                ),
                                new OA\Property(property: 'duplicate', type: 'boolean', example: false),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 200, description: 'El event_id ya había sido procesado: devuelve el escaneo original con duplicate=true, sin acreditar nada.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'API key ausente o inválida.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'El contenedor (container_serial_number) o el usuario (code_identity) no existen.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación (campos requeridos, event_id no es UUID, material fuera del catálogo, imagen inválida), contenedor inactivo o cuenta del usuario desactivada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function store() {}

    #[OA\Get(
        path: '/scans',
        tags: ['Scans'],
        summary: 'Listar escaneos (administración)',
        description: 'Listado paginado/filtrable/ordenable sobre App\Repositories\ScanRepository::paginate(). Por defecto, los más recientes primero.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', description: '1 = éxito, 0 = fallo.', schema: new OA\Schema(type: 'integer', enum: [0, 1])),
            new OA\Parameter(name: 'container_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'material_type_id', in: 'query', description: 'Sale de GET /material-types/catalog.', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda libre por nombre, apellido, correo o code_identity del usuario, o por nombre/número de serie del contenedor (ilike).', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'key', in: 'query', description: 'Columna de orden — allowlist explícita.', schema: new OA\Schema(type: 'string', enum: ['id', 'points_awarded', 'scan_status', 'scanned_at', 'created_at'])),
            new OA\Parameter(name: 'order', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Página de escaneos.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Scan')),
                                new OA\Property(property: 'last_page', type: 'integer', example: 1),
                                new OA\Property(property: 'total', type: 'integer', example: 1),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Parámetro de filtro u orden inválido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function index() {}

    #[OA\Get(
        path: '/material-types/catalog',
        tags: ['Scans'],
        summary: 'Catálogo de tipos de material',
        description: 'Todos los tipos de material (incluidos los inactivos), ordenados por nombre — pensado para el selector de filtros del listado de escaneos.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Catálogo de materiales.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'catalogo', summary: 'Catálogo', value: ['success' => true, 'message' => 'Tipos de material obtenidos correctamente.', 'data' => ['material_types' => [['id' => 2, 'name' => 'Aluminio', 'slug' => 'aluminum', 'points' => 35, 'is_active' => true], ['id' => 1, 'name' => 'Plástico', 'slug' => 'plastic', 'points' => 15, 'is_active' => true]]], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function materialCatalog() {}
}
