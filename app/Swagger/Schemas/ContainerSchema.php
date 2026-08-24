<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Container',
    description: 'Contenedor de reciclaje (App\Models\Container).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Contenedor Parque Central'),
        new OA\Property(property: 'serial_number', type: 'string', example: 'SN-0001'),
        new OA\Property(property: 'location', type: 'string', example: 'Parque Central'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true, example: 19.4326),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true, example: -99.1332),
        new OA\Property(property: 'status', type: 'integer', enum: [0, 1], description: '1 = activo, 0 = inactivo (App\Enums\ContainerStatus).', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class ContainerSchema
{
    // Contenedor de anotaciones; no se instancia.
}
