<script setup>
// path: App.vue
import ToastNotification from '@/components/Base/ToastNotification'
import VDialogComponent from '@/components/Base/VAlertDialog/VAlertDialog.vue'
</script>

<template>
  <VApp>
    <ToastNotification />
    <VDialogComponent />
    <RouterView />
  </VApp>
</template>
