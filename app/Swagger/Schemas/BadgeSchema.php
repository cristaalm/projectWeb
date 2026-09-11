<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Badge',
    description: 'Insignia del catálogo de reciclaje (App\Models\Badge).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Reciclador del mes'),
        new OA\Property(property: 'icon', type: 'string', nullable: true, enum: ['bx-recycle', 'bx-leaf', 'bx-bxs-tree', 'bx-water', 'bx-world', 'bx-medal', 'bx-trophy', 'bx-award', 'bx-badge-check', 'bx-sun'], example: 'bx-recycle'),
        new OA\Property(property: 'recycles_required', type: 'integer', description: 'Reciclajes válidos requeridos en un mes calendario para completar la insignia.', example: 10),
        new OA\Property(property: 'points_awarded', type: 'integer', description: 'Puntos acreditados al reclamar la insignia.', example: 50),
        new OA\Property(property: 'status', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class BadgeSchema
{
    // Contenedor de anotaciones; no se instancia.
}
