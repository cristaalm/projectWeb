<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BadgeUser',
    description: 'Insignia reclamada por un usuario en un mes específico (App\Models\BadgeUser). La misma insignia puede volver a reclamarse en un mes distinto.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'badge', ref: '#/components/schemas/Badge'),
        new OA\Property(property: 'month', type: 'string', format: 'date', example: '2026-09-01'),
        new OA\Property(property: 'awarded_at', type: 'string', format: 'date-time'),
    ]
)]
class BadgeUserSchema
{
    // Contenedor de anotaciones; no se instancia.
}
