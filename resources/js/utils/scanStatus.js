// Espejo de App\Enums\ScanStatus.
export const SCAN_STATUS = [
  { value: 1, label: 'Éxito' },
  { value: 0, label: 'Fallo' },
]

export function scanStatusLabel(status) {
  return SCAN_STATUS.find(item => item.value === Number(status))?.label ?? 'Desconocido'
}

export function scanStatusColor(status) {
  return Number(status) === 1 ? 'success' : 'error'
}
