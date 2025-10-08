<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "User", title: "User", description: "Representa un usuario")]
class UserSchema
{
    #[OA\Property(property: "id", type: "integer", example: 1)]
    public int $id;

    #[OA\Property(property: "name", type: "string", example: "John Doe")]
    public string $name;

    #[OA\Property(property: "last_name", type: "string", example: "Doe")]
    public string $last_name;

    #[OA\Property(property: "email", type: "string", example: "john.doe@example.com")]
    public string $email;

    #[OA\Property(property: "phone", type: "string", example: "1234567890")]
    public string $phone;

    #[OA\Property(property: "curp", type: "string", example: "CURP")]
    public string $curp;

    #[OA\Property(property: "email_verified_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $email_verified_at;

    #[OA\Property(property: "role_id", type: "integer", example: 1)]
    public int $role_id;

    #[OA\Property(property: "total_points", type: "integer", example: 95)]
    public int $total_points;

    #[OA\Property(property: "verification_status", type: "integer", example: 1)]
    public int $verification_status;

    #[OA\Property(property: "two_factor_status", type: "boolean", example: true)]
    public bool $two_factor_status;

    #[OA\Property(property: "code_identity", type: "string", example: '4075224740324')]
    public string $code_identity;

    #[OA\Property(property: "status", type: "string", example: "ACTIVE")]
    public string $status;

    #[OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $created_at;

    #[OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $updated_at;
}
