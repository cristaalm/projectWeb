<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: "IAResponse", title: "IAResponse", description: "Representa la respuesta de la IA")]
class IAResponseSchema
{
    #[OA\Property(property: "tipo_espanol", type: "string", example: "Botella de Plástico")]
    public string $tipo_espanol;

    #[OA\Property(property: "reciclable", type: "boolean", example: true)]
    public bool $reciclable;

    #[OA\Property(property: "tipo", type: "string", example: "Plástico")]
    public string $tipo;

    #[OA\Property(property: "titulo", type: "string", example: "Botella Aplastada")]
    public string $titulo;

    #[OA\Property(property: "aplastado", type: "boolean", example: true)]
    public bool $aplastado;

    #[OA\Property(property: "confianza", type: "integer", example: 95)]
    public int $confianza;

    #[OA\Property(property: "detalle", type: "string", example: "Una mano sostiene una botella de plástico transparente aplastada.")]
    public string $detalle;

    #[OA\Property(property: "success", type: "boolean", example: true)]
    public bool $success;

    #[OA\Property(property: "timestamp", type: "string", format: "date-time", example: "2025-04-01T10:00:00.000000Z")]
    public string $timestamp;
}
