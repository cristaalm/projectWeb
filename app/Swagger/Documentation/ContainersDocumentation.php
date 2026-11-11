<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Containers', description: 'CRUD administrativo de contenedores de reciclaje. Las rutas de administración están bajo /containers, protegidas por auth:sanctum + ensureUserIsActive + role:superadmin,moderador (middleware App\Http\Middleware\EnsureUserHasRole): nombre, número de serie, ubicación, coordenadas, estado activo/inactivo. La excepción es POST /containers/fill-levels, que la llama el contenedor físico con su propio token (X-Container-Token) para reportar su nivel de llenado por compartimento: la última lectura de cada compartimento queda en container_sensors y cada lectura recibida, en el historial container_fill_readings (purgado por containers:prune-fill-readings según CONTAINER_FILL_READINGS_RETENTION_MONTHS).')]
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
    public function index() {}

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
    public function store() {}

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
    public function update() {}

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
    public function destroy() {}

    #[OA\Get(
        path: '/containers/{id}/token',
        tags: ['Containers'],
        summary: 'Ver el token de API de un contenedor',
        description: 'Devuelve en claro el token con el que el contenedor físico se autentica (header X-Container-Token) en POST /scans/identify y POST /scans. El token nunca viaja en el listado ni en las respuestas de crear/editar: solo en este endpoint y en el de regenerar, bajo demanda.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token del contenedor.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'token', summary: 'Token', value: ['success' => true, 'message' => 'Token obtenido correctamente.', 'data' => ['token' => 'ect_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Contenedor no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function token() {}

    #[OA\Post(
        path: '/containers/{id}/token/regenerate',
        tags: ['Containers'],
        summary: 'Regenerar el token de API de un contenedor',
        description: 'Genera un token nuevo y lo devuelve. El anterior deja de funcionar de inmediato: el contenedor físico responderá 401 hasta que se le cargue el nuevo.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Token nuevo.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'token', summary: 'Token regenerado', value: ['success' => true, 'message' => 'Token regenerado correctamente.', 'data' => ['token' => 'ect_yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy'], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Contenedor no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function regenerateToken() {}

    #[OA\Post(
        path: '/containers/fill-levels',
        tags: ['Containers'],
        summary: 'Reportar el nivel de llenado del contenedor',
        description: 'Reporte periódico que manda el contenedor físico (cada cierto tiempo o cuando cambia el nivel, aunque nadie recicle), sobre App\Services\ContainerFillLevelService::record(). El contenedor es siempre el dueño del token. Cada lectura identifica su compartimento por `sensor_key`; si no se manda, el compartimento es el material (un compartimento por material). El primer reporte de un compartimento lo da de alta. Cada lectura se guarda en el historial; además pasa a ser el estado actual del compartimento salvo que ya exista una lectura con `measured_at` más reciente (en ese caso `applied` = false). El mismo formato de lecturas se puede mandar, opcional, en el campo `fill_levels` de POST /scans.',
        security: [['containerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['sensors'],
                properties: [
                    new OA\Property(property: 'measured_at', type: 'string', format: 'date-time', nullable: true, description: 'Hora de la medición (ISO-8601). Opcional: si falta, se usa la hora del servidor. Se rechaza si está más de 5 minutos en el futuro o tiene más de 30 días.', example: '2026-11-10T18:00:00Z'),
                    new OA\Property(
                        property: 'sensors',
                        type: 'array',
                        minItems: 1,
                        maxItems: 20,
                        items: new OA\Items(
                            type: 'object',
                            required: ['material', 'fill_level'],
                            properties: [
                                new OA\Property(property: 'material', type: 'string', description: 'Slug del material del compartimento (material_types.slug).', example: 'plastic'),
                                new OA\Property(property: 'fill_level', type: 'number', format: 'float', minimum: 0, maximum: 100, description: 'Porcentaje de llenado.', example: 72.5),
                                new OA\Property(property: 'sensor_key', type: 'string', maxLength: 50, nullable: true, description: 'Identificador del compartimento (letras, números, - y _). Solo hace falta si hay más de un compartimento del mismo material. No puede repetirse en la misma petición.', example: 'comp_1'),
                            ]
                        )
                    ),
                ],
                example: ['measured_at' => '2026-11-10T18:00:00Z', 'sensors' => [['material' => 'plastic', 'fill_level' => 72.5], ['material' => 'aluminum', 'fill_level' => 18]]]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lecturas registradas.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'measured_at', type: 'string', format: 'date-time', example: '2026-11-10T18:00:00.000000Z'),
                                new OA\Property(
                                    property: 'sensors',
                                    type: 'array',
                                    items: new OA\Items(
                                        type: 'object',
                                        properties: [
                                            new OA\Property(property: 'sensor_key', type: 'string', example: 'plastic'),
                                            new OA\Property(property: 'material', type: 'string', example: 'plastic'),
                                            new OA\Property(property: 'fill_level', type: 'number', format: 'float', example: 72.5),
                                            new OA\Property(property: 'applied', type: 'boolean', description: 'false si el compartimento ya tenía una lectura más reciente: quedó solo en el historial.', example: true),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Token de contenedor ausente o inválido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El contenedor dueño del token está inactivo.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: falta sensors, material fuera del catálogo, fill_level fuera de 0–100, compartimento repetido, o measured_at en el futuro / con más de 30 días.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function reportFillLevels() {}
}
