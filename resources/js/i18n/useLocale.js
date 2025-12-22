import { useI18n } from 'vue-i18n'

export function useLocale() {
  const { locale } = useI18n()

  const setLocale = lang => {
    locale.value = lang
    localStorage.setItem('locale', lang)
    document.documentElement.lang = lang
  }

  return {
    locale,
    setLocale,
  }
}
