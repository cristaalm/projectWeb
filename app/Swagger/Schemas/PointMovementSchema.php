<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PointMovement',
    description: 'Movimiento del saldo de puntos del usuario autenticado (App\Http\Resources\PointMovementResource sobre App\Repositories\PointRepository::movements()). Une 4 fuentes: reciclaje (point_earnings), insignias (badge_earnings), ajustes de admin (point_adjustments) y canjes (point_redemptions, solo REDEEMED/DELIVERED). La suma de todos los points coincide con total_points de /points/balance.',
    properties: [
        new OA\Property(property: 'type', type: 'string', enum: ['earning', 'badge', 'adjustment', 'redemption'], description: 'Fuente del movimiento (App\Enums\PointMovementType).', example: 'earning'),
        new OA\Property(property: 'label', type: 'string', description: 'Etiqueta en español del tipo: Reciclaje, Insignia, Ajuste o Canje.', example: 'Reciclaje'),
        new OA\Property(property: 'description', type: 'string', nullable: true, description: 'Reciclaje: nombre del material. Insignia: nombre de la insignia. Ajuste: motivo. Canje: nombre de la recompensa.', example: 'PET'),
        new OA\Property(property: 'points', type: 'integer', description: 'Puntos con signo: positivo suma al saldo, negativo lo descuenta (ajustes negativos y canjes = puntos × cantidad).', example: 5),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true, example: '2026-09-20T16:31:28.000000Z'),
    ]
)]
class PointMovementSchema
{
    // Contenedor de anotaciones; no se instancia.
}
