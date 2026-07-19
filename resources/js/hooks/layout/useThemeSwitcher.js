import { useDarkModeStore } from '@/store/dark-mode'
import { computed } from 'vue'
import { useTheme } from 'vuetify'

export function useThemeSwitcher() {
  const { name: themeName, global: globalTheme } = useTheme()
  const darkModeStore = useDarkModeStore()
  const themes = ['light', 'dark']

  function getNextThemeName() {
    const currentIndex = themes.indexOf(globalTheme.name.value)

    return themes[(currentIndex + 1) % themes.length]
  }

  function changeTheme() {
    globalTheme.name.value = getNextThemeName()
    darkModeStore.setDarkMode(globalTheme.name.value === 'dark')
  }

  // Solo cambia el tema visual (Vuetify + la clase `dark` de Tailwind en
  // <html>), sin tocar la preferencia guardada: se usa en páginas públicas/
  // de invitado (landing, login...) que siempre se ven en claro sin importar
  // el modo elegido para el dashboard. Si persistiera, visitar estas páginas
  // borraría la preferencia oscura del usuario.
  function changeThemeToLight() {
    globalTheme.name.value = 'light'
    darkModeStore.updateDOMDarkMode(false)
  }

  const darkMode = computed({
    get: () => darkModeStore.darkMode,
    set: value => darkModeStore.setDarkMode(value),
  })

  return {
    themeName,
    changeTheme,
    changeThemeToLight,
    darkMode,
    globalTheme,
  }
}
