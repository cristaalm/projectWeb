<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "IdentityVerification", title: "Identity Verification", description: "Verificación de identidad de un usuario")]
class IdentityVerificationSchema
{
    #[OA\Property(property: "id", type: "integer", example: 1)]
    public int $id;

    #[OA\Property(property: "user_id", type: "integer", example: 5)]
    public int $user_id;

    #[OA\Property(property: "ine_front_url", type: "string", example: "users/user_5/ine_front.jpg", nullable: true)]
    public ?string $ine_front_url;

    #[OA\Property(property: "ine_back_url", type: "string", example: "users/user_5/ine_back.jpg", nullable: true)]
    public ?string $ine_back_url;

    #[OA\Property(property: "selfie_url", type: "string", example: "users/user_5/selfie.jpg", nullable: true)]
    public ?string $selfie_url;

    #[OA\Property(property: "status", type: "integer", example: 1)]
    public int $status;

    #[OA\Property(property: "rejection_reason", type: "string", nullable: true)]
    public ?string $rejection_reason;

    #[OA\Property(property: "verified_by", type: "integer", example: 1, nullable: true)]
    public ?int $verified_by;

    #[OA\Property(property: "verified_at", type: "string", format: "date-time", nullable: true)]
    public ?string $verified_at;

    #[OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $created_at;

    #[OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $updated_at;
}
