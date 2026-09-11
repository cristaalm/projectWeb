<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Avatar',
    description: 'Configuración/estado del avatar de Evi para un usuario (App\Models\Avatar). Se crea con defaults neutros en el primer acceso si el usuario todavía no tiene fila (App\Repositories\AvatarRepository::findOrCreateAvatar()).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 42),
        new OA\Property(property: 'preferred_language', type: 'string', example: 'es'),
        new OA\Property(property: 'selected_tone', type: 'string', example: 'amigable'),
        new OA\Property(property: 'socratic_mode', type: 'boolean', nullable: true, example: false),
        new OA\Property(property: 'feedback_enabled', type: 'boolean', nullable: true, example: true),
        new OA\Property(property: 'voice_model', type: 'string', example: 'default'),
        new OA\Property(property: 'current_mood', type: 'string', example: 'neutral'),
        new OA\Property(property: 'state', type: 'string', example: 'idle'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
class AvatarSchema
{
    // Contenedor de anotaciones; no se instancia.
}
