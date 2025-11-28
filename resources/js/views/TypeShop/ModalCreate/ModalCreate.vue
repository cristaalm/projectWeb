<script setup>
import { useCreateTypeShop } from '@/hooks/TypeShop/useCreateTypeShop'
import { useValidations } from '@/hooks/TypeShop/useValidations'
import { useDarkModeStore } from '@/store/dark-mode'
import { ref, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
})

const emit = defineEmits(['update:modelValue', 'create'])

const darkModeStore = useDarkModeStore()

const { loading, createTypeShop, typeShopData, resetTypeShopData } = useCreateTypeShop()
const isOpen = ref(props.modelValue)

const {
  formValidate,
  formErrors,
  touchField,
  touchedFields,
  resetValidations,
} = useValidations({ typeShopData })



const handleSaveShop = async () => {
  if (!formValidate.value) return

  const result = await createTypeShop()

  if (!result) return

  resetValidations()
  resetTypeShopData()
  emit('create')
  isOpen.value = false
}

watch(() => props.modelValue, val => {
  isOpen.value = val
  resetValidations()
})

watch(isOpen, val => {
  emit('update:modelValue', val)
  resetTypeShopData()
})
</script>

<template>
  <VDialog
    v-model="isOpen"
    max-width="400px"
    persistent
  >
    <VCard>
      <VCardTitle class="text-xl font-semibold">
        📝 Agregar una categoria
      </VCardTitle>

      <VCardText class="space-y-8">
        <div class="flex flex-col gap-4">
          <VTextField
            v-model="typeShopData.name"
            autofocus
            label="Nombre de la categoria"
            placeholder="Nombre de la categoria"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.name ? '!max-h-[60px]' : '!max-h-[38px]'"
            :loading="loading"
            :disabled="loading"
            :error="touchedFields.name && !!formErrors.name"
            :error-messages="touchedFields.name ? formErrors.name : ''"
            @keydown.enter="handleSaveShop"
            @input="touchField('name')"
          />
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
          :disabled="loading || !formValidate"
          :loading="loading"
          prepend-icon="bx-save"
          @click="handleSaveShop"
        >
          Guardar
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
