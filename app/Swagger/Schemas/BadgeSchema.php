<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Badge',
    description: 'Insignia del catálogo de reciclaje (App\Models\Badge).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Reciclador del mes'),
        new OA\Property(property: 'icon', type: 'string', nullable: true, enum: ['bx-recycle', 'bx-leaf', 'bx-bxs-tree', 'bx-water', 'bx-world', 'bx-medal', 'bx-trophy', 'bx-award', 'bx-badge-check', 'bx-sun'], example: 'bx-recycle', description: 'Clave del catálogo cerrado (App\\Support\\BadgeIcons, ver GET /badges/icons). null = icono predeterminado (bx-medal). Son iconos de Boxicons; para pintarlos conviene usar icon_url.'),
        new OA\Property(property: 'recycles_required', type: 'integer', description: 'Reciclajes válidos requeridos en un mes calendario para completar la insignia.', example: 10),
        new OA\Property(property: 'points_awarded', type: 'integer', description: 'Puntos acreditados al reclamar la insignia.', example: 50),
        new OA\Property(property: 'status', type: 'boolean', example: true),
        new OA\Property(property: 'icon_url', type: 'string', example: 'https://api.ejemplo.com/icons/badges/bx-recycle.svg', description: 'URL absoluta del SVG del icono (24x24, fill="currentColor"); nunca null: si icon es null apunta al predeterminado. Requiere APP_URL correcto en el entorno.'),
        new OA\Property(property: 'tier', ref: '#/components/schemas/BadgeTier', nullable: true, description: 'Nivel visual según recycles_required: el emblema se pinta con fondo tier.background y el icono teñido con tier.color.'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'BadgeTier',
    description: 'Nivel visual de una insignia (App\\Support\\BadgeTiers), solo presentación: aplica el último nivel cuyo min_recycles alcanza recycles_required.',
    properties: [
        new OA\Property(property: 'key', type: 'string', enum: ['brote', 'musgo', 'bosque', 'follaje'], example: 'musgo'),
        new OA\Property(property: 'label', type: 'string', example: 'Musgo'),
        new OA\Property(property: 'min_recycles', type: 'integer', example: 10),
        new OA\Property(property: 'background', type: 'string', description: 'Color de fondo del emblema (hex).', example: '#4C8C5A'),
        new OA\Property(property: 'color', type: 'string', description: 'Color del icono sobre ese fondo (hex), elegido por contraste.', example: '#1B3A2B'),
    ]
)]
class BadgeSchema
{
    // Contenedor de anotaciones; no se instancia.
}
