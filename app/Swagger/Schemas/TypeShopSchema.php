<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TypeShop',
    description: 'Categoría de comercio (App\Models\TypeShop) — clasifica a las alianzas (ej. Supermercado, Farmacia).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Supermercado'),
        new OA\Property(property: 'icon', type: 'string', example: 'cart', description: 'Nombre del icono en Material Design Icons (sin prefijo), de un catálogo cerrado — ver GET /type-shop/icons.'),
        new OA\Property(property: 'icon_url', type: 'string', nullable: true, example: 'https://api.ejemplo.com/icons/type-shop/cart.svg', description: 'URL absoluta del SVG del icono (24x24, fill="currentColor"). Requiere APP_URL correcto en el entorno.'),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'TypeShopSummary',
    description: 'Forma mínima de una categoría anidada en otras respuestas (comercios, alianza del usuario).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 3),
        new OA\Property(property: 'name', type: 'string', example: 'Cafetería'),
        new OA\Property(property: 'icon', type: 'string', example: 'coffee', description: 'Nombre del icono en Material Design Icons (sin prefijo).'),
        new OA\Property(property: 'icon_url', type: 'string', nullable: true, example: 'https://api.ejemplo.com/icons/type-shop/coffee.svg', description: 'URL absoluta del SVG del icono.'),
    ]
)]
class TypeShopSchema
{
    // Contenedor de anotaciones; no se instancia.
}
