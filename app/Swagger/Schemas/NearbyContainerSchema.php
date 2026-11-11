<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NearbyContainer',
    description: 'Contenedor de "contenedores cerca de ti" (App\Http\Resources\NearbyContainerResource), con el último nivel de llenado de cada compartimento (container_sensors). No incluye serial_number ni el token.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 4),
        new OA\Property(property: 'name', type: 'string', example: 'Contenedor Jardín Libertad'),
        new OA\Property(property: 'location', type: 'string', nullable: true, description: 'Descripción del lugar.', example: 'Jardín Libertad, Centro, Colima'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 19.2433),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', example: -103.725),
        new OA\Property(property: 'distance_m', type: 'integer', description: 'Distancia en metros desde la ubicación enviada.', example: 640),
        new OA\Property(
            property: 'sensors',
            type: 'array',
            description: 'Un elemento por compartimento, ordenados por sensor_key. Vacía si el contenedor todavía no reporta su llenado.',
            items: new OA\Items(type: 'object', properties: [
                new OA\Property(property: 'sensor_key', type: 'string', description: 'Identificador del compartimento (por defecto, el slug del material).', example: 'plastic'),
                new OA\Property(property: 'material', type: 'object', nullable: true, description: 'null si el material se borró del catálogo.', properties: [
                    new OA\Property(property: 'slug', type: 'string', example: 'plastic'),
                    new OA\Property(property: 'name', type: 'string', example: 'Plástico'),
                ]),
                new OA\Property(property: 'fill_level', type: 'number', format: 'float', minimum: 0, maximum: 100, description: 'Porcentaje de llenado.', example: 72.5),
                new OA\Property(property: 'is_full', type: 'boolean', description: 'true desde 90%.', example: false),
                new OA\Property(property: 'measured_at', type: 'string', format: 'date-time', description: 'Hora de la última medición.', example: '2026-11-11T18:00:00.000000Z'),
                new OA\Property(property: 'is_stale', type: 'boolean', description: 'true si el compartimento lleva más de 6 horas sin reportar (el nivel puede no ser el actual).', example: false),
            ])
        ),
    ]
)]
class NearbyContainerSchema
{
    // Contenedor de anotaciones; no se instancia.
}
