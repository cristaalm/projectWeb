// Espejo de App\Services\DashboardService::PERIODS.
export const DASHBOARD_PERIODS = [
  { value: 'last_7_days', title: 'Últimos 7 días' },
  { value: 'last_30_days', title: 'Últimos 30 días' },
  { value: 'this_month', title: 'Este mes' },
  { value: 'last_month', title: 'Mes anterior' },
  { value: 'this_year', title: 'Este año' },
  { value: 'last_12_months', title: 'Últimos 12 meses' },
]

export const DEFAULT_DASHBOARD_PERIOD = 'last_30_days'

// Las fechas del panel llegan como día calendario (Y-m-d) o mes (Y-m), sin
// hora: se construyen en hora local para que no se recorran un día al
// interpretarse como UTC.
function toLocalDate(value) {
  const [year, month, day = 1] = value.split('-').map(Number)

  return new Date(year, month - 1, day)
}

const dayFormatter = new Intl.DateTimeFormat('es-MX', { day: 'numeric', month: 'short' })
const dayWithYearFormatter = new Intl.DateTimeFormat('es-MX', { day: 'numeric', month: 'short', year: 'numeric' })
const monthFormatter = new Intl.DateTimeFormat('es-MX', { month: 'short', year: 'numeric' })

export function formatDashboardDay(value) {
  return dayFormatter.format(toLocalDate(value))
}

export function formatDashboardMonth(value) {
  return monthFormatter.format(toLocalDate(value))
}

export function formatDashboardRange(from, to) {
  if (!from || !to) return ''

  return `${dayWithYearFormatter.format(toLocalDate(from))} – ${dayWithYearFormatter.format(toLocalDate(to))}`
}

const numberFormatter = new Intl.NumberFormat('es-MX')

export function formatDashboardNumber(value) {
  return numberFormatter.format(value ?? 0)
}
