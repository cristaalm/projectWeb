import { defineStore } from 'pinia'

export const useDarkModeStore = defineStore('darkMode', {
  state: () => ({
    // Inicializa el estado en función de lo que esté en localStorage
    value: localStorage.getItem('darkMode') === 'true' || false,
  }),
  getters: {
    // Getter para devolver el estado del modo oscuro (usado en el v-model)
    darkMode(state) {
      return state.value
    },
  },
  actions: {
    // Acción para establecer directamente el modo oscuro
    setDarkMode(darkMode) {
      localStorage.setItem('darkMode', darkMode.toString())
      this.value = darkMode

      // Actualizar el DOM para reflejar el modo oscuro
      this.updateDOMDarkMode(darkMode)
    },

    // Acción para alternar el modo oscuro
    toggleDarkMode() {
      this.setDarkMode(!this.value)
    },

    // Actualizar la clase 'dark' en el HTML
    updateDOMDarkMode(isDarkMode) {
      const htmlElement = document.documentElement
      if (isDarkMode) {
        htmlElement.classList.add('dark')
      } else {
        htmlElement.classList.remove('dark')
      }
    },

    // Cargar el estado del modo oscuro al inicializar la aplicación
    loadDarkMode() {
      const darkMode = localStorage.getItem('darkMode') === 'true'

      this.setDarkMode(darkMode)
    },
  },
})

// Llamar a esta función para cargar el estado inicial cuando se inicie la aplicación
const darkModeStore = useDarkModeStore()

darkModeStore.loadDarkMode()
