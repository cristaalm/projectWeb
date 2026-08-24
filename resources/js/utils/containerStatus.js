export const CONTAINER_STATUS = [
  { value: 1, label: 'Activo' },
  { value: 0, label: 'Inactivo' },
]

export function containerStatusLabel(status) {
  return CONTAINER_STATUS.find(item => item.value === Number(status))?.label ?? 'Desconocido'
}

export function containerStatusColor(status) {
  return Number(status) === 1 ? 'success' : 'secondary'
}
