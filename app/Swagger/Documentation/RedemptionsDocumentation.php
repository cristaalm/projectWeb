<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Redemptions', description: 'Consulta de canjes de recompensas (point_redemptions) y confirmación de entrega en persona. Bajo /redemptions, protegidas por auth:sanctum + ensureUserIsActive + role:superadmin,moderador,admin_merchant. admin_merchant solo ve/gestiona los canjes de su propia alianza. El canje en sí (el cliente gastando sus puntos) no se hace desde este panel — se asume que ocurre desde una app móvil, fuera de alcance de este módulo.')]
class RedemptionsDocumentation
{
    #[OA\Get(
        path: '/redemptions',
        tags: ['Redemptions'],
        summary: 'Listar canjes',
        description: 'Listado paginado/filtrable/ordenable. Si quien pide el listado es admin_merchant, alliance_id se fuerza a su propia alianza (App\Services\RedemptionService::scopeAllianceFilter()).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'alliance_id', in: 'query', description: 'Ignorado si quien pide el listado es admin_merchant.', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'reward_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'integer', enum: [1, 2, 3, 4])),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda libre por nombre/correo del usuario o nombre/código de la recompensa.', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'key', in: 'query', schema: new OA\Schema(type: 'string', enum: ['id', 'points_spent', 'quantity', 'status', 'created_at'])),
            new OA\Parameter(name: 'order', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Página de canjes.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin, moderador ni admin_merchant.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function index() {}

    #[OA\Post(
        path: '/redemptions/{id}/deliver',
        tags: ['Redemptions'],
        summary: 'Marcar un canje como entregado',
        description: 'Canjeado → Entregado. Solo aplica sobre canjes en status Canjeado (1). Cuando lo marca admin_merchant (no staff) y el canje todavía no tenía merchant_user_id asignado, queda registrado como quien lo entregó.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Canje marcado como entregado.', content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'Rol no permitido, o admin_merchant intentando confirmar un canje de otra alianza.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Canje no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'El canje no está en status Canjeado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function deliver() {}
}
