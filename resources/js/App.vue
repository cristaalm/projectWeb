<template>
  <VApp>
    <ToastNotification />
    <VDialogComponent />
    <RouterView />
  </VApp>
</template>

<script setup>
import ToastNotification from '@/components/Base/ToastNotification'
import VDialogComponent from '@/components/Base/VAlertDialog/VAlertDialog.vue'
</script>
