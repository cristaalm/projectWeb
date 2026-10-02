// Ícono de cada log, por su clave (App\Services\SystemLogService::sources()).
// Una clave nueva que no esté aquí cae al ícono genérico.
const SOURCE_ICONS = {
  'laravel': 'bx-code-alt',
  'supervisor': 'bx-server',
  'scheduler': 'bx-time-five',
  'scheduler-error': 'bx-time-five',
  'queue': 'bx-list-ul',
  'queue-error': 'bx-list-ul',
  'apache': 'bx-globe',
  'apache-error': 'bx-globe',
}

export function systemLogIcon(source) {
  return SOURCE_ICONS[source] ?? 'bx-file'
}

export const SYSTEM_LOG_LINES_OPTIONS = [
  { value: 50, title: '50 líneas' },
  { value: 200, title: '200 líneas' },
  { value: 500, title: '500 líneas' },
  { value: 1000, title: '1000 líneas' },
]

export const DEFAULT_SYSTEM_LOG_LINES = 200

export function formatBytes(bytes) {
  if (bytes === null || bytes === undefined) return ''
  if (bytes < 1024) return `${bytes} B`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`

  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

// Cada log marca la gravedad a su manera: Laravel ("production.ERROR"),
// supervisor ("exited:", "FATAL state"), Apache ("[core:error]") y PHP
// ("PHP Fatal error"). Se distinguen mayúsculas para no pintar de rojo
// cualquier línea que solo mencione la palabra "error".
const ERROR_PATTERN = /\b(?:ERROR|CRITICAL|ALERT|EMERGENCY|FATAL|FAIL|FAILED)\b|exited: |:(?:error|crit|alert|emerg)\]|PHP (?:Fatal|Parse) error/
const WARNING_PATTERN = /\b(?:WARNING|WARN|NOTICE)\b|:warn\]|PHP (?:Warning|Notice|Deprecated)/

// Líneas de un stack trace de PHP ("#12 /ruta/Archivo.php(34): ...").
const TRACE_PATTERN = /^#\d+ /

// Nivel de una línea para colorearla. Es solo una ayuda visual sobre texto
// libre: cada log tiene su propio formato y no todos traen nivel.
export function systemLogLineLevel(line) {
  if (TRACE_PATTERN.test(line)) return 'trace'
  if (ERROR_PATTERN.test(line)) return 'error'
  if (WARNING_PATTERN.test(line)) return 'warning'

  return 'default'
}
