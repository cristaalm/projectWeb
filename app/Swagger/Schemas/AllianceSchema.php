<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Alliance',
    description: 'Comercio aliado (App\Models\Alliance).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Alianza EcoSort Centro'),
        new OA\Property(property: 'contact_name', type: 'string', example: 'Juan Pérez'),
        new OA\Property(property: 'contact_email', type: 'string', format: 'email', example: 'juan@ejemplo.com'),
        new OA\Property(property: 'phone', type: 'string', example: '5551234567'),
        new OA\Property(property: 'address', type: 'string', example: 'Av. Siempre Viva 123'),
        new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true, example: 19.4326),
        new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true, example: -99.1332),
        new OA\Property(property: 'logo_url', type: 'string', nullable: true, description: 'Ruta relativa en el disco público — anteponer /storage/ en el frontend.', example: 'alliances/alliance_1/logo.png'),
        new OA\Property(property: 'has_exclusive_rewards', type: 'boolean', description: 'Si es true, esta alianza acepta enlazar usuarios con rol member (App\Http\Requests\Users\CreateUserRequest lo valida al crear un member con alliance_id).', example: false),
        new OA\Property(property: 'type_shop_id', type: 'integer', example: 1),
        new OA\Property(property: 'type_shop', ref: '#/components/schemas/TypeShop', nullable: true, description: 'Presente cuando el endpoint hace eager-load de la relación (ej. el listado).'),
        new OA\Property(property: 'status', type: 'integer', enum: [0, 1], description: '1 = activa, 0 = pausada (App\Enums\AllianceStatus).', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class AllianceSchema
{
    // Contenedor de anotaciones; no se instancia.
}
