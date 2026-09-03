<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Alliances', description: 'CRUD administrativo de alianzas (comercios). Las rutas de administración están bajo /alliances, protegidas por auth:sanctum + ensureUserIsActive + role:superadmin,moderador (App\Http\Middleware\EnsureUserHasRole); el catálogo (/alliances/catalog) solo exige sesión/token activa. El campo has_exclusive_rewards controla si la alianza acepta enlazar usuarios con rol member — ver App\Http\Requests\Users\CreateUserRequest.')]
class AlliancesDocumentation
{
    #[OA\Get(
        path: '/alliances/catalog',
        tags: ['Alliances'],
        summary: 'Catálogo de alianzas activas',
        description: 'Lista mínima (id + name + has_exclusive_rewards) de alianzas con status activo, ordenada por nombre — pensada para poblar selects (filtro de usuarios por alianza, formulario de crear admin_merchant/merchant/member; el frontend filtra client-side por has_exclusive_rewards cuando el rol elegido es member). No requiere un rol específico, solo sesión/token activa.',
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
                        value: ['success' => true, 'message' => 'Alianzas obtenidas correctamente.', 'data' => ['alliances' => [['id' => 1, 'name' => 'Alianza EcoSort Centro', 'has_exclusive_rewards' => true], ['id' => 2, 'name' => 'Alianza EcoSort Norte', 'has_exclusive_rewards' => false]]], 'errors' => null, 'code' => 200]
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

    #[OA\Get(
        path: '/alliances',
        tags: ['Alliances'],
        summary: 'Listar alianzas (administración)',
        description: 'Listado paginado/filtrable/ordenable sobre App\Repositories\AllianceRepository::paginate(), con eager-load de la categoría (type_shop).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'integer', enum: [0, 1])),
            new OA\Parameter(name: 'type_shop_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda libre por nombre, contacto, correo, teléfono o dirección (ilike).', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'key', in: 'query', description: 'Columna de orden — allowlist explícita.', schema: new OA\Schema(type: 'string', enum: ['id', 'name', 'contact_name', 'contact_email', 'phone', 'type_shop_id', 'status', 'created_at'])),
            new OA\Parameter(name: 'order', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Página de alianzas.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function index()
    {
    }

    #[OA\Post(
        path: '/alliances',
        tags: ['Alliances'],
        summary: 'Crear alianza',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'contact_name', 'contact_email', 'phone', 'address', 'type_shop_id', 'has_exclusive_rewards', 'status'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'Alianza EcoSort Centro'),
                    new OA\Property(property: 'contact_name', type: 'string', maxLength: 100, example: 'Juan Pérez'),
                    new OA\Property(property: 'contact_email', type: 'string', format: 'email', maxLength: 100, example: 'juan@ejemplo.com'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 20, example: '5551234567'),
                    new OA\Property(property: 'address', type: 'string', maxLength: 255, example: 'Av. Siempre Viva 123'),
                    new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true, example: 19.4326),
                    new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true, example: -99.1332),
                    new OA\Property(property: 'type_shop_id', type: 'integer', example: 1),
                    new OA\Property(property: 'has_exclusive_rewards', type: 'boolean', example: false),
                    new OA\Property(property: 'status', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Alianza creada.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: campos requeridos o type_shop_id inexistente.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function store()
    {
    }

    #[OA\Put(
        path: '/alliances/{id}',
        tags: ['Alliances'],
        summary: 'Actualizar alianza',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'contact_name', 'contact_email', 'phone', 'address', 'type_shop_id', 'has_exclusive_rewards', 'status'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 150),
                    new OA\Property(property: 'contact_name', type: 'string', maxLength: 100),
                    new OA\Property(property: 'contact_email', type: 'string', format: 'email', maxLength: 100),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 20),
                    new OA\Property(property: 'address', type: 'string', maxLength: 255),
                    new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true),
                    new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true),
                    new OA\Property(property: 'type_shop_id', type: 'integer'),
                    new OA\Property(property: 'has_exclusive_rewards', type: 'boolean'),
                    new OA\Property(property: 'status', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Alianza actualizada.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Alianza no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: campos requeridos o type_shop_id inexistente.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function update()
    {
    }

    #[OA\Delete(
        path: '/alliances/{id}',
        tags: ['Alliances'],
        summary: 'Eliminar alianza',
        description: 'Eliminación permanente. merchants.alliance_id y organization_members.alliance_id son FK ON DELETE RESTRICT — si la alianza tiene comercios o miembros vinculados, la eliminación falla con 422. rewards.alliance_id sí es ON DELETE CASCADE: si la alianza tiene recompensas creadas, se eliminan en cascada sin aviso adicional (riesgo documentado, sin guarda extra en esta versión — el módulo de Rewards todavía no está reconstruido).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Alianza eliminada.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Alianza no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'La alianza tiene comercios o miembros vinculados (merchants/organization_members).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function destroy()
    {
    }

    #[OA\Post(
        path: '/alliances/{id}/logo',
        tags: ['Alliances'],
        summary: 'Subir/reemplazar el logo de la alianza',
        description: 'multipart/form-data. Reemplaza el logo anterior si existía (se borra del disco) y guarda el nuevo en alliances/alliance_{id}/logo.{ext}, persistiendo la ruta relativa en logo_url.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['logo'],
                    properties: [
                        new OA\Property(property: 'logo', type: 'string', format: 'binary', description: 'jpeg, png, jpg o webp, máximo 2MB.'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logo actualizado.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Alianza no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Archivo inválido (formato o tamaño).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function uploadLogo()
    {
    }

    #[OA\Delete(
        path: '/alliances/{id}/logo',
        tags: ['Alliances'],
        summary: 'Quitar el logo de la alianza',
        description: 'Borra el archivo del disco y limpia logo_url. La alianza vuelve a mostrarse con las iniciales de su nombre en el frontend.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logo eliminado.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Alianza no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function deleteLogo()
    {
    }
}
