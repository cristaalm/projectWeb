<script setup>
import { useThemeSwitcher } from '@/hooks/layout/useThemeSwitcher'
import { onMounted } from 'vue'

// Login, forgot/reset password, verify-2fa y logout siempre se ven en tema
// claro, sin importar la preferencia guardada para el dashboard.
const { changeThemeToLight } = useThemeSwitcher()

onMounted(() => {
  changeThemeToLight()
})
</script>

<template>
  <div
    class="layout-wrapper layout-blank"
    data-allow-mismatch
  >
    <RouterView />
  </div>
</template>

<style>
.layout-wrapper.layout-blank {
  flex-direction: column;
}
</style>
