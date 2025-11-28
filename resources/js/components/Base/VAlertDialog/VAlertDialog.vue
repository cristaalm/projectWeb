<!-- components/VDialogComponent.vue -->
<template>
  <div>
    <VDialog
      v-for="dialog in dialogs"
      :key="dialog.id"
      v-model="dialog.model"
      persistent
      max-width="500"
    >
      <VCard>
        <VCardTitle
          v-if="dialog.title"
          class="text-h6"
        >
          {{ dialog.title }}
        </VCardTitle>

        <VCardText class="pt-4">
          <div v-if="dialog.type === 'loading'">
            <VProgressCircular
              indeterminate
              color="primary"
            />
            <p class="mt-2">
              {{ dialog.text || 'Cargando...' }}
            </p>
          </div>
          <div v-else>
            <p>{{ dialog.text }}</p>
          </div>
        </VCardText>

        <VCardActions v-if="dialog.type !== 'loading'">
          <VBtn
            v-if="dialog.type === 'confirm'"
            color="grey"
            variant="text"
            @click="closeDialog(dialog.id, false)"
          >
            {{ dialog.cancelText }}
          </VBtn>
          <VSpacer v-if="dialog.type === 'confirm'" />

          <VBtn
            color="primary"
            variant="flat"
            @click="dialog.type === 'confirm' ? closeDialog(dialog.id, true) : closeDialog(dialog.id)"
          >
            {{ dialog.confirmText }}
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia'
import { useDialogStore } from '@/store/useAlertDialogStorage'

const dialogStore = useDialogStore()
const { dialogs } = storeToRefs(dialogStore)

// Hacer que cada diálogo sea reactivo con un modelo local
dialogs.value.forEach(dialog => {
  dialog.model = true
})

const closeDialog = (id, value) => {
  dialogStore.closeDialog(id, value)
}
</script>
