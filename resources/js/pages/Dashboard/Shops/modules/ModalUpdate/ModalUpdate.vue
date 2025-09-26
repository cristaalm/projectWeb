<script setup>
import { useUpdateShop } from '@/hooks/Shops/useUpdateShop'
import { useValidations } from '@/hooks/Shops/useValidations'
import { useCatalogTypeShop } from '@/hooks/TypeShop/useCatalogTypeShop'
import { useDarkModeStore } from '@/store/dark-mode'
import { ModalViewTypeShop, useModalViewTypeShop } from '@/views/TypeShop/ModalView'
import { ref, watch } from 'vue'


const props = defineProps({
  modelValue: Boolean,
  data: Object,
})

const emit = defineEmits(['update:modelValue', 'update'])

const darkModeStore = useDarkModeStore()
const { loading, updateShop, setNewData, isUnchanged, shopData } = useUpdateShop()
const { loading: loadingTypeShop, loadCatalogTypeShop, typeShopData } = useCatalogTypeShop()
const isOpen = ref(props.modelValue)

const {
  formValidate,
  formErrors,
  touchField,
  touchedFields,
  resetValidations,
} = useValidations({ shopData })

watch(() => props.modelValue, val => {
  isOpen.value = val
  if (val) {
    setNewData(props.data)
    loadCatalogTypeShop()
  }
  resetValidations()
})

watch(isOpen, val => {
  emit('update:modelValue', val)
})

const handleSaveShop = () => {
  if (!formValidate.value) return

  const result = updateShop()

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

  shopData.value.phone = mask.value
}
const { showModalViewTypeShop, openModalViewTypeShop } = useModalViewTypeShop()
</script>

<template>
  <ModalViewTypeShop
    v-model="showModalViewTypeShop"
    @change="loadCatalogTypeShop"
  />

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
            v-model="shopData.name"
            label="Nombre del comercio"
            placeholder="Nombre del comercio"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.name ? '!max-h-[60px]' : '!max-h-[38px]'"
            :loading="loading"
            :disabled="loading"
            :error="touchedFields.name && !!formErrors.name"
            :error-messages="touchedFields.name ? formErrors.name : ''"
            @enter="handleSaveShop"
            @input="touchField('name')"
          />
          <VTextField
            v-model="shopData.contact_name"
            label="Nombre del contacto"
            placeholder="Nombre del contacto"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.contact_name ? '!max-h-[60px]' : '!max-h-[38px]'"
            :loading="loading"
            :disabled="loading"
            :error="touchedFields.contact_name && !!formErrors.contact_name"
            :error-messages="touchedFields.contact_name ? formErrors.contact_name : ''"
            @enter="handleSaveShop"
            @input="touchField('contact_name')"
          />
          <VTextField
            v-model="shopData.contact_email"
            label="Correo del contacto"
            placeholder="example@example.com"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.contact_email ? '!max-h-[60px]' : '!max-h-[38px]'"
            :loading="loading"
            :disabled="loading"
            :error="touchedFields.contact_email && !!formErrors.contact_email"
            :error-messages="touchedFields.contact_email ? formErrors.contact_email : ''"
            @enter="handleSaveShop"
            @input="touchField('contact_email')"
          />
          <VTextField
            v-model="shopData.phone"
            label="Telefono"
            placeholder="(###) ###-####"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.phone ? '!max-h-[60px]' : '!max-h-[38px]'"
            :loading="loading"
            :disabled="loading"
            :error="touchedFields.phone && !!formErrors.phone"
            :error-messages="touchedFields.phone ? formErrors.phone : ''"
            @enter="handleSaveShop"
            @input="(e) => {touchField('phone'); updatePhone(e)}"
          />
          <VTextField
            v-model="shopData.address"
            label="Direccion"
            placeholder="Direccion"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.address ? '!max-h-[60px]' : '!max-h-[38px]'"
            :loading="loading"
            :disabled="loading"
            :error="touchedFields.address && !!formErrors.address"
            :error-messages="touchedFields.address ? formErrors.address : ''"
            @enter="handleSaveShop"
            @input="touchField('address')"
          />
          <div class="flex flex-row items-center justify-center w-full">
            <VSelect
              v-model="shopData.type_shop_id"
              :items="typeShopData"
              item-title="name"
              item-value="id"
              label="Tipo de comercio"
              placeholder="Tipo de comercio"
              outlined
              :color="darkModeStore.darkMode ? 'white' : 'primary'" 
              :class="formErrors.type_shop_id ? '!max-h-[60px]' : '!max-h-[38px]'"
              :loading="loadingTypeShop || loading"
              :disabled="loadingTypeShop || loading"
              :error="touchedFields.type_shop_id && !!formErrors.type_shop_id"
              :error-messages="touchedFields.type_shop_id ? formErrors.type_shop_id : ''"
              @input="touchField('type_shop_id')"
            />
            <VBtn
              color="primary"
              variant="flat"
              :disabled="loadingTypeShop || loading"
              :loading="loadingTypeShop || loading"
              prepend-icon="bx-search"
              @click="openModalViewTypeShop"
            >
              Consultar
            </VBtn>
          </div>
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
              @click="() => shopData.status = !shopData.status"
            >
              <td>Estado</td>
              <td>
                <VCheckbox v-model="shopData.status" />
              </td>
              <td class="text-sm text-gray-500 dark:text-slate-300">
                El comercio estara habilitado o deshabilitado
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
          @click="handleSaveShop"
        >
          Guardar cambios
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
