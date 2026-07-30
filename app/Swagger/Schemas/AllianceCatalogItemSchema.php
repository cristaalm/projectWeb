<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AllianceCatalogItem',
    description: 'Entrada del catálogo de alianzas activas (App\Http\Controllers\AllianceController::catalog) — solo id y nombre, pensado para poblar selects (filtro/formulario de crear usuario). No es el recurso completo de Alliance.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Alianza EcoSort Centro'),
    ]
)]
class AllianceCatalogItemSchema
{
    // Contenedor de anotaciones; no se instancia.
}
