import { useDarkModeStore } from '@/store/dark-mode'
import { computed, onMounted } from 'vue'
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
    localStorage.setItem('oldMode', globalTheme.name.value)
  }

  async function logoutMode() {
    // guardamos el ultimo thema que tubo en localStorage
    localStorage.setItem('oldMode', globalTheme.name.value)

    return new Promise(resolve => {
      setTimeout(() => {
        globalTheme.name.value = 'light'
        darkModeStore.setDarkMode(false)
        resolve()
      }, 0) // espera 1 segundo
    })
  }

  const darkMode = computed({
    get: () => darkModeStore.darkMode,
    set: value => darkModeStore.setDarkMode(value),
  })

  onMounted(() => {
    // Set the initial theme
    globalTheme.name.value = darkMode.value ? 'dark' : 'light'
    
    // comprobamos si tiene un thema guardado en localStorage
    const oldMode = localStorage.getItem('oldMode')
    if (oldMode) {
      globalTheme.name.value = oldMode
      darkModeStore.setDarkMode(oldMode === 'dark')
    }
  })

  return {
    themeName,
    changeTheme,
    darkMode,
    globalTheme,
    logoutMode,
  }
}
