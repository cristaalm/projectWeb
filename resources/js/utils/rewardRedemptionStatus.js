export const REDEMPTION_STATUS = [
  { value: 1, label: 'Canjeado' },
  { value: 2, label: 'Entregado' },
  { value: 3, label: 'Cancelado' },
  { value: 4, label: 'Expirado' },
]

const COLORS = {
  1: 'info',
  2: 'success',
  3: 'error',
  4: 'secondary',
}

export function redemptionStatusLabel(status) {
  return REDEMPTION_STATUS.find(item => item.value === Number(status))?.label ?? 'Desconocido'
}

export function redemptionStatusColor(status) {
  return COLORS[Number(status)] ?? 'default'
}
