<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    description: 'Representación del usuario autenticado, tal como la produce UserResource.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'avatar', type: 'string', nullable: true, example: null),
        new OA\Property(property: 'name', type: 'string', example: 'Eduardo'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Arcega'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'eduardo@example.com'),
        new OA\Property(property: 'phone', type: 'string', example: '5555555555'),
        new OA\Property(property: 'tour', type: 'boolean', description: 'Si el usuario ya completó el tour guiado de la interfaz.', example: false),
        new OA\Property(property: 'two_factor_status', type: 'boolean', description: 'Si el usuario tiene 2FA habilitado.', example: false),
        new OA\Property(property: 'code_identity', type: 'string', description: 'Código EAN-13 único de identidad del usuario.', example: 'ECOSORT-SA-001'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'deleted_at', type: 'string', format: 'date-time', nullable: true, description: 'Fecha de baja (soft-delete). Null si la cuenta está activa.'),
        new OA\Property(property: 'points_balance', type: 'integer', nullable: true, description: 'Saldo de puntos calculado (point_earnings + point_adjustments - point_redemptions). Solo presente cuando el endpoint lo calcula explícitamente, ej. el listado de administración de usuarios.', example: 120),
        new OA\Property(
            property: 'alliance',
            type: 'object',
            nullable: true,
            description: 'Presente solo si el usuario es dueño/admin (merchant) o empleado (organizationMember) de un comercio. Null en caso contrario.',
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'phone', type: 'string', nullable: true),
                new OA\Property(property: 'logo', type: 'string', nullable: true),
                new OA\Property(property: 'address', type: 'string', nullable: true),
                new OA\Property(
                    property: 'type_shop',
                    type: 'object',
                    nullable: true,
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'name', type: 'string'),
                    ]
                ),
                new OA\Property(property: 'total_points', type: 'number', nullable: true),
                new OA\Property(property: 'ext', type: 'string', nullable: true),
                new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
            ]
        ),
        new OA\Property(
            property: 'role',
            type: 'object',
            nullable: true,
            description: 'Solo presente cuando el endpoint carga la relación role.',
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string', example: 'superadmin', description: 'superadmin | moderador | admin_merchant | merchant | member'),
                new OA\Property(property: 'display_name', type: 'string', example: 'Super Administrador'),
                new OA\Property(property: 'is_active', type: 'boolean'),
            ]
        ),
    ]
)]
class UserSchema
{
    // Contenedor de anotaciones; no se instancia.
}
