// Catálogo fijo — la tabla `roles` se siembra una sola vez con estos 5 registros
// (database/migrations/2026_07_05_000006_create_roles_table.php) y no hay
// endpoint de listado, así que se hardcodea acá en vez de pedirlo al backend.
export const ROLES = [
  { id: 1, name: 'superadmin', display_name: 'Super Administrador' },
  { id: 2, name: 'moderador', display_name: 'Moderador' },
  { id: 3, name: 'admin_merchant', display_name: 'Administrador de Comercio' },
  { id: 4, name: 'merchant', display_name: 'Comerciante' },
  { id: 5, name: 'member', display_name: 'Miembro' },
]

export const CREATABLE_ROLES = ROLES.filter(role => role.name !== 'superadmin')

export const ALLIANCE_REQUIRED_ROLES = ['admin_merchant', 'merchant']

// Color por rol — solo para diferenciarlos de un vistazo en la tabla, no tiene
// otro significado (no confundir con estado activo/inactivo, que usa su propio indicador).
export const ROLE_COLORS = {
  superadmin: 'error',
  moderador: 'primary',
  admin_merchant: 'info',
  merchant: 'warning',
  member: 'secondary',
}
