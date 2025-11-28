<script setup>
import useResetPass from '@/hooks/Users/useResetPass'
import { ref, watch } from 'vue'


const props = defineProps({
  modelValue: Boolean,
  data: Object,
})

const emit = defineEmits(['update:modelValue', 'reset-pass'])
const { loading, resetPass } = useResetPass()
const data = computed(() => props.data)

const isOpen = ref(props.modelValue)

watch(() => props.modelValue, val => {
  isOpen.value = val
})

watch(isOpen, val => emit('update:modelValue', val))

const confirmToggleStatus = async () => {
  if (await resetPass({ email: data.value.email })) {
    emit('reset-pass', true)
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
        Restablecer Contraseña
      </VCardTitle>

      <!-- Contenido -->
      <VCardText>
        <div class="flex flex-col gap-4">
          <p>
            ¿Estás seguro de restablecer la contraseña de la cuenta
            <strong>{{ data?.name }}</strong>?
          </p>
          <p class="text-gray-600 dark:text-slate-300">
            Se enviará una solicitud por correo de reinicio de contraseña a 
            <strong>{{ data?.email }}</strong> 
          </p>
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
          color="success"
          variant="flat"
          :loading="loading"
          :disabled="loading"
          @click="confirmToggleStatus"
        >
          Restablecer Contraseña
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

