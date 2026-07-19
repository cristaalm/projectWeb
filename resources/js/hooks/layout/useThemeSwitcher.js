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

  function changeThemeToLight() {
    globalTheme.name.value = 'light'
    darkModeStore.setDarkMode(false)
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
