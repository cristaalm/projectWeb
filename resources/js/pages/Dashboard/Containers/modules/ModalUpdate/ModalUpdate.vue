<script setup>
import { useUpdateContainer } from '@/hooks/Containers/useUpdateContainer'
import { useValidations } from '@/hooks/Containers/useValidations'
import { useDarkModeStore } from '@/store/dark-mode'
import { ref, watch } from 'vue'


const props = defineProps({
  modelValue: Boolean,
  data: Object,
})

const emit = defineEmits(['update:modelValue', 'update'])

const darkModeStore = useDarkModeStore()
const { loading, updateContainer, setNewData, isUnchanged, containerData } = useUpdateContainer()
const isOpen = ref(props.modelValue)

const {
  formValidate,
  formErrors,
  touchField,
  touchedFields,
  resetValidations,
} = useValidations({ containerData })

watch(() => props.modelValue, val => {
  isOpen.value = val
  if (val) {
    setNewData(props.data)
  }
  resetValidations()
})

watch(isOpen, val => {
  emit('update:modelValue', val)
})

const handleSaveContainer = async () => {
  if (!formValidate.value) return

  const result = await updateContainer()

  if (!result) return

  resetValidations()
  emit('update')
  isOpen.value = false
}

function updatePhone(event) {
  const mask = IMask.createMask({
    mask: '(000) 000-0000',
  })

  if (event.target.value.length === 0) return
  mask.resolve(event.target.value || event)

  containerData.value.phone = mask.value
}
</script>

<template>
  <VDialog
    v-model="isOpen"
    max-width="800px"
    persistent
  >
    <VCard>
      <VCardTitle class="text-xl font-semibold">
        📝 Editar Comercio
      </VCardTitle>

      <VCardText class="space-y-8">
        <div class="flex flex-col gap-4">
          <VTextField
            v-model="containerData.name"
            label="Nombre del contenedor"
            placeholder="Nombre del contenedor"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.name ? '!max-h-[60px]' : '!max-h-[38px]'"
            :loading="loading"
            :disabled="loading"
            :error="touchedFields.name && !!formErrors.name"
            :error-messages="touchedFields.name ? formErrors.name : ''"
            @enter="handleSaveContainer"
            @input="touchField('name')"
          />
          <VTextField
            v-model="containerData.serial_number"
            label="Número de serie del contenedor"
            placeholder="Numero de serie del contenedor"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.serial_number ? '!max-h-[60px]' : '!max-h-[38px]'"
            :loading="loading"
            :disabled="loading"
            :error="touchedFields.serial_number && !!formErrors.serial_number"
            :error-messages="touchedFields.serial_number ? formErrors.serial_number : ''"
            @enter="handleSaveContainer"
            @input="touchField('serial_number')"
          />
          <VTextField
            v-model="containerData.location"
            label="Ubicación del contenedor"
            placeholder="Ubicación del contenedor"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.location ? '!max-h-[60px]' : '!max-h-[38px]'"
            :loading="loading"
            :disabled="loading"
            :error="touchedFields.location && !!formErrors.location"
            :error-messages="touchedFields.location ? formErrors.location : ''"
            @enter="handleSaveContainer"
            @input="touchField('location')"
          />
        </div>

        <!-- Estado del plan -->
        <VTable class="mt-4 text-no-wrap">
          <thead>
            <tr>
              <th>Configuración</th>
              <th>Valor</th>
              <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            <tr
              class=" select-none cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800"
              @click="() => containerData.status = !containerData.status"
            >
              <td>Estado</td>
              <td>
                <VCheckbox v-model="containerData.status" />
              </td>
              <td class="text-sm text-gray-500 dark:text-slate-300">
                El contenedor  habilitado o deshabilitado
              </td>
            </tr>
          </tbody>
        </VTable>
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
          :disabled="loading || !formValidate || isUnchanged"
          :loading="loading"
          @click="handleSaveContainer"
        >
          Guardar cambios
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
