<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NearbyShop',
    description: 'Comercio de "comercios cerca de ti" (App\Http\Resources\NearbyShopResource): la misma forma que GET /alliances/shops más su distancia y cuántas recompensas tiene para el usuario.',
    allOf: [
        new OA\Schema(ref: '#/components/schemas/Shop'),
        new OA\Schema(properties: [
            new OA\Property(property: 'distance_m', type: 'integer', description: 'Distancia en metros desde la ubicación enviada.', example: 990),
            new OA\Property(property: 'available_rewards_count', type: 'integer', description: 'Recompensas disponibles para este usuario (aprobadas, sin vencer, con stock; las exclusivas solo si es miembro). Siempre ≥ 1.', example: 3),
            new OA\Property(property: 'redeemable_rewards_count', type: 'integer', description: 'De ésas, cuántas le alcanzan con su saldo actual.', example: 1),
        ]),
    ]
)]
class NearbyShopSchema
{
    // Contenedor de anotaciones; no se instancia.
}
