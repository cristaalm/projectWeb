<script setup>
import { useAuthStore } from '@/store/auth'
import SuperadminPanel from '@/views/Panel/superadmin/SuperadminPanel.vue'
import { computed } from 'vue'

const authStore = useAuthStore()

// Cada rol con acceso al dashboard tiene su propia vista de panel. Los que
// todavía no la tienen conservan la card vacía.
const role = computed(() => authStore.user?.role?.name)
</script>

<template>
  <SuperadminPanel v-if="role === 'superadmin'" />
  <div
    v-else
    class="w-full h-full"
  >
    <VCard class="w-full h-full" />
  </div>
</template>
