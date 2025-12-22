import { createI18n } from 'vue-i18n'

import esLanding from './es/landing'
import enLanding from './en/landing'

const SUPPORTED_LOCALES = ['es', 'en']

function detectLocale() {
  // 1️⃣ Preferencia guardada
  const saved = localStorage.getItem('locale')
  if (SUPPORTED_LOCALES.includes(saved)) {
    return saved
  }

  // 2️⃣ Idioma del navegador
  const browserLocale = navigator.language.split('-')[0]
  if (SUPPORTED_LOCALES.includes(browserLocale)) {
    return browserLocale
  }

  // 3️⃣ Default
  return 'es'
}

export const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  locale: detectLocale(),
  fallbackLocale: 'es',
  messages: {
    es: {
      landing: esLanding,
    },
    en: {
      landing: enLanding,
    },
  },
})
