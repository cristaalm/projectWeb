import { IMask } from 'vue-imask'

export function maskSixDigitCode(rawValue) {
  const mask = IMask.createMask({ mask: '000000' })

  mask.resolve(rawValue || '')

  return mask.value
}
