<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Badge",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Eco Warrior"),
        new OA\Property(property: "points_required", type: "integer", example: 100, description: "Puntos mensuales mínimos requeridos para desbloquear"),
        new OA\Property(property: "points_awared", type: "integer", example: 50, description: "Puntos que se otorgan al desbloquear"),
        new OA\Property(property: "status", type: "boolean", example: true, description: "true = activo, false = inactivo"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-01-01T10:00:00.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T15:30:00.000000Z"),
    ],
    required: ["id", "name", "points_required", "points_awared", "status"]
)]
class BadgeSchema {}
