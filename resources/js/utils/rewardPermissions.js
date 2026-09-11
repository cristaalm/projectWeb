// Espejo en el frontend de las reglas de autorización de
// App\Services\RewardService / App\Services\RedemptionService — sirve solo
// para ocultar/deshabilitar acciones en la UI; el backend es la fuente de
// verdad real y rechaza cualquier intento que se salte estos chequeos.
const STAFF_ROLES = ['superadmin', 'moderador']

export function isStaff(user) {
  return STAFF_ROLES.includes(user?.role?.name)
}

function ownsAlliance(user, allianceId) {
  return Boolean(user?.alliance?.id) && user.alliance.id === allianceId
}

export function canManageReward(user, reward) {
  return isStaff(user) || ownsAlliance(user, reward?.alliance_id)
}

export function canApproveOrReject(user, reward) {
  return isStaff(user) && Number(reward?.status) === 0
}

export function canPause(user, reward) {
  return canManageReward(user, reward) && Number(reward?.status) === 1
}

export function canReactivate(user, reward) {
  return canManageReward(user, reward) && Number(reward?.status) === 3
}

export function canManageRedemption(user, redemption) {
  return isStaff(user) || ownsAlliance(user, redemption?.alliance_id)
}

export function canDeliver(user, redemption) {
  return canManageRedemption(user, redemption) && Number(redemption?.status) === 1
}
