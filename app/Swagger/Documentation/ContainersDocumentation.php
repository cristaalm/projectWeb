<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Containers', description: 'CRUD administrativo de contenedores de reciclaje. Todas las rutas están bajo /containers, protegidas por auth:sanctum + ensureUserIsActive + role:superadmin,moderador (middleware App\Http\Middleware\EnsureUserHasRole). Alcance de este módulo: solo administración básica (nombre, número de serie, ubicación, coordenadas, estado activo/inactivo) — el tracking de sensores/nivel de llenado (tabla container_sensors) y el endpoint IoT de actualización de capacidad quedan fuera de alcance a propósito.')]
class ContainersDocumentation
{
    #[OA\Get(
        path: '/containers',
        tags: ['Containers'],
        summary: 'Listar contenedores (administración)',
        description: 'Listado paginado/filtrable/ordenable sobre App\Repositories\ContainerRepository::paginate().',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'integer', enum: [0, 1])),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda libre por nombre, número de serie o ubicación (ilike).', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'key', in: 'query', description: 'Columna de orden — allowlist explícita.', schema: new OA\Schema(type: 'string', enum: ['id', 'name', 'serial_number', 'location', 'status', 'created_at'])),
            new OA\Parameter(name: 'order', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Página de contenedores.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'pagina', summary: 'Página de resultados', value: ['success' => true, 'message' => 'Contenedores obtenidos correctamente.', 'data' => ['data' => [['id' => 1, 'name' => 'Contenedor Parque Central', 'serial_number' => 'SN-0001', 'location' => 'Parque Central', 'status' => 1]], 'last_page' => 1, 'total' => 1], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function index()
    {
    }

    #[OA\Post(
        path: '/containers',
        tags: ['Containers'],
        summary: 'Crear contenedor',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'serial_number', 'location', 'status'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Contenedor Parque Central'),
                    new OA\Property(property: 'serial_number', type: 'string', maxLength: 255, example: 'SN-0001'),
                    new OA\Property(property: 'location', type: 'string', maxLength: 255, example: 'Parque Central'),
                    new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true, example: 19.4326),
                    new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true, example: -99.1332),
                    new OA\Property(property: 'status', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Contenedor creado.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'creado', summary: 'Contenedor creado', value: ['success' => true, 'message' => 'Contenedor creado correctamente.', 'data' => ['container' => ['id' => 1, 'name' => 'Contenedor Parque Central', 'serial_number' => 'SN-0001']], 'errors' => null, 'code' => 201])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: campos requeridos o número de serie duplicado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function store()
    {
    }

    #[OA\Put(
        path: '/containers/{id}',
        tags: ['Containers'],
        summary: 'Actualizar contenedor',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'serial_number', 'location', 'status'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255),
                    new OA\Property(property: 'serial_number', type: 'string', maxLength: 255),
                    new OA\Property(property: 'location', type: 'string', maxLength: 255),
                    new OA\Property(property: 'latitude', type: 'number', format: 'float', nullable: true),
                    new OA\Property(property: 'longitude', type: 'number', format: 'float', nullable: true),
                    new OA\Property(property: 'status', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Contenedor actualizado.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Contenedor no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: campos requeridos o número de serie duplicado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function update()
    {
    }

    #[OA\Delete(
        path: '/containers/{id}',
        tags: ['Containers'],
        summary: 'Eliminar contenedor',
        description: 'Eliminación permanente (no hay soft-delete en containers). La FK de scans.container_id es ON DELETE CASCADE, así que borrar un contenedor también borra sus scans asociados.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Contenedor eliminado.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Contenedor no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function destroy()
    {
    }
}
