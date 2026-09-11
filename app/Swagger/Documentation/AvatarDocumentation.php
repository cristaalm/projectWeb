<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Avatar', description: 'Contrato de datos para el asistente conversacional Evi (proyecto RenovaAvatar). Quien llama a estas rutas es el backend de Evi/el software del contenedor, no un usuario logueado ni la SPA de este proyecto — por eso van autenticadas por API key compartida (App\Http\Middleware\EnsureValidServiceApiKey), no por Sanctum. /avatar/identify es la llamada principal (1 sola vez por sesión, Evi cachea el resultado en RAM); las otras tres son de fase 2 (Evi escribe de vuelta memoria/feedback/estado del avatar) y son opcionales.')]
class AvatarDocumentation
{
    #[OA\Post(
        path: '/avatar/identify',
        tags: ['Avatar'],
        summary: 'Identificar usuario en un contenedor (snapshot completo para Evi)',
        description: 'Resuelve el contenedor por serial_number y el usuario por code_identity (QR/NFC), y arma un snapshot completo sobre App\Services\AvatarService::identify(). Evi calcula el nivel pedagógico internamente a partir de valid_scans — este endpoint nunca devuelve un "nivel".',
        security: [['eviApiKey' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['code_identity', 'container_serial_number'],
                properties: [
                    new OA\Property(property: 'code_identity', type: 'string', maxLength: 30, example: 'ABC123'),
                    new OA\Property(property: 'container_serial_number', type: 'string', maxLength: 255, example: 'SN-0001'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Identificación exitosa.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [new OA\Property(property: 'data', ref: '#/components/schemas/AvatarIdentifySnapshot')]
                )
            ),
            new OA\Response(response: 401, description: 'API key ausente o inválida.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'El contenedor (container_serial_number) o el usuario (code_identity) no existen.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: campos requeridos.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function identify() {}

    #[OA\Post(
        path: '/avatar/{user}/memories',
        tags: ['Avatar'],
        summary: 'Guardar un resumen de la sesión (memoria entre visitas)',
        description: 'Fase 2, opcional — pensado para llamarse al cerrar sesión con Evi. Escribe en App\Models\AgentMemory; no afecta scans ni puntos.',
        security: [['eviApiKey' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'ID del usuario (users.id).', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['content'],
                properties: [
                    new OA\Property(property: 'content', type: 'string', example: 'El usuario recicló 3 botellas y estuvo motivado.'),
                    new OA\Property(property: 'metadata', type: 'object', nullable: true, additionalProperties: true, example: ['mood' => 'happy']),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Memoria guardada.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'creada', summary: 'Memoria guardada', value: ['success' => true, 'message' => 'Memoria guardada correctamente.', 'data' => ['memory' => ['id' => 1, 'user_id' => 42, 'content' => 'El usuario recicló 3 botellas y estuvo motivado.', 'metadata' => ['mood' => 'happy'], 'created_at' => '2026-09-11T23:06:47.000000Z']], 'errors' => null, 'code' => 201])]
                )
            ),
            new OA\Response(response: 401, description: 'API key ausente o inválida.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: content requerido.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function storeMemory() {}

    #[OA\Post(
        path: '/avatar/{user}/feedback',
        tags: ['Avatar'],
        summary: 'Guardar una valoración del usuario sobre Evi',
        description: 'Fase 2, opcional — solo si el usuario da feedback durante la sesión. Escribe en App\Models\AgentFeedback.',
        security: [['eviApiKey' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'ID del usuario (users.id).', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['thread_id', 'rating'],
                properties: [
                    new OA\Property(property: 'thread_id', type: 'string', maxLength: 255, example: 'abc-123'),
                    new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 5),
                    new OA\Property(property: 'comment', type: 'string', nullable: true, example: 'Muy buena experiencia'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Valoración registrada.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [new OA\Property(property: 'data', type: 'object', properties: [new OA\Property(property: 'feedback', ref: '#/components/schemas/AgentFeedback')])]
                )
            ),
            new OA\Response(response: 401, description: 'API key ausente o inválida.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: thread_id/rating requeridos, rating fuera de 1-5.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function storeFeedback() {}

    #[OA\Patch(
        path: '/avatar/{user}/state',
        tags: ['Avatar'],
        summary: 'Actualizar el mood/estado del avatar durante la sesión',
        description: 'Fase 2, opcional — solo si la app de Evi muestra el estado del avatar. Actualiza (o crea, si el usuario todavía no tenía fila) App\Models\Avatar. Al menos uno de current_mood/state es requerido.',
        security: [['eviApiKey' => []]],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, description: 'ID del usuario (users.id).', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'current_mood', type: 'string', maxLength: 50, nullable: true, example: 'feliz'),
                    new OA\Property(property: 'state', type: 'string', maxLength: 50, nullable: true, example: 'escuchando'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Estado del avatar actualizado.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [new OA\Property(property: 'data', type: 'object', properties: [new OA\Property(property: 'avatar', ref: '#/components/schemas/Avatar')])]
                )
            ),
            new OA\Response(response: 401, description: 'API key ausente o inválida.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'No se envió current_mood ni state.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function updateState() {}
}
