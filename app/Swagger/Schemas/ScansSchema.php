<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "Scans", title: "Scans", description: "Representa un escaneo registrado en el sistema")]
class ScansSchema
{
    #[OA\Property(property: "id", type: "integer", example: 1)]
    public int $id;

    #[OA\Property(property: "user_id", type: "integer", example: 1)]
    public int $user_id;

    #[OA\Property(property: "container_id", type: "integer", example: 1)]
    public int $container_id;

    #[OA\Property(property: "material_type_id", type: "integer", example: 1)]
    public int $material_type_id;

    #[OA\Property(property: "image", type: "string", example: "image.jpg")]
    public string $image;

    #[OA\Property(property: "scan_status", type: "integer", example: 1)]
    public int $scan_status;

    #[OA\Property(property: "is_valid", type: "boolean", example: true)]
    public bool $is_valid;

    #[OA\Property(property: "points_awarded", type: "integer", example: 10)]
    public int $points_awarded;

    #[OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $created_at;

    #[OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $updated_at;
}
