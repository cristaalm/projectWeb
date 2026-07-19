<script setup>
// path: App.vue
import ToastNotification from '@/components/Base/ToastNotification'
import VDialogComponent from '@/components/Base/VAlertDialog/VAlertDialog.vue'
import { useDarkModeStore } from '@/store/dark-mode'
import { onMounted } from 'vue'
import { useTheme } from 'vuetify'

const { global: globalTheme } = useTheme()
const darkModeStore = useDarkModeStore()

// Único punto donde se sincroniza el tema de Vuetify con la preferencia
// persistida (dark-mode store) al arrancar la app — independiente de si hay
// sesión o no, y sin repetirse en cada componente que use useThemeSwitcher.
onMounted(() => {
  globalTheme.name.value = darkModeStore.darkMode ? 'dark' : 'light'
})
</script>

<template>
  <VApp>
    <ToastNotification />
    <VDialogComponent />
    <RouterView />
  </VApp>
</template>
