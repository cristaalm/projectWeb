<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AgentFeedback',
    description: 'Valoración del usuario sobre una sesión con Evi (App\Models\AgentFeedback).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 42),
        new OA\Property(property: 'thread_id', type: 'string', example: 'abc-123'),
        new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 5),
        new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'Muy buena experiencia'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class AgentFeedbackSchema
{
    // Contenedor de anotaciones; no se instancia.
}
