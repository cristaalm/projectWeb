<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PointRedemption',
    description: 'Canje de una recompensa por un usuario (App\Models\PointRedemption, tabla point_redemptions).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 5),
        new OA\Property(property: 'user', ref: '#/components/schemas/User', nullable: true),
        new OA\Property(property: 'reward_id', type: 'integer', example: 1),
        new OA\Property(property: 'reward', ref: '#/components/schemas/Reward', nullable: true),
        new OA\Property(property: 'alliance_id', type: 'integer', example: 1),
        new OA\Property(property: 'alliance', ref: '#/components/schemas/Alliance', nullable: true),
        new OA\Property(property: 'merchant_user_id', type: 'integer', nullable: true, description: 'Usuario del comercio que confirmó la entrega en persona — se asigna solo cuando quien marca "entregado" es admin_merchant.'),
        new OA\Property(property: 'merchant_user', ref: '#/components/schemas/User', nullable: true),
        new OA\Property(property: 'points_spent', type: 'integer', description: 'Puntos por unidad (multiplicar por quantity para el total gastado).', example: 100),
        new OA\Property(property: 'quantity', type: 'integer', example: 1),
        new OA\Property(property: 'status', type: 'integer', enum: [1, 2, 3, 4], description: '1 = Canjeado, 2 = Entregado, 3 = Cancelado, 4 = Expirado (App\Enums\RewardRedemptionStatus). Solo Canjeado/Entregado restan del saldo de puntos del usuario (App\Repositories\UserRepository::pointsBalance()).', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class PointRedemptionSchema
{
    // Contenedor de anotaciones; no se instancia.
}
