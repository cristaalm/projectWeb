// Espejo en el frontend de las reglas de autorización de
// App\Services\UserManagementService::assertActionAllowed() — sirve solo para
// ocultar/deshabilitar acciones en la UI; el backend es la fuente de verdad
// real y rechaza cualquier intento que se salte estos chequeos.
const STAFF_ROLES = ['superadmin', 'moderador']

function isSelf(currentUser, target) {
  return Boolean(currentUser?.id) && currentUser.id === target?.id
}

function isModeratorActingOnStaff(currentUser, target) {
  return currentUser?.role?.name === 'moderador' && STAFF_ROLES.includes(target?.role?.name)
}

export function canModifyPoints(currentUser, target) {
  if (isSelf(currentUser, target)) return true

  return !isModeratorActingOnStaff(currentUser, target)
}

export function canManageAccount(currentUser, target) {
  if (isSelf(currentUser, target)) return false

  return !isModeratorActingOnStaff(currentUser, target)
}
