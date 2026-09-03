<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'TypeShop', description: 'CRUD administrativo de categorías de comercio (App\Models\TypeShop) — clasifican a las alianzas (ej. Supermercado, Farmacia). Las rutas de administración están bajo /type-shop, protegidas por auth:sanctum + ensureUserIsActive + role:superadmin,moderador; el catálogo (/type-shop/catalog) solo exige sesión/token activa.')]
class TypeShopDocumentation
{
    #[OA\Get(
        path: '/type-shop/catalog',
        tags: ['TypeShop'],
        summary: 'Catálogo de categorías',
        description: 'Lista completa (activas e inactivas, con is_active) ordenada por nombre — pensada para poblar el select de categoría en el formulario de Alianza. Incluye inactivas para no perder la referencia visual de una alianza ya ligada a una categoría desactivada.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Catálogo de categorías.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(
                        example: 'catalogo',
                        summary: 'Catálogo',
                        value: ['success' => true, 'message' => 'Categorías obtenidas correctamente.', 'data' => ['type_shops' => [['id' => 1, 'name' => 'Supermercado', 'is_active' => true]]], 'errors' => null, 'code' => 200]
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
        path: '/type-shop',
        tags: ['TypeShop'],
        summary: 'Listar categorías (administración)',
        description: 'Listado paginado/filtrable/ordenable sobre App\Repositories\TypeShopRepository::paginate().',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'is_active', in: 'query', schema: new OA\Schema(type: 'integer', enum: [0, 1])),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda libre por nombre (ilike).', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'key', in: 'query', description: 'Columna de orden — allowlist explícita.', schema: new OA\Schema(type: 'string', enum: ['id', 'name', 'is_active', 'created_at'])),
            new OA\Parameter(name: 'order', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Página de categorías.',
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
        path: '/type-shop',
        tags: ['TypeShop'],
        summary: 'Crear categoría',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'is_active'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 150, example: 'Supermercado'),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Categoría creada.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: campos requeridos o nombre duplicado (case-insensitive).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function store()
    {
    }

    #[OA\Put(
        path: '/type-shop/{id}',
        tags: ['TypeShop'],
        summary: 'Actualizar categoría',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'is_active'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 150),
                    new OA\Property(property: 'is_active', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Categoría actualizada.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Categoría no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: campos requeridos o nombre duplicado (case-insensitive).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function update()
    {
    }

    #[OA\Delete(
        path: '/type-shop/{id}',
        tags: ['TypeShop'],
        summary: 'Eliminar categoría',
        description: 'Eliminación permanente. alliances.type_shop_id es FK ON DELETE RESTRICT — si la categoría tiene alianzas vinculadas, la eliminación falla con 422.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Categoría eliminada.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Categoría no encontrada.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'La categoría tiene alianzas vinculadas.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function destroy()
    {
    }
}
