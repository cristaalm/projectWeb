export const BADGE_TIER_COLORS = {
  brote: '#7CB88F',
  musgo: '#4C8C5A',
  bosque: '#2F5F3F',
  follaje: '#1B3A2B',
}

export function badgeTier(recyclesRequired) {
  const n = Number(recyclesRequired)
  if (n < 10) return 'brote'
  if (n < 25) return 'musgo'
  if (n < 50) return 'bosque'
  
  return 'follaje'
}

export function badgeTierColor(recyclesRequired) {
  return BADGE_TIER_COLORS[badgeTier(recyclesRequired)]
}

export function badgeTierContrast(recyclesRequired) {
  return ['bosque', 'follaje'].includes(badgeTier(recyclesRequired)) ? '#FFFFFF' : '#1B3A2B'
}

export const CURATED_BADGE_ICONS = [
  'bx-recycle',
  'bx-leaf',
  'bx-bxs-tree',
  'bx-water',
  'bx-world',
  'bx-medal',
  'bx-trophy',
  'bx-award',
  'bx-badge-check',
  'bx-sun',
]

export const DEFAULT_BADGE_ICON = 'bx-medal'
