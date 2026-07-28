<?php

namespace App\Swagger\Schemas\Common;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SuccessResponse',
    description: 'Forma estándar de una respuesta exitosa, tal como la produce Controller::apiResponse().',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true),
        new OA\Property(property: 'message', type: 'string', example: 'Operación realizada correctamente.'),
        new OA\Property(property: 'data', type: 'object', nullable: true),
        new OA\Property(property: 'errors', nullable: true, example: null),
        new OA\Property(property: 'code', type: 'integer', example: 200),
    ]
)]
class SuccessResponseSchema
{
    // Contenedor de anotaciones; no se instancia.
}
