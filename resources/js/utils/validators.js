export function isValidEmail(value) {
  return /^[\w.+-]+@[\w-]+\.[a-z]{2,}$/i.test(value)
}
