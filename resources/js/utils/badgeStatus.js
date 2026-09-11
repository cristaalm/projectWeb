export const BADGE_STATUS = [
  { value: 1, label: 'Activa' },
  { value: 0, label: 'Inactiva' },
]

export function badgeStatusLabel(status) {
  return BADGE_STATUS.find(item => item.value === Number(status))?.label ?? 'Desconocido'
}

export function badgeStatusColor(status) {
  return Number(status) === 1 ? 'success' : 'secondary'
}
