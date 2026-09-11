<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Reward',
    description: 'Recompensa canjeable por puntos, propiedad de una alianza (App\Models\Reward).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'alliance_id', type: 'integer', example: 1),
        new OA\Property(property: 'alliance', ref: '#/components/schemas/Alliance', nullable: true, description: 'Presente cuando el endpoint hace eager-load de la relación (ej. el listado).'),
        new OA\Property(property: 'name', type: 'string', example: 'Café gratis'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Un café americano o espresso, tamaño chico.'),
        new OA\Property(property: 'points_required', type: 'integer', example: 100),
        new OA\Property(property: 'stock', type: 'integer', nullable: true, description: 'null = ilimitado.', example: 50),
        new OA\Property(property: 'code', type: 'string', description: 'Código de 13 dígitos (12 aleatorios + dígito verificador EAN-13) generado por el backend al crear — no editable.', example: '123456789012 5'),
        new OA\Property(property: 'is_exclusive', type: 'boolean', description: 'Solo puede ser true si la alianza tiene has_exclusive_rewards=true (App\Models\Alliance).', example: false),
        new OA\Property(property: 'status', type: 'integer', enum: [0, 1, 2, 3], description: '0 = pendiente de revisión, 1 = aprobada, 2 = rechazada, 3 = pausada (App\Enums\RewardStatus).', example: 1),
        new OA\Property(property: 'rejection_reason', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'approved_by', type: 'integer', nullable: true),
        new OA\Property(property: 'approved_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'rejected_by', type: 'integer', nullable: true),
        new OA\Property(property: 'rejected_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'deleted_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class RewardSchema
{
    // Contenedor de anotaciones; no se instancia.
}
