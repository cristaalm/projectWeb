<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "Alliance", title: "Alliance", description: "Representa una alianza o comercio registrado en el sistema")]
class AllianceSchema
{
    #[OA\Property(property: "id", type: "integer", example: 1)]
    public int $id;

    #[OA\Property(property: "name", type: "string", example: "Empresa Ejemplo S.A.")]
    public string $name;

    #[OA\Property(property: "contact_name", type: "string", example: "Juan Pérez")]
    public string $contact_name;

    #[OA\Property(property: "contact_email", type: "string", example: "juan@example.com")]
    public string $contact_email;

    #[OA\Property(property: "phone", type: "string", example: "+57 300 1234567")]
    public string $phone;

    #[OA\Property(property: "address", type: "string", example: "Calle 123 #45-67, Bogotá, Colombia")]
    public string $address;

    #[OA\Property(property: "logo", type: "boolean", example: true)]
    public bool $logo;

    #[OA\Property(property: "ext", type: "string", example: "png")]
    public string $ext;

    #[OA\Property(property: "status", type: "integer", example: 1, description: "1 = Activo, 0 = Inactivo")]
    public int $status;

    #[OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $created_at;

    #[OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $updated_at;
}
