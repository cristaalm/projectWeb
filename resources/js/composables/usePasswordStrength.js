// Mismos 4 criterios que exige el backend (Password::min(8)->mixedCase()->numbers()->symbols()
// en UpdatePasswordRequest) — el frontend nunca debe prometer "segura" algo que el backend
// vaya a rechazar.
const CRITERIA = [
  { key: 'length', label: 'Al menos 8 caracteres', test: value => value.length >= 8 },
  { key: 'lowercase', label: 'Una minúscula', test: value => /[a-z]/.test(value) },
  { key: 'uppercase', label: 'Una mayúscula', test: value => /[A-Z]/.test(value) },
  { key: 'number', label: 'Un número', test: value => /\d/.test(value) },
  { key: 'symbol', label: 'Un símbolo (ej. !@#$%)', test: value => /[^a-z0-9]/i.test(value) },
]

const LABELS = ['Muy débil', 'Débil', 'Media', 'Fuerte', 'Muy fuerte']

export function usePasswordStrength(password) {
  const value = password ?? ''
  const missing = CRITERIA.filter(criterion => !criterion.test(value))
  const passed = CRITERIA.length - missing.length
  const score = value.length === 0 ? 0 : Math.max(1, Math.round((passed / CRITERIA.length) * 4))

  return {
    score,
    label: LABELS[score],
    isValid: missing.length === 0,
    missing: missing.map(criterion => criterion.label),
  }
}
