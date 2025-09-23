<script setup>
import { useDeleteShop } from '@/hooks/Shops/useDeleteShop'
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  data: Object,
})

const emit = defineEmits(['update:modelValue', 'delete'])

const { loading, deleteShop } = useDeleteShop()

const isOpen = ref(props.modelValue)

watch(() => props.modelValue, val => {
  isOpen.value = val
})

watch(isOpen, val => emit('update:modelValue', val))

const confirmDelete = async () => {
  if (await deleteShop(props.data.id)) {
    emit('delete', true)
    isOpen.value = false
  }
}
</script>

<template>
  <VDialog
    v-model="isOpen"
    max-width="500px"
    persistent
  >
    <VCard>
      <VCardTitle class="text-xl font-semibold">
        ⚠️ Confirmar Eliminación
      </VCardTitle>
      <VCardText>
        <div class="flex flex-col items-center gap-2">
          <VAlert
            type="warning"
            variant="tonal"
          >
            Si el comercio tiene elementos asignados, no se podrá eliminar.
          </VAlert>

          <div class="text-lg">
            ¿Estás seguro de que deseas eliminar el comercio
            <strong>{{ data?.name }}</strong>? Esta acción no se puede deshacer.
          </div>
        </div>
      </VCardText>
      <VDivider />
      <VCardActions class="justify-end mt-2 space-x-2">
        <VBtn
          variant="elevated"
          color="grey darken-1"
          :disabled="loading"
          @click="isOpen = false"
        >
          Cancelar
        </VBtn>
        <VBtn
          color="#dc2626"
          variant="flat"
          :loading="loading"
          :disabled="loading"
          @click="confirmDelete"
        >
          Eliminar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
