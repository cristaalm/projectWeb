<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Shop',
    description: 'Comercio activo en su forma pública para la app móvil (App\Http\Resources\ShopResource sobre App\Models\Alliance). No incluye datos de contacto administrativo (contact_name, contact_email, phone) ni status/timestamps.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Café Central'),
        new OA\Property(property: 'logo_url', type: 'string', nullable: true, description: 'URL absoluta del logo (disco public, Storage::url). null si el comercio no tiene logo. Requiere APP_URL correcto en el entorno.', example: 'https://api.ejemplo.com/storage/alliances/alliance_1/logo.png'),
        new OA\Property(property: 'address', type: 'string', example: 'Calle Uno 10, Centro'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true, example: 19.4326),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true, example: -99.1332),
        new OA\Property(property: 'has_exclusive_rewards', type: 'boolean', example: false),
        new OA\Property(
            property: 'type_shop',
            type: 'object',
            nullable: true,
            description: 'Categoría del comercio.',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 3),
                new OA\Property(property: 'name', type: 'string', example: 'Cafetería'),
            ]
        ),
    ]
)]
class ShopSchema
{
    // Contenedor de anotaciones; no se instancia.
}
