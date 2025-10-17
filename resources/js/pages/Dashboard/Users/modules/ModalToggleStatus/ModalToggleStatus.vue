<script setup>
import useToggleStatus from '@/hooks/Users/useToggleStatus'
import { ref, watch } from 'vue'
import { useToastStore } from '@/store/useToastStore'
import { useDarkModeStore } from '@/store/dark-mode'

const props = defineProps({
  modelValue: Boolean,
  data: Object,
})

const emit = defineEmits(['update:modelValue', 'toggle-status'])

const darkModeStore = useDarkModeStore()
const { loading, toggleStatus } = useToggleStatus()
const toastStore = useToastStore()
const data = computed(() => props.data)
const justification = ref('')

const isOpen = ref(props.modelValue)

watch(() => props.modelValue, val => {
  isOpen.value = val
})

watch(isOpen, val => emit('update:modelValue', val))

const confirmToggleStatus = async () => {

  if (data.value.status == 0 && justification.value.trim().length === 0) {
    toastStore.showToast({ message: 'Para desactivar la cuenta, debe ingresar una justificación.', tipo: 'error', duration: 4000 })
    
    return
  }

  if (await toggleStatus({ id: data.value.user.id, status: data.value.status, justification: justification.value })) {
    emit('toggle-status', true)
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
      <!-- Título -->
      <VCardTitle class="text-xl font-semibold pb-2">
        {{ data?.status == 1 ? 'Activar' : 'Desactivar' }} Cuenta
      </VCardTitle>

      <!-- Contenido -->
      <VCardText>
        <div class="flex flex-col gap-4">
          <p>
            ¿Estás seguro de {{ data?.status == 1 ? 'activar' : 'desactivar' }} la cuenta
            <strong>{{ data?.user?.name }}</strong>?
          </p>
          <p class="text-gray-600 dark:text-slate-300">
            Se le notificará a <strong>{{ data?.user?.name }}</strong> sobre la 
            {{ data?.status == 1 ? 'activación' : 'desactivación' }} de su cuenta.
          </p>

          <!-- Justificación -->
          <div v-if="data?.status == 0">
            <VTextarea
              v-model="justification"
              label="Justificación"
              placeholder="Ingrese la justificación para desactivar la cuenta"
              class="w-full"
              :color="darkModeStore.darkMode ? 'white' : 'primary'"
              auto-grow
              rows="3"
              :rules="[
                v => !!v || 'La justificación es requerida',
                v => v.length <= 500 || 'La justificación debe tener menos de 500 caracteres'
              ]"
            />
          </div>
        </div>
      </VCardText>

      <!-- Acciones -->
      <VDivider />
      <VCardActions class="justify-end gap-2 py-3">
        <VBtn
          variant="outlined"
          color="grey darken-1"
          :disabled="loading"
          @click="isOpen = false"
        >
          Cancelar
        </VBtn>
        <VBtn
          :color="data?.status == 1 ? 'success' : 'error'"
          variant="flat"
          :loading="loading"
          :disabled="loading || (data?.status == 0 && justification.trim().length === 0)"
          @click="confirmToggleStatus"
        >
          {{ data?.status == 1 ? 'Activar' : 'Desactivar' }}
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

