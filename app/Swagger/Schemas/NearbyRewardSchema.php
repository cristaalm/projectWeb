<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NearbyReward',
    description: 'Recompensa de "recompensas cerca de ti" (App\Http\Resources\NearbyRewardResource). No incluye `code`: es el código que se entrega al canjear.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'name', type: 'string', example: 'Café americano gratis'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Un café americano de 12 oz.'),
        new OA\Property(property: 'image_url', type: 'string', nullable: true, description: 'URL absoluta de la imagen del producto (disco public). null si no tiene.', example: 'https://api.ejemplo.com/storage/rewards/reward_12/image.png'),
        new OA\Property(property: 'points_required', type: 'integer', example: 80),
        new OA\Property(property: 'stock', type: 'integer', nullable: true, description: 'Unidades que quedan. null = ilimitado. Las de stock 0 no salen.', example: 15),
        new OA\Property(property: 'expires_at', type: 'string', format: 'date-time', nullable: true, description: 'null = sin vencimiento. Las vencidas no salen.', example: '2026-12-31T23:59:59.000000Z'),
        new OA\Property(property: 'is_exclusive', type: 'boolean', description: 'true = recompensa exclusiva para los miembros del comercio. Solo le aparecen al usuario que es miembro de esa alianza, y van al principio de la lista.', example: false),
        new OA\Property(property: 'can_redeem', type: 'boolean', description: 'true si el saldo del usuario alcanza los points_required.', example: true),
        new OA\Property(property: 'points_missing', type: 'integer', description: 'Puntos que le faltan (0 si can_redeem).', example: 0),
        new OA\Property(
            property: 'shop',
            description: 'Comercio de la recompensa, en la misma forma que GET /alliances/shops, más su distancia.',
            allOf: [
                new OA\Schema(ref: '#/components/schemas/Shop'),
                new OA\Schema(properties: [
                    new OA\Property(property: 'distance_m', type: 'integer', nullable: true, description: 'Distancia en metros desde la ubicación enviada. null si no se mandó ubicación válida o el comercio no tiene coordenadas.', example: 1240),
                ]),
            ]
        ),
    ]
)]
class NearbyRewardSchema
{
    // Contenedor de anotaciones; no se instancia.
}
