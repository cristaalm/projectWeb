<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TypeShop',
    description: 'Categoría de comercio (App\Models\TypeShop) — clasifica a las alianzas (ej. Supermercado, Farmacia).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Supermercado'),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class TypeShopSchema
{
    // Contenedor de anotaciones; no se instancia.
}
