<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Scans', description: 'Flujo de escaneo/reciclaje. POST /scans/identify y POST /scans los llama el software del contenedor físico, autenticado con el token propio de ese contenedor (header X-Container-Token, App\Http\Middleware\EnsureValidContainerToken) — no un usuario logueado. El contenedor clasifica el material con su modelo de visión en el borde y solo reporta el resultado. Un escaneo válido acredita puntos (point_earnings), avanza el progreso mensual de insignias y la racha diaria del usuario. GET /scans y GET /material-types/catalog son de consulta administrativa (auth:sanctum + ensureUserIsActive + role:superadmin,moderador).')]
class ScansDocumentation
{
    #[OA\Post(
        path: '/scans/identify',
        tags: ['Scans'],
        summary: 'Identificar al usuario que se acercó al contenedor',
        description: 'Resuelve al usuario por su code_identity (leído de su QR/NFC) y devuelve sus datos para que el contenedor pueda saludarlo y mostrarle su avance, sobre App\Services\ScanService::identify(). No registra nada. El contenedor es el dueño del token.',
        security: [['containerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['code_identity'],
                properties: [
                    new OA\Property(property: 'code_identity', type: 'string', maxLength: 30, example: '7501000000012'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuario identificado.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'user',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 39),
                                        new OA\Property(property: 'code_identity', type: 'string', example: '7501000000012'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Ana'),
                                        new OA\Property(property: 'last_name', type: 'string', example: 'López'),
                                    ]
                                ),
                                new OA\Property(property: 'total_points', type: 'integer', description: 'Saldo actual de puntos.', example: 100),
                                new OA\Property(property: 'points_month', type: 'integer', description: 'Puntos ganados en el mes calendario en curso (reciclaje + insignias).', example: 100),
                                new OA\Property(property: 'valid_scans', type: 'integer', description: 'Reciclajes válidos de por vida.', example: 4),
                                new OA\Property(
                                    property: 'streak',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'current_streak', type: 'integer', example: 1),
                                        new OA\Property(property: 'best_streak', type: 'integer', example: 3),
                                        new OA\Property(property: 'streak_status', type: 'boolean', description: 'true si la racha sigue viva (recicló hoy o ayer).', example: true),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'badge',
                                    type: 'object',
                                    nullable: true,
                                    description: 'Insignia de mayor nivel ya completada este mes, o null.',
                                    properties: [
                                        new OA\Property(property: 'name', type: 'string', example: 'Reciclador Bronce'),
                                        new OA\Property(property: 'recycles_remaining', type: 'integer', example: 0),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'next_badge',
                                    type: 'object',
                                    nullable: true,
                                    description: 'Siguiente insignia sin completar, o null si ya completó todas.',
                                    properties: [
                                        new OA\Property(property: 'name', type: 'string', example: 'Reciclador Plata'),
                                        new OA\Property(property: 'recycles_required', type: 'integer', example: 25),
                                        new OA\Property(property: 'recycles_remaining', type: 'integer', example: 21),
                                    ]
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Token de contenedor ausente o inválido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El contenedor dueño del token está inactivo.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'El code_identity no pertenece a ningún usuario.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Falta code_identity, o la cuenta del usuario está desactivada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function identify() {}

    #[OA\Post(
        path: '/scans',
        tags: ['Scans'],
        summary: 'Registrar un escaneo reportado por un contenedor',
        description: 'Registra el reciclaje sobre App\Services\ScanService::register(). El contenedor es siempre el dueño del token (no se envía en el body) y el usuario se resuelve por code_identity. Los puntos son siempre los del material (material_types.points). Si el material está inactivo o vale 0 puntos (ej. "other"), el escaneo se guarda igualmente con status 0 y sin acreditar nada. `event_id` hace la llamada idempotente: reintentar con el mismo valor devuelve el escaneo original (HTTP 200, duplicate=true) sin volver a dar puntos. Se puede enviar como JSON (sin imagen) o como multipart/form-data (con imagen).',
        security: [['containerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['event_id', 'code_identity', 'material'],
                    properties: [
                        new OA\Property(property: 'event_id', type: 'string', format: 'uuid', description: 'UUID generado por el contenedor, uno por escaneo.', example: 'ad62c108-7c63-451d-af7e-59be2b426346'),
                        new OA\Property(property: 'code_identity', type: 'string', maxLength: 30, example: '5760307480459'),
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
            new OA\Response(response: 401, description: 'Token de contenedor ausente o inválido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El contenedor dueño del token está inactivo.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'El code_identity no pertenece a ningún usuario.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 409, description: 'El event_id ya fue usado por otro contenedor.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación (campos requeridos, event_id no es UUID, material fuera del catálogo, imagen inválida) o cuenta del usuario desactivada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
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
        description: 'Todos los tipos de material (incluidos los inactivos), ordenados por nombre — alimenta la vista de Materiales del panel y el selector de filtros del listado de escaneos. El contenedor reporta el material por su slug, no por su id (los ids no coinciden entre bases).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Catálogo de materiales.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'catalogo', summary: 'Catálogo', value: ['success' => true, 'message' => 'Tipos de material obtenidos correctamente.', 'data' => ['material_types' => [['id' => 2, 'name' => 'Aluminio', 'slug' => 'aluminum', 'points' => 35, 'is_active' => true], ['id' => 4, 'name' => 'Cartón', 'slug' => 'cardboard', 'points' => 15, 'is_active' => true], ['id' => 3, 'name' => 'Otros', 'slug' => 'other', 'points' => 0, 'is_active' => true], ['id' => 1, 'name' => 'Plástico', 'slug' => 'plastic', 'points' => 15, 'is_active' => true], ['id' => 5, 'name' => 'Vidrio', 'slug' => 'glass', 'points' => 20, 'is_active' => true]]], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function materialCatalog() {}
}
