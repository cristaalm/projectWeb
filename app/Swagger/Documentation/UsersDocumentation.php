<?php

namespace App\Swagger\Documentation;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Users', description: "Administración de cuentas de usuario (crear, ajustar puntos, dar de baja/restaurar, resetear credenciales, deshabilitar 2FA) y su listado paginado. Todas las rutas están bajo /users, protegidas por auth:sanctum + ensureUserIsActive + role:superadmin,moderador (middleware App\Http\Middleware\EnsureUserHasRole) — ningún otro rol puede acceder, sin importar el resultado de negocio. Ver App\Services\UserManagementService.")]
class UsersDocumentation
{
    #[OA\Get(
        path: '/users',
        tags: ['Users'],
        summary: 'Listar usuarios (administración)',
        description: 'Listado paginado/filtrable/ordenable sobre App\Repositories\UserRepository::paginate(). "points_balance" es un valor calculado (point_earnings + point_adjustments - point_redemptions), no una columna — por eso también se puede ordenar/filtrar por él. Por defecto excluye usuarios dados de baja salvo with_trashed=true.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [
            new OA\Parameter(name: 'role', in: 'query', description: 'Nombre exacto del rol a filtrar.', schema: new OA\Schema(type: 'string', enum: ['superadmin', 'moderador', 'admin_merchant', 'merchant', 'member'])),
            new OA\Parameter(name: 'alliance_id', in: 'query', description: 'Filtra usuarios ligados (como merchant u organizationMember) a esta alianza.', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'points_min', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'points_max', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'query', in: 'query', description: 'Búsqueda libre por nombre, apellido, correo o teléfono (ilike).', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'key', in: 'query', description: 'Columna de orden — allowlist explícita, no acepta nombres de columna arbitrarios.', schema: new OA\Schema(type: 'string', enum: ['id', 'name', 'last_name', 'email', 'phone', 'created_at', 'points_balance', 'role'])),
            new OA\Parameter(name: 'order', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100, default: 15)),
            new OA\Parameter(name: 'with_trashed', in: 'query', description: 'Si es verdadero, incluye también usuarios dados de baja en el resultado.', schema: new OA\Schema(type: 'boolean', default: false)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Página de usuarios.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'pagina', summary: 'Página de resultados', value: ['success' => true, 'message' => 'Usuarios obtenidos correctamente.', 'data' => ['data' => [['id' => 1, 'name' => 'Eduardo', 'last_name' => 'Arcega', 'email' => 'eduardo@example.com', 'points_balance' => 120, 'deleted_at' => null, 'role' => ['id' => 5, 'name' => 'member', 'display_name' => 'Miembro']]], 'last_page' => 3, 'total' => 42], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación en los filtros (ej. "key" fuera de la allowlist).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function index()
    {
    }

    #[OA\Post(
        path: '/users',
        tags: ['Users'],
        summary: 'Crear usuario',
        description: "Genera una contraseña aleatoria y envía un correo de bienvenida con las credenciales (UserWelcomeNotification) — el texto de acceso varía según el rol (web+app para moderador/admin_merchant, solo app para merchant/member). No se puede crear un superadmin desde este endpoint (solo debe existir uno). alliance_id es obligatorio para admin_merchant/merchant (crea el vínculo en merchants/organization_members) y opcional para member.",
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'last_name', 'email', 'role_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'Eduardo'),
                    new OA\Property(property: 'last_name', type: 'string', maxLength: 100, example: 'Arcega'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'nuevo@ecosort.mx'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 20, nullable: true, example: '5555555555'),
                    new OA\Property(property: 'role_id', type: 'integer', description: 'Id de un rol distinto a superadmin.', example: 5),
                    new OA\Property(property: 'alliance_id', type: 'integer', nullable: true, description: 'Obligatorio si el rol es admin_merchant o merchant; opcional para member.', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuario creado.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'creado', summary: 'Usuario creado', value: ['success' => true, 'message' => 'Usuario creado correctamente.', 'data' => ['user' => ['id' => 10, 'name' => 'Eduardo', 'last_name' => 'Arcega', 'email' => 'nuevo@ecosort.mx']], 'errors' => null, 'code' => 201])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación: campos requeridos, correo duplicado, role_id = superadmin, o alliance_id faltante para un rol que lo exige.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function store()
    {
    }

    #[OA\Get(
        path: '/users/{userId}',
        tags: ['Users'],
        summary: 'Detalle de un usuario (administración)',
        description: 'Información no sensible de un usuario (incluye dados de baja) con rol, alianza y saldo de puntos calculado. Pensado para el modal de detalle del CRUD de administración.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuario encontrado.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function show()
    {
    }

    #[OA\Get(
        path: '/users/{userId}/history',
        tags: ['Users'],
        summary: 'Historial de acciones administrativas sobre un usuario',
        description: 'Combina user_account_actions (creación de cuenta, baja, restauración, reset de credenciales, deshabilitar 2FA) y point_adjustments (cambios de puntos) en una sola lista ordenada por fecha descendente, sin paginar. Cada entrada trae quién la ejecutó (actor) y, si aplica, el motivo o los puntos ajustados.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Historial del usuario.',
                content: new OA\JsonContent(
                    allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')],
                    examples: [new OA\Examples(example: 'historial', summary: 'Historial combinado', value: ['success' => true, 'message' => 'Historial obtenido correctamente.', 'data' => ['history' => [['id' => 'points_adjustment_3', 'type' => 'points_adjustment', 'action_type' => 'points_adjustment', 'label' => 'Ajuste de puntos', 'reason' => 'Bono de bienvenida', 'points' => 50, 'actor' => ['id' => 1, 'name' => 'Eduardo', 'last_name' => 'Arcega'], 'created_at' => '2026-07-29T10:00:00Z'], ['id' => 'account_action_2', 'type' => 'account_action', 'action_type' => 'user_created', 'label' => 'Cuenta creada', 'reason' => null, 'points' => null, 'actor' => ['id' => 1, 'name' => 'Eduardo', 'last_name' => 'Arcega'], 'created_at' => '2026-07-28T09:00:00Z']]], 'errors' => null, 'code' => 200])]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function history()
    {
    }

    #[OA\Post(
        path: '/users/{userId}/points',
        tags: ['Users'],
        summary: 'Modificar puntos de un usuario',
        description: 'Registra un ajuste (positivo o negativo) en point_adjustments, con quién lo hizo y el motivo. Valida que el saldo resultante nunca quede negativo. Notifica al usuario por correo con el saldo anterior/nuevo y el motivo (PointsAdjustedNotification). Rechazado si el usuario está dado de baja.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['points', 'reason'],
                properties: [
                    new OA\Property(property: 'points', type: 'integer', description: 'Ajuste a aplicar, distinto de 0 (puede ser negativo).', example: 50),
                    new OA\Property(property: 'reason', type: 'string', maxLength: 1000, example: 'Bono de bienvenida'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Puntos actualizados.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado (o está dado de baja: se excluye por defecto de la búsqueda).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación, o el ajuste dejaría el saldo en negativo.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function modifyPoints()
    {
    }

    #[OA\Post(
        path: '/users/{userId}/deactivate',
        tags: ['Users'],
        summary: 'Dar de baja a un usuario (soft-delete)',
        description: 'Marca deleted_at en el usuario (ya no puede iniciar sesión), registra la acción en user_account_actions (quién, cuándo, motivo) y notifica al usuario por correo con el motivo (AccountDeactivatedNotification).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['reason'],
                properties: [new OA\Property(property: 'reason', type: 'string', maxLength: 1000, example: 'Incumplimiento de políticas de la plataforma')]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Usuario dado de baja.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Falta el motivo, o el usuario ya está dado de baja.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function deactivate()
    {
    }

    #[OA\Post(
        path: '/users/{userId}/restore',
        tags: ['Users'],
        summary: 'Restaurar un usuario dado de baja',
        description: 'Limpia deleted_at, registra la acción en user_account_actions y notifica al usuario por correo con el motivo (AccountRestoredNotification).',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['reason'],
                properties: [new OA\Property(property: 'reason', type: 'string', maxLength: 1000, example: 'Se resolvió la situación que motivó la baja')]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuario restaurado.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Falta el motivo, o el usuario no está dado de baja.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function restore()
    {
    }

    #[OA\Post(
        path: '/users/{userId}/reset-credentials',
        tags: ['Users'],
        summary: 'Resetear las credenciales de un usuario',
        description: 'Genera una contraseña aleatoria nueva y la envía por correo (UserCredentialsNotification) — pensado como recuperación de cuenta asistida por un administrador. Registra quién y cuándo en user_account_actions (sin motivo obligatorio). Rechazado si el usuario está dado de baja.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Credenciales restablecidas.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado (o está dado de baja: se excluye por defecto de la búsqueda).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'El usuario está dado de baja.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function resetCredentials()
    {
    }

    #[OA\Post(
        path: '/users/{userId}/disable-two-factor',
        tags: ['Users'],
        summary: 'Deshabilitar el 2FA de un usuario (administrador)',
        description: 'A diferencia del autoservicio (POST /auth/disable-2fa), no exige el código TOTP/recuperación vigente — pensado para desbloquear a un usuario que perdió acceso a su app de autenticación. Limpia el secreto y los códigos de recuperación, registra la acción en user_account_actions y notifica al usuario por correo (TwoFactorDisabledByAdminNotification). Rechazado si el usuario está dado de baja.',
        security: [['sessionCookie' => []], ['bearerToken' => []]],
        parameters: [new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(
                response: 200,
                description: '2FA deshabilitado.',
                content: new OA\JsonContent(allOf: [new OA\Schema(ref: '#/components/schemas/SuccessResponse')])
            ),
            new OA\Response(response: 401, description: 'No autenticado.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'El rol del usuario autenticado no es superadmin ni moderador.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado (o está dado de baja: se excluye por defecto de la búsqueda).', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'El usuario está dado de baja.', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ]
    )]
    public function disableTwoFactor()
    {
    }
}
