<script setup>
import { useCreateBadge } from '@/hooks/Badges/useCreateBadge'
import { useValidations } from '@/hooks/Badges/useValidations'
import { useDarkModeStore } from '@/store/dark-mode'
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
})

const emit = defineEmits(['update:modelValue', 'create'])

const darkModeStore = useDarkModeStore()

const { loading, createBadge, badgeData, resetBadgeData } = useCreateBadge()
const isOpen = ref(props.modelValue)

const {
  formValidate,
  formErrors,
  touchField,
  touchedFields,
  resetValidations,
} = useValidations({ badgeData })

const handleSaveBadge = async () => {
  if (!formValidate.value) return

  const result = await createBadge()

  if (!result) return

  resetValidations()
  resetBadgeData()
  emit('create')
  isOpen.value = false
}

watch(() => props.modelValue, val => {
  isOpen.value = val
  resetValidations()
})

watch(isOpen, val => {
  emit('update:modelValue', val)
  resetBadgeData()
})
</script>

<template>
  <VDialog
    v-model="isOpen"
    max-width="800px"
    persistent
  >
    <VCard>
      <VCardTitle class="text-xl font-semibold">
        📝 Agregar Insignia
      </VCardTitle>

      <VCardText class="space-y-8">
        <div class="flex flex-col gap-4">
          <VTextField
            v-model="badgeData.name"
            label="Nombre de la insignia"
            placeholder="Nombre de la insignia"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.name ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.name && !!formErrors.name"
            :error-messages="touchedFields.name ? formErrors.name : ''"
            @enter="handleSaveBadge"
            @input="touchField('name')"
          />
          <VTextField
            v-model="badgeData.points_required"
            v-number-only
            label="Numero de puntos requeridos"
            placeholder="Numero de puntos requeridos"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.points_required ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.points_required && !!formErrors.points_required"
            :error-messages="touchedFields.points_required ? formErrors.points_required : ''"
            @enter="handleSaveBadge"
            @keydown="touchField('points_required')"
          />
          <VTextField
            v-model="badgeData.points_awared"
            v-number-only
            label="Numero de puntos de recompensa"
            placeholder="Numero de puntos de recompensa"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.points_awared ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.points_awared && !!formErrors.points_awared"
            :error-messages="touchedFields.points_awared ? formErrors.points_awared : ''"
            @enter="handleSaveBadge"
            @keydown="touchField('points_awared')"
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
              @click="() => badgeData.status = !badgeData.status"
            >
              <td>Estado</td>
              <td>
                <VCheckbox v-model="badgeData.status" />
              </td>
              <td class="text-sm text-gray-500 dark:text-slate-300">
                La insignia estará habilitada o deshabilitada
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
          :disabled="loading || !formValidate"
          :loading="loading"
          prepend-icon="bx-save"
          @click="handleSaveBadge"
        >
          Guardar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
