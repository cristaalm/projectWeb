<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Rewards', description: 'Catálogo de recompensas canjeables por puntos. Bajo /rewards, protegidas por auth:sanctum + ensureUserIsActive + role:superadmin,moderador,admin_merchant (primer módulo del dashboard con acceso no-staff). admin_merchant solo ve/gestiona las recompensas de su propia alianza (App\Models\User::currentAlliance()) y sus creaciones quedan pendientes de revisión (App\Enums\RewardStatus::PENDING); superadmin/moderador gestionan cualquier alianza y sus creaciones se aprueban de una vez. Aprobar/rechazar es exclusivo de superadmin/moderador.')]
class RewardsDocumentation
{
    #[OA\Get(
        path: '/rewards',
        tags: ['Rewards'],
        summary: 'Listar recompensas',
        description: 'Listado paginado/filtrable/ordenable. Si quien pide el listado es admin_merchant, alliance_id se fuerza a su propia alianza sin importar qué mande el query string (App\Services\RewardService::scopeAllianceFilter()).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'alliance_id', in: 'query', description: 'Ignorado si quien pide el listado es admin_merchant.', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'integer', enum: [0, 1, 2, 3])),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda libre por nombre, descripción o código (ilike).', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'key', in: 'query', schema: new OA\Schema(type: 'string', enum: ['id', 'name', 'points_required', 'stock', 'status', 'is_exclusive', 'expires_at', 'created_at'])),
            new OA\Parameter(name: 'order', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Página de recompensas.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin, moderador ni admin_merchant.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/rewards',
        tags: ['Rewards'],
        summary: 'Crear recompensa',
        description: 'admin_merchant: alliance_id se ignora, se fuerza a su propia alianza y queda en status PENDING. staff (superadmin/moderador): alliance_id es obligatorio y la recompensa se crea ya APPROVED (aprobada por quien la crea). is_exclusive=true solo se acepta si la alianza tiene has_exclusive_rewards=true.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'points_required'],
                properties: [
                    new OA\Property(property: 'alliance_id', type: 'integer', description: 'Obligatorio para staff; ignorado para admin_merchant.', example: 1),
                    new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'Café gratis'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, maxLength: 1000),
                    new OA\Property(property: 'points_required', type: 'integer', minimum: 1, example: 100),
                    new OA\Property(property: 'stock', type: 'integer', nullable: true, minimum: 0, description: 'null = ilimitado.'),
                    new OA\Property(property: 'is_exclusive', type: 'boolean', example: false),
                    new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', nullable: true, description: 'Debe ser una fecha futura.'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Recompensa creada.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin, moderador ni admin_merchant.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación, o is_exclusive=true sobre una alianza sin has_exclusive_rewards, o la cuenta admin_merchant no está enlazada a ninguna alianza.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function store() {}

    #[OA\Put(
        path: '/rewards/{id}',
        tags: ['Rewards'],
        summary: 'Actualizar recompensa',
        description: 'admin_merchant solo puede editar recompensas de su propia alianza (403 si no); si la recompensa editada no estaba PENDING, vuelve a PENDING y se limpia el resultado de la revisión anterior — necesita aprobarse de nuevo. staff puede editar cualquiera, sin resetear el estado, y es el único que puede reasignar alliance_id.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'points_required'],
                properties: [
                    new OA\Property(property: 'alliance_id', type: 'integer', description: 'Solo staff puede reasignar; se ignora si lo manda admin_merchant.'),
                    new OA\Property(property: 'name', type: 'string', maxLength: 150),
                    new OA\Property(property: 'description', type: 'string', nullable: true, maxLength: 1000),
                    new OA\Property(property: 'points_required', type: 'integer', minimum: 1),
                    new OA\Property(property: 'stock', type: 'integer', nullable: true, minimum: 0),
                    new OA\Property(property: 'is_exclusive', type: 'boolean'),
                    new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Recompensa actualizada.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Rol no permitido, o admin_merchant intentando editar una recompensa de otra alianza.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Recompensa no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación, o is_exclusive=true sobre una alianza sin has_exclusive_rewards.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function update() {}

    #[OA\Delete(
        path: '/rewards/{id}',
        tags: ['Rewards'],
        summary: 'Eliminar recompensa',
        description: 'Soft-delete. admin_merchant solo sobre las de su propia alianza.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Recompensa eliminada.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Rol no permitido, o admin_merchant intentando eliminar una recompensa de otra alianza.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Recompensa no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function destroy() {}

    #[OA\Post(
        path: '/rewards/{id}/approve',
        tags: ['Rewards'],
        summary: 'Aprobar recompensa pendiente',
        description: 'Exclusivo de superadmin/moderador. Solo aplica sobre recompensas en status PENDING.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Recompensa aprobada.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Recompensa no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'La recompensa no está en status PENDING.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function approve() {}

    #[OA\Post(
        path: '/rewards/{id}/reject',
        tags: ['Rewards'],
        summary: 'Rechazar recompensa pendiente',
        description: 'Exclusivo de superadmin/moderador. Solo aplica sobre recompensas en status PENDING. El motivo queda guardado en rejection_reason.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['reason'],
                properties: [
                    new OA\Property(property: 'reason', type: 'string', maxLength: 1000, example: 'El costo en puntos no corresponde al valor real del producto.'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Recompensa rechazada.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Recompensa no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Falta el motivo, o la recompensa no está en status PENDING.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function reject() {}

    #[OA\Post(
        path: '/rewards/{id}/pause',
        tags: ['Rewards'],
        summary: 'Pausar recompensa aprobada',
        description: 'Solo aplica sobre recompensas en status APPROVED — no pierde su historial de aprobación (a diferencia de un rechazo). admin_merchant solo sobre las de su propia alianza.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Recompensa pausada.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Rol no permitido, o admin_merchant intentando pausar una recompensa de otra alianza.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Recompensa no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'La recompensa no está en status APPROVED.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function pause() {}

    #[OA\Post(
        path: '/rewards/{id}/reactivate',
        tags: ['Rewards'],
        summary: 'Reactivar recompensa pausada',
        description: 'Solo aplica sobre recompensas en status PAUSED. admin_merchant solo sobre las de su propia alianza.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Recompensa reactivada.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Rol no permitido, o admin_merchant intentando reactivar una recompensa de otra alianza.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Recompensa no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'La recompensa no está en status PAUSED.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function reactivate() {}
}
