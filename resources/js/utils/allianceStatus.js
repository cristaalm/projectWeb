export const ALLIANCE_STATUS = [
  { value: 1, label: 'Activo' },
  { value: 0, label: 'Pausado' },
]

export function allianceStatusLabel(status) {
  return ALLIANCE_STATUS.find(item => item.value === Number(status))?.label ?? 'Desconocido'
}

export function allianceStatusColor(status) {
  return Number(status) === 1 ? 'success' : 'secondary'
}
