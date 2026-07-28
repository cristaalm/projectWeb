<?php

namespace App\Swagger\Schemas\Common;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ErrorResponse',
    description: 'Forma estándar de una respuesta de error, tal como la produce Controller::apiResponse(). En producción (APP_DEBUG=false), "errors" se reemplaza por un mensaje genérico salvo que sea un arreglo de errores de validación.',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'La solicitud no pudo ser procesada.'),
        new OA\Property(property: 'data', nullable: true, example: null),
        new OA\Property(
            property: 'errors',
            nullable: true,
            oneOf: [
                new OA\Schema(type: 'string'),
                new OA\Schema(type: 'object', additionalProperties: new OA\AdditionalProperties(
                    type: 'array',
                    items: new OA\Items(type: 'string')
                )),
            ],
            example: 'La solicitud no pudo ser procesada. Por favor revise los datos enviados.'
        ),
        new OA\Property(property: 'code', type: 'integer', example: 422),
    ]
)]
class ErrorResponseSchema
{
    // Contenedor de anotaciones; no se instancia.
}
