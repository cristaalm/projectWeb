<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Reward",
    title: "Reward",
    description: "Representa una recompensa ofrecida por una alianza (comercio)"
)]
class RewardSchema
{
    #[OA\Property(property: "id", type: "integer", example: 1)]
    public int $id;

    #[OA\Property(property: "alliance_id", type: "integer", example: 1)]
    public int $alliance_id;

    #[OA\Property(property: "name", type: "string", maxLength: 255, example: "Auriculares Gamer RGB")]
    public string $name;

    #[OA\Property(property: "description", type: "string", maxLength: 255, example: "Auriculares con sonido envolvente y micrófono retráctil")]
    public string $description;

    #[OA\Property(property: "points_required", type: "integer", example: 1500)]
    public int $points_required;

    #[OA\Property(property: "image", type: "boolean", example: true, description: "Indica si la recompensa tiene una imagen asociada")]
    public bool $image;

    #[OA\Property(property: "stock", type: "integer", nullable: true, example: 50)]
    public ?int $stock;

    #[OA\Property(property: "code", type: "string", maxLength: 255, example: "REWARD-GAMER-001")]
    public string $code;

    #[OA\Property(property: "is_active", type: "boolean", example: true)]
    public bool $is_active;

    #[OA\Property(property: "expires_at", type: "string", format: "date-time", nullable: true, example: "2025-12-31T23:59:59.000000Z")]
    public ?string $expires_at;

    #[OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $created_at;

    #[OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-02T14:30:00.000000Z")]
    public string $updated_at;

    #[OA\Property(property: "alliance", ref: "#/components/schemas/Alliance", nullable: true)]
    public ?object $alliance;
}
