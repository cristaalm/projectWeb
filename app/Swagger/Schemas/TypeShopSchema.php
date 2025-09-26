<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "TypeShop", title: "TypeShop", description: "Representa un tipo de comercio registrado en el sistema")]
class TypeShopSchema
{
    #[OA\Property(property: "id", type: "integer", example: 1)]
    public int $id;

    #[OA\Property(property: "name", type: "string", example: "Empresa Ejemplo S.A.")]
    public string $name;

    #[OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $created_at;

    #[OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $updated_at;
}
