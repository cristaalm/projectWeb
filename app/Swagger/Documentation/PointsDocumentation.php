<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Points', description: 'Puntos del usuario autenticado, pensados para la app móvil. Todas las rutas están bajo /points y solo exigen sesión/token activa (auth:sanctum + ensureUserIsActive), sin gate de rol: siempre responden sobre el propio usuario del token. El saldo no es una columna real: se calcula como point_earnings + point_adjustments + badge_earnings − canjes REDEEMED/DELIVERED (App\Repositories\UserRepository::pointsBalance()). No existe endpoint de escritura: los puntos los generan el flujo de scan, las insignias, los ajustes de admin y los canjes.')]
class PointsDocumentation
{
    #[OA\Get(
        path: '/points/balance',
        tags: ['Points'],
        summary: 'Total de puntos y última vez que cambiaron',
        description: 'total_points es el saldo actual. last_changed_at es la fecha del movimiento más reciente entre las mismas fuentes que componen el saldo (reciclaje, insignias, ajustes y canjes REDEEMED/DELIVERED); null si el usuario nunca ha tenido movimientos.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Saldo del usuario.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [
                        new OA\Examples(example: 'con_movimientos', summary: 'Usuario con movimientos', value: ['success' => true, 'message' => 'Saldo de puntos obtenido correctamente.', 'data' => ['total_points' => 18, 'last_changed_at' => '2026-09-20T19:21:28.000000Z'], 'errors' => null, 'code' => 200]),
                        new OA\Examples(example: 'sin_movimientos', summary: 'Usuario sin movimientos', value: ['success' => true, 'message' => 'Saldo de puntos obtenido correctamente.', 'data' => ['total_points' => 0, 'last_changed_at' => null], 'errors' => null, 'code' => 200]),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado, o la cuenta fue dada de baja.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function balance() {}

    #[OA\Get(
        path: '/points/movements',
        tags: ['Points'],
        summary: 'Últimos movimientos de puntos',
        description: 'Historial paginado del saldo, más reciente primero. Cada movimiento trae points con signo. Los canjes cancelados o expirados no aparecen (no afectan el saldo). La respuesta usa el mismo formato de paginación que el resto de listados: data.data, data.total y data.last_page.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Página de movimientos.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    properties: [new OA\Property(property: 'data', type: 'object', properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/PointMovement')),
                        new OA\Property(property: 'total', type: 'integer', example: 4),
                        new OA\Property(property: 'last_page', type: 'integer', example: 1),
                    ])],
                    examples: [new OA\Examples(
                        example: 'pagina',
                        summary: 'Página de movimientos',
                        value: ['success' => true, 'message' => 'Movimientos de puntos obtenidos correctamente.', 'data' => ['data' => [
                            ['type' => 'redemption', 'label' => 'Canje', 'description' => 'Café gratis', 'points' => -20, 'created_at' => '2026-09-20T19:21:28.000000Z'],
                            ['type' => 'adjustment', 'label' => 'Ajuste', 'description' => 'Corrección de puntos', 'points' => -4, 'created_at' => '2026-09-20T19:01:28.000000Z'],
                            ['type' => 'badge', 'label' => 'Insignia', 'description' => 'Reciclador del mes', 'points' => 20, 'created_at' => '2026-09-20T17:31:28.000000Z'],
                            ['type' => 'earning', 'label' => 'Reciclaje', 'description' => 'PET', 'points' => 5, 'created_at' => '2026-09-20T16:31:28.000000Z'],
                        ], 'last_page' => 1, 'total' => 4], 'errors' => null, 'code' => 200]
                    )]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado, o la cuenta fue dada de baja.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Parámetros de paginación inválidos.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function movements() {}

    #[OA\Get(
        path: '/points/month',
        tags: ['Points'],
        summary: 'Puntos ganados en el mes',
        description: 'Puntos ganados por actividad en el mes calendario en curso (zona horaria de la aplicación): reciclaje (point_earnings) más insignias reclamadas (badge_earnings). No incluye ajustes de admin ni descuenta canjes. Es el mismo cálculo que el points_month que devuelve /avatar/identify (Evi), por lo que ambos números siempre coinciden.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Puntos del mes.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'mes', summary: 'Mes en curso', value: ['success' => true, 'message' => 'Puntos del mes obtenidos correctamente.', 'data' => ['month' => '2026-09', 'points_month' => 35], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado, o la cuenta fue dada de baja.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function month() {}
}
