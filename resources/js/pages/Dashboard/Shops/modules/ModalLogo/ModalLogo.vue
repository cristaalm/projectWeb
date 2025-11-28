<script setup>
import { useLogoShop } from '@/hooks/Shops/useLogoShop'
import { useDarkModeStore } from '@/store/dark-mode'
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  data: Object,
})

const emit = defineEmits(['update:modelValue', 'update'])

const darkModeStore = useDarkModeStore()
const isOpen = ref(props.modelValue)

const {
  loading,
  previewUrl,
  hasChanges,
  handleFileChange,
  handleDeleteLocal,
  saveLogo,
  initializeLogoState,
  resetLogoState,
  displayImageUrl,
  initialHasLogo,
} = useLogoShop()

// Inicializar cuando se abre con datos
watch(() => props.data, newData => {
  if (newData) {
    initializeLogoState(newData)
  }
})

// Sincronizar v-model
watch(() => props.modelValue, val => {
  isOpen.value = val
  if (val && props.data) {
    initializeLogoState(props.data)
  } else {
    resetLogoState()
  }
})

watch(isOpen, val => {
  emit('update:modelValue', val)
  if (!val) resetLogoState()
})

// Guardar
const handleSave = async () => {
  if (!hasChanges.value) return

  const success = await saveLogo(props.data.id)
  if (success) {
    emit('update')
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
        🖼️ Logo del Comercio
      </VCardTitle>

      <VCardText class="space-y-6">
        <!-- Contenedor del logo con acciones flotantes -->
        <div class="relative flex justify-center py-6">
          <!-- Círculo del logo -->
          <div class="relative w-40 h-40 rounded-full overflow-visible border-4 border-gray-200 dark:border-gray-700">
            <img
              :src="displayImageUrl"
              alt="Logo del comercio"
              class="w-full h-full object-cover rounded-full"
            >

            <!-- Botón flotante AZUL (subir) -->
            <label
              class="absolute bottom-2 right-2 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-full cursor-pointer shadow-lg transition-all duration-200 hover:scale-110"
              title="Cambiar logo"
            >
              <VIcon
                icon="bx-image-add"
                size="20"
              />
              <input
                type="file"
                accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp"
                class="hidden"
                @change="handleFileChange"
              >
            </label>

            <!-- Botón flotante ROJO (eliminar) - solo si tenía logo inicialmente -->
            <button
              v-if="initialHasLogo || previewUrl"
              class="absolute bottom-2 left-2 bg-red-500 hover:bg-red-600 text-white p-2 rounded-full cursor-pointer shadow-lg transition-all duration-200 hover:scale-110"
              title="Eliminar logo"
              @click="handleDeleteLocal"
            >
              <VIcon
                icon="bx-trash"
                size="20"
              />
            </button>
          </div>
        </div>

        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
          <p v-if="!data?.logo && !previewUrl">
            No hay logo asignado. Sube uno para personalizar.
          </p>
          <p v-else-if="previewUrl">
            Vista previa del nuevo logo.
          </p>
          <p v-else>
            Logo actual del comercio.
          </p>
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
          color="success"
          variant="flat"
          :disabled="loading || !hasChanges"
          :loading="loading"
          prepend-icon="bx-save"
          @click="handleSave"
        >
          Guardar Cambios
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
/* Efecto hover suave en botones flotantes */
button:hover,
label:hover {
  transform: scale(1.1);
}
</style>
