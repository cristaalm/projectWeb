<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Badges', description: 'Insignias de reciclaje (App\Models\Badge). El CRUD administrativo del catálogo está bajo /badges, protegido por auth:sanctum + ensureUserIsActive + role:superadmin,moderador. Las rutas de lectura/reclamo (/badges/catalog, /badges/my-progress, /badges/my-history, /badges/pending-claims, /badges/claim) solo exigen sesión/token activa, sin gate de rol — pensadas para la futura app móvil. El otorgamiento es un reclamo manual: al completar el progreso mensual solo se marca completed=true en App\Models\BadgeProgress, el usuario debe llamar a /badges/claim para recibir la insignia (App\Models\BadgeUser) y sus puntos (App\Models\BadgeEarning).')]
class BadgesDocumentation
{
    #[OA\Get(
        path: '/badges',
        tags: ['Badges'],
        summary: 'Listar insignias (administración)',
        description: 'Listado paginado/filtrable/ordenable sobre App\Repositories\BadgeRepository::paginate().',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'integer', enum: [0, 1])),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda libre por nombre (ilike).', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'key', in: 'query', description: 'Columna de orden — allowlist explícita.', schema: new OA\Schema(type: 'string', enum: ['id', 'name', 'recycles_required', 'points_awarded', 'status', 'created_at'])),
            new OA\Parameter(name: 'order', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Página de insignias.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/badges',
        tags: ['Badges'],
        summary: 'Crear insignia',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'recycles_required', 'points_awarded', 'status'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'Reciclador del mes'),
                    new OA\Property(property: 'icon', type: 'string', nullable: true, enum: ['bx-recycle', 'bx-leaf', 'bx-bxs-tree', 'bx-water', 'bx-world', 'bx-medal', 'bx-trophy', 'bx-award', 'bx-badge-check', 'bx-sun']),
                    new OA\Property(property: 'recycles_required', type: 'integer', minimum: 1, example: 10),
                    new OA\Property(property: 'points_awarded', type: 'integer', minimum: 0, example: 50),
                    new OA\Property(property: 'status', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Insignia creada.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: campos requeridos o nombre duplicado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function store() {}

    #[OA\Put(
        path: '/badges/{id}',
        tags: ['Badges'],
        summary: 'Actualizar insignia',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'recycles_required', 'points_awarded', 'status'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 100),
                    new OA\Property(property: 'icon', type: 'string', nullable: true, enum: ['bx-recycle', 'bx-leaf', 'bx-bxs-tree', 'bx-water', 'bx-world', 'bx-medal', 'bx-trophy', 'bx-award', 'bx-badge-check', 'bx-sun']),
                    new OA\Property(property: 'recycles_required', type: 'integer', minimum: 1),
                    new OA\Property(property: 'points_awarded', type: 'integer', minimum: 0),
                    new OA\Property(property: 'status', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Insignia actualizada.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Insignia no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: campos requeridos o nombre duplicado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/badges/{id}',
        tags: ['Badges'],
        summary: 'Eliminar insignia',
        description: 'Eliminación permanente. Bloqueada con 422 si la insignia ya tiene progreso o historial de usuarios vinculado (App\Models\BadgeProgress / App\Models\BadgeUser) — hay que desactivarla en vez de eliminarla para preservar la auditoría y los puntos ya acreditados.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Insignia eliminada.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Insignia no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Tiene progreso o historial de usuarios vinculado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function destroy() {}

    #[OA\Get(
        path: '/badges/catalog',
        tags: ['Badges'],
        summary: 'Catálogo de insignias activas',
        description: 'Solo insignias con status=true, ordenadas por recycles_required — pensado para el catálogo de la futura app móvil.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Catálogo de insignias.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function catalog() {}

    #[OA\Get(
        path: '/badges/my-progress',
        tags: ['Badges'],
        summary: 'Mi progreso del mes actual',
        description: 'Progreso del usuario autenticado sobre todas las insignias activas en el mes calendario actual (App\Services\BadgeService::myProgress()).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Progreso del mes actual.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [new OA\Property(property: 'data', properties: [new OA\Property(property: 'progress', type: 'array', items: new OA\Items(ref: '#/components/schemas/BadgeProgress'))], type: 'object')]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function myProgress() {}

    #[OA\Get(
        path: '/badges/my-history',
        tags: ['Badges'],
        summary: 'Mi historial de insignias reclamadas',
        description: 'Todas las insignias reclamadas por el usuario autenticado, de cualquier mes, orden descendente por fecha de otorgamiento (App\Services\BadgeService::myHistory()).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Historial de insignias.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [new OA\Property(property: 'data', properties: [new OA\Property(property: 'history', type: 'array', items: new OA\Items(ref: '#/components/schemas/BadgeUser'))], type: 'object')]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function myHistory() {}

    #[OA\Get(
        path: '/badges/pending-claims',
        tags: ['Badges'],
        summary: 'Mis insignias listas para reclamar',
        description: 'Progreso con completed=true (de cualquier mes, no expira) sin su fila correspondiente en App\Models\BadgeUser todavía — lo que puede reclamarse ahora mismo vía /badges/claim.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Reclamos pendientes.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [new OA\Property(property: 'data', properties: [new OA\Property(property: 'pending', type: 'array', items: new OA\Items(ref: '#/components/schemas/BadgeProgress'))], type: 'object')]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function pendingClaims() {}

    #[OA\Post(
        path: '/badges/claim',
        tags: ['Badges'],
        summary: 'Reclamar una insignia completada',
        description: 'Otorgamiento manual: crea la fila en App\Models\BadgeUser y acredita los puntos (App\Models\BadgeEarning). Solo funciona sobre un (usuario, insignia, mes) con progreso completed=true y todavía no reclamado — protegido además por un índice único a nivel de base de datos contra doble-reclamo por condición de carrera.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['badge_id', 'month'],
                properties: [
                    new OA\Property(property: 'badge_id', type: 'integer', example: 1),
                    new OA\Property(property: 'month', type: 'string', format: 'date', description: 'Cualquier día del mes calendario a reclamar; el backend lo normaliza al primer día.', example: '2026-09-01'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Insignia reclamada.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Insignia no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Aún no se completó esta insignia en el mes indicado, o ya fue reclamada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function claim() {}
}
