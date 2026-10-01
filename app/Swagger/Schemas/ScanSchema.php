<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Scan',
    description: 'Escaneo de reciclaje (App\Models\Scan) con la forma de App\Http\Resources\ScanResource. `user` y `container` solo vienen en el listado administrativo (GET /scans), no en la respuesta al contenedor (POST /scans).',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 12),
        new OA\Property(property: 'event_id', type: 'string', format: 'uuid', nullable: true, description: 'Identificador único generado por el contenedor para este escaneo.', example: 'ad62c108-7c63-451d-af7e-59be2b426346'),
        new OA\Property(property: 'status', type: 'integer', enum: [0, 1], description: '1 = éxito (otorgó puntos), 0 = fallo (material rechazado) — App\Enums\ScanStatus.', example: 1),
        new OA\Property(property: 'status_label', type: 'string', example: 'Éxito'),
        new OA\Property(
            property: 'material',
            type: 'object',
            nullable: true,
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'slug', type: 'string', example: 'plastic'),
                new OA\Property(property: 'name', type: 'string', example: 'Plástico'),
            ]
        ),
        new OA\Property(property: 'points_awarded', type: 'integer', description: 'Copia de los puntos del material al momento del escaneo; 0 si fue rechazado.', example: 15),
        new OA\Property(property: 'description', type: 'string', nullable: true, description: 'Motivo del rechazo cuando status = 0; null en un escaneo válido.', example: null),
        new OA\Property(property: 'image_url', type: 'string', nullable: true, description: 'URL absoluta de la imagen, o null si el contenedor no la envió.', example: null),
        new OA\Property(property: 'scanned_at', type: 'string', format: 'date-time', example: '2026-10-01T23:34:07.000000Z'),
        new OA\Property(
            property: 'user',
            type: 'object',
            nullable: true,
            description: 'Solo en el listado administrativo. Incluye usuarios dados de baja.',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 42),
                new OA\Property(property: 'name', type: 'string', example: 'Juan'),
                new OA\Property(property: 'last_name', type: 'string', example: 'Pérez'),
                new OA\Property(property: 'email', type: 'string', example: 'juan@example.com'),
                new OA\Property(property: 'code_identity', type: 'string', example: '5760307480459'),
                new OA\Property(property: 'avatar', type: 'string', nullable: true, example: null),
            ]
        ),
        new OA\Property(
            property: 'container',
            type: 'object',
            nullable: true,
            description: 'Solo en el listado administrativo.',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Contenedor Parque Central'),
                new OA\Property(property: 'serial_number', type: 'string', example: 'SN-0001'),
            ]
        ),
    ]
)]
class ScanSchema
{
    // Contenedor de anotaciones; no se instancia.
}
