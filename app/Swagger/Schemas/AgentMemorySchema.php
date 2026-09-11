<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AgentMemory',
    description: 'Resumen de una interacción que Evi guarda para recordar entre visitas (App\Models\AgentMemory). Evi solo escribe content/metadata — embedding queda sin usar por ahora.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 42),
        new OA\Property(property: 'content', type: 'string', example: 'El usuario recicló 3 botellas y estuvo motivado.'),
        new OA\Property(property: 'metadata', type: 'object', nullable: true, additionalProperties: true, example: ['mood' => 'happy']),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class AgentMemorySchema
{
    // Contenedor de anotaciones; no se instancia.
}
