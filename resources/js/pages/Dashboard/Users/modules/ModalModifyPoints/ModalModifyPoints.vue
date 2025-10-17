<script setup>
import useModifyPoints from '@/hooks/Users/useModifyPoints'
import { ref, watch } from 'vue'
import { useDarkModeStore } from '@/store/dark-mode'

const props = defineProps({
  modelValue: Boolean,
  data: Object,
})

const emit = defineEmits(['update:modelValue', 'modify-points'])

const darkModeStore = useDarkModeStore()
const { loading, modifyPoints, setNewData, userData } = useModifyPoints()
const data = computed(() => props.data)

const isOpen = ref(props.modelValue)

watch(() => props.modelValue, val => {
  isOpen.value = val
  if (val) {
    console.log(data.value)
    setNewData({ id: data.value.id, points: data.value.total_points, justify: '' })
  } else {
    isOpen.value = false
    setNewData({})
  }
})

watch(isOpen, val => emit('update:modelValue', val))

const confirmModifyPoints = async () => {
  if (loading.value) return

  const response = await modifyPoints()

  if (response) {
    emit('modify-points', response)
    isOpen.value = false
  }
}

watch(isOpen, val => emit('update:modelValue', val))
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
        Modificar Puntos
      </VCardTitle>

      <!-- Contenido -->
      <VCardText>
        <div class="flex flex-col gap-4">
          <span>
            Ingresa la nueva cantidad de puntos y una justificación para la modificación.
          </span>
          <div class="flex flex-col gap-2">
            <VTextField
              v-model="userData.points"
              v-number-only
              :disabled="loading"
              :color="darkModeStore.darkMode ? 'white' : 'primary'"
              :rules="[
                v => !!v || 'La cantidad de puntos es requerida',
                v => v >= 0 || 'La cantidad de puntos debe ser mayor o igual a 0',
              ]"
              label="Puntos totales"
              placeholder="Ingrese la nueva cantidad de puntos"
              type="text"
            />
            
            <VTextarea
              v-model="userData.justify"
              label="Justificación"
              placeholder="Ingrese la justificación para modificar los puntos"
              :color="darkModeStore.darkMode ? 'white' : 'primary'"
              :disabled="loading"
              class="w-full"
              auto-grow
              rows="3"
              :rules="[
                v => !!v || 'La justificación es requerida',
                v => v.length <= 500 || 'La justificación debe tener menos de 500 caracteres'
              ]"
            />
          </div>
        </div>

        <VAlert
          icon="mdi mdi-alert-outline"
          variant="tonal"
          color="warning"
          class="mt-4"
        >
          Se le notificará a <strong>{{ data?.name }}</strong> sobre la 
          modificación de sus puntos.
        </VAlert>
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
          :disabled="loading || !userData.points || !userData.justify"
          @click="confirmModifyPoints"
        >
          Modificar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

