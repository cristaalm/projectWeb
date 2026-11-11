<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Nearby', description: '"Cerca de ti" para la app móvil: recompensas, comercios y contenedores cercanos al usuario. Todas las rutas están bajo /nearby, son POST (la ubicación va en el cuerpo para que no quede en los logs de acceso) y solo exigen sesión/token activa (auth:sanctum + ensureUserIsActive), sin gate de rol. Reciben la ubicación como latitude/longitude/accuracy/captured_at. "Cercano" = 5 km o menos (Haversine). Una ubicación es inválida si accuracy > 500 m, si captured_at tiene más de 10 minutos o si está más de 5 minutos en el futuro. `limit` opcional: 5 por defecto; uno mayor a 50 no es error, se topa a 50.')]
class NearbyDocumentation
{
    #[OA\Post(
        path: '/nearby/rewards',
        tags: ['Nearby'],
        summary: 'Recompensas cerca de ti',
        description: 'Recompensas que el usuario puede canjear hoy (aprobadas, sin vencer, con stock, de comercios activos; las exclusivas solo si es miembro de esa alianza), sobre App\Services\NearbyService::rewards(). '
            .'La ubicación es opcional. Con una ubicación válida solo cuentan los comercios a 5 km o menos; si no hay ninguno con recompensas disponibles, o la ubicación falta o es inválida por precisión/antigüedad, se elige solo por puntos (location_used = false). Una ubicación con formato incorrecto (coordenadas fuera de rango, campos sueltos) sí es 422. '
            .'Orden: 1) las exclusivas de su alianza, 2) las que puede canjear —por distancia si location_used; si no, de más a menos puntos—, 3) las que menos puntos le faltan. Como mucho 2 por comercio; si con ese tope no se llena el límite, se completa con más de los mismos comercios.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'latitude', type: 'number', format: 'float', minimum: -90, maximum: 90, nullable: true, example: 19.0522),
                    new OA\Property(property: 'longitude', type: 'number', format: 'float', minimum: -180, maximum: 180, nullable: true, example: -104.3158),
                    new OA\Property(property: 'accuracy', type: 'number', format: 'float', minimum: 0, nullable: true, description: 'Precisión en metros que reporta el GPS. Obligatoria si se manda la ubicación.', example: 12.5),
                    new OA\Property(property: 'captured_at', type: 'string', format: 'date-time', nullable: true, description: 'Hora en que se obtuvo la ubicación (ISO-8601). Obligatoria si se manda la ubicación.', example: '2026-11-08T15:30:00Z'),
                    new OA\Property(property: 'limit', type: 'integer', minimum: 1, nullable: true, description: 'Cuántas recompensas devolver. 5 por defecto; mayor a 50 se topa a 50.', example: 5),
                ],
                example: ['latitude' => 19.0522, 'longitude' => -104.3158, 'accuracy' => 12.5, 'captured_at' => '2026-11-08T15:30:00Z']
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Recompensas para el usuario (puede ser una lista vacía).',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'location_used', type: 'boolean', description: 'true si la lista se armó con los comercios dentro del radio.', example: true),
                        new OA\Property(property: 'total_points', type: 'integer', description: 'Saldo actual del usuario (mismo cálculo que GET /points/balance).', example: 95),
                        new OA\Property(property: 'rewards', type: 'array', items: new OA\Items(ref: '#/components/schemas/NearbyReward')),
                    ])],
                    examples: [new OA\Examples(
                        example: 'con_ubicacion',
                        summary: 'Con ubicación válida y comercios cerca',
                        value: ['success' => true, 'message' => 'Recompensas obtenidas correctamente.', 'data' => [
                            'location_used' => true,
                            'total_points' => 95,
                            'rewards' => [
                                ['id' => 12, 'name' => 'Café americano gratis', 'description' => 'Un café americano de 12 oz.', 'image_url' => null, 'points_required' => 80, 'stock' => 15, 'expires_at' => null, 'is_exclusive' => false, 'can_redeem' => true, 'points_missing' => 0, 'shop' => ['id' => 3, 'name' => 'Café Central', 'logo_url' => null, 'address' => 'Calle Uno 10, Centro', 'latitude' => 19.0611, 'longitude' => -104.3158, 'has_exclusive_rewards' => false, 'type_shop' => ['id' => 1, 'name' => 'Cafetería'], 'distance_m' => 990]],
                                ['id' => 20, 'name' => '2x1 en pan dulce', 'description' => null, 'image_url' => null, 'points_required' => 120, 'stock' => null, 'expires_at' => '2026-12-31T23:59:59.000000Z', 'is_exclusive' => false, 'can_redeem' => false, 'points_missing' => 25, 'shop' => ['id' => 5, 'name' => 'Panadería La Espiga', 'logo_url' => null, 'address' => 'Av. Dos 200', 'latitude' => 19.0702, 'longitude' => -104.3101, 'has_exclusive_rewards' => false, 'type_shop' => ['id' => 2, 'name' => 'Panadería'], 'distance_m' => 2080]],
                            ],
                        ], 'errors' => null, 'code' => 200]
                    )]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado, o la cuenta fue dada de baja.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Ubicación con formato incorrecto (coordenadas fuera de rango, latitude sin longitude, falta accuracy/captured_at) o limit menor a 1.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function rewards() {}
}
