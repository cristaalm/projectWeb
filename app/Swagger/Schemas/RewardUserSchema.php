<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "RewardUser",
    title: "RewardUser",
    description: "Registro de una recompensa reclamada por un usuario"
)]
class RewardUserSchema
{
    #[OA\Property(property: "id", type: "integer", example: 1)]
    public int $id;

    #[OA\Property(property: "user_id", type: "integer", example: 5)]
    public int $user_id;

    #[OA\Property(property: "reward_id", type: "integer", example: 3)]
    public int $reward_id;

    #[OA\Property(property: "redeemed_at", type: "string", format: "date-time", example: "2025-04-05T14:30:00.000000Z")]
    public string $redeemed_at;
}
