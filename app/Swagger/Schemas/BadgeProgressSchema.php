<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BadgeProgress',
    description: 'Progreso mensual de un usuario hacia una insignia (App\Models\BadgeProgress). Cada mes calendario tiene su propia fila — el reinicio mensual es implícito, no requiere un job.',
    properties: [
        new OA\Property(property: 'badge', ref: '#/components/schemas/Badge'),
        new OA\Property(property: 'recycles_count', type: 'integer', description: 'Reciclajes válidos acumulados en el mes actual.', example: 7),
        new OA\Property(property: 'completed', type: 'boolean', description: 'true cuando recycles_count alcanzó recycles_required — todavía no implica que se haya reclamado.', example: false),
    ]
)]
class BadgeProgressSchema
{
    // Contenedor de anotaciones; no se instancia.
}
