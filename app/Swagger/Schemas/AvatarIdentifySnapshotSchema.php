<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AvatarIdentifySnapshot',
    description: 'Snapshot completo que Evi carga una sola vez al identificarse en el contenedor (App\Services\AvatarService::identify()) y cachea en RAM durante toda la sesión — no vuelve a consultar por turno. Evi calcula el nivel pedagógico internamente a partir de valid_scans (umbrales 0-10/11-25/>25); esta API nunca devuelve un "nivel".',
    properties: [
        new OA\Property(
            property: 'container',
            type: 'object',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'serial_number', type: 'string', example: 'SN-0001'),
            ]
        ),
        new OA\Property(property: 'user_id', type: 'integer', example: 42),
        new OA\Property(property: 'code_identity', type: 'string', example: 'ABC123'),
        new OA\Property(property: 'name', type: 'string', example: 'Juan'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Pérez'),
        new OA\Property(property: 'total_points', type: 'integer', description: 'Saldo calculado (App\Repositories\UserRepository::pointsBalance()), no una columna real.', example: 350),
        new OA\Property(property: 'valid_scans', type: 'integer', description: 'Reciclajes válidos de por vida (COUNT(scans WHERE scan_status = SUCCESS)).', example: 18),
        new OA\Property(
            property: 'streak',
            type: 'object',
            properties: [
                new OA\Property(property: 'current_streak', type: 'integer', example: 4),
                new OA\Property(property: 'best_streak', type: 'integer', example: 9),
                new OA\Property(property: 'streak_status', type: 'boolean', example: true),
            ]
        ),
        new OA\Property(
            property: 'prefs',
            type: 'object',
            description: 'Preferencias del avatar (App\Models\Avatar), creado con defaults neutros la primera vez si el usuario no tenía fila todavía.',
            properties: [
                new OA\Property(property: 'tone', type: 'string', example: 'amigable'),
                new OA\Property(property: 'socratic_mode', type: 'boolean', example: false),
                new OA\Property(property: 'lang', type: 'string', example: 'es'),
                new OA\Property(property: 'voice', type: 'string', example: 'default'),
                new OA\Property(property: 'feedback', type: 'boolean', example: true),
            ]
        ),
        new OA\Property(property: 'tour', type: 'boolean', description: 'true si el usuario ya vio el onboarding (users.tour).', example: false),
        new OA\Property(property: 'points_month', type: 'integer', description: 'Puntos ganados (point_earnings) en el mes calendario actual.', example: 40),
        new OA\Property(
            property: 'badge',
            nullable: true,
            type: 'object',
            description: 'Insignia de mayor nivel ya completada este mes. null si no hay ninguna.',
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Reciclador Bronce'),
                new OA\Property(property: 'recycles_remaining', type: 'integer', example: 0),
            ]
        ),
        new OA\Property(
            property: 'next_badge',
            nullable: true,
            type: 'object',
            description: 'Siguiente insignia no completada este mes. null si no queda ninguna pendiente.',
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Reciclador Plata'),
                new OA\Property(property: 'recycles_required', type: 'integer', example: 25),
                new OA\Property(property: 'recycles_remaining', type: 'integer', example: 7),
            ]
        ),
        new OA\Property(
            property: 'recent_memories',
            type: 'array',
            description: 'Hasta 3 resúmenes más recientes de App\Models\AgentMemory.content, más nuevo primero.',
            items: new OA\Items(type: 'string'),
            example: ['El usuario recicló 3 botellas y estuvo motivado.']
        ),
        new OA\Property(property: 'alliance_id', type: 'integer', nullable: true, description: 'Alianza actual del usuario (User::currentAlliance()), vía merchant u organization_member.', example: null),
        new OA\Property(property: 'role_id', type: 'integer', example: 1),
        new OA\Property(
            property: 'verification_status',
            type: 'string',
            nullable: true,
            enum: ['PENDING', 'APPROVED', 'REJECTED', 'EMPTY', null],
            description: 'Nombre del case de App\Enums\VerificationStatus. Siempre null por ahora — la tabla identity_verifications no tiene migración en este esquema.',
            example: null
        ),
    ]
)]
class AvatarIdentifySnapshotSchema
{
    // Contenedor de anotaciones; no se instancia.
}
