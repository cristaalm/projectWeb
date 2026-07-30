<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Alliances', description: 'Módulo de alianzas (comercios). Por ahora solo documentado el catálogo de solo lectura usado para selects — el resto del módulo (crear/editar/eliminar/estadísticas) sigue sin reconstruir tras el rediseño del esquema, ver App\Http\Controllers\OldControllers\AllianceController (fuera de alcance).')]
class AlliancesDocumentation
{
    #[OA\Get(
        path: '/alliances/catalog',
        tags: ['Alliances'],
        summary: 'Catálogo de alianzas activas',
        description: 'Lista mínima (id + name) de alianzas con status activo, ordenada por nombre — pensada para poblar selects (filtro de usuarios por alianza, formulario de crear admin_merchant/merchant/member). No requiere un rol específico, solo sesión/token activa.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Catálogo de alianzas activas.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(
                        example: 'catalogo',
                        summary: 'Catálogo',
                        value: ['success' => true, 'message' => 'Alianzas obtenidas correctamente.', 'data' => ['alliances' => [['id' => 1, 'name' => 'Alianza EcoSort Centro'], ['id' => 2, 'name' => 'Alianza EcoSort Norte']]], 'errors' => null, 'code' => 200]
                    )]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'La cuenta del usuario ya no está activa.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function catalog()
    {
    }
}
