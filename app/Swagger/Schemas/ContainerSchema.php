<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "Container", title: "Container", description: "Representa un contenedor registrado en el sistema")]
class ContainerSchema
{
    #[OA\Property(property: "id", type: "integer", example: 1)]
    public int $id;

    #[OA\Property(property: "name", type: "string", example: "Contenedor Ejemplo")]
    public string $name;

    #[OA\Property(property: "serial_number", type: "string", example: "123456789")]
    public string $serial_number;

    #[OA\Property(property: "location", type: "string", example: "Bogotá")]
    public string $location;

    #[OA\Property(property: "latitude", type: "string", example: "4.609711")]
    public string $latitude;

    #[OA\Property(property: "longitude", type: "string", example: "-74.085772")]
    public string $longitude;

    #[OA\Property(property: "status", type: "integer", example: 1, description: "1 = Activo, 0 = Inactivo")]
    public int $status;

    #[OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $created_at;

    #[OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $updated_at;
}
