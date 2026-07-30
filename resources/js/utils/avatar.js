const PALETTE = [
  '#EF4444', // red
  '#3B82F6', // blue
  '#10B981', // green
  '#F59E0B', // yellow
  '#8B5CF6', // purple
  '#EC4899', // pink
  '#14B8A6', // teal
  '#F97316', // orange
]

// Color determinístico por nombre — el mismo usuario siempre cae en el mismo
// color entre recargas, sin necesidad de guardar nada.
export function getAvatarColor(name) {
  if (!name) return '#6B7280'

  let hash = 0
  for (let i = 0; i < name.length; i++) {
    hash = name.charCodeAt(i) + ((hash << 5) - hash)
  }

  return PALETTE[Math.abs(hash) % PALETTE.length]
}

export function getInitials(name) {
  if (!name) return ''

  return name
    .split(' ')
    .filter(Boolean)
    .map(part => part[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}
