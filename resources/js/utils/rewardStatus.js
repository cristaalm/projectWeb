export const REWARD_STATUS = [
  { value: 0, label: 'Pendiente de revisión' },
  { value: 1, label: 'Aprobada' },
  { value: 2, label: 'Rechazada' },
  { value: 3, label: 'Pausada' },
]

const COLORS = {
  0: 'warning',
  1: 'success',
  2: 'error',
  3: 'secondary',
}

export function rewardStatusLabel(status) {
  return REWARD_STATUS.find(item => item.value === Number(status))?.label ?? 'Desconocido'
}

export function rewardStatusColor(status) {
  return COLORS[Number(status)] ?? 'default'
}
