<script setup>
import { useCreateUser } from '@/hooks/Users/useCreateUser'
import { useCreateValidations } from '@/hooks/Users/useCreateValidations'
import { useCatalogShops } from '@/hooks/Shops/useCatalogShops'
import { useDarkModeStore } from '@/store/dark-mode'
import { ref, watch } from 'vue'
import { IMask } from 'vue-imask'

const props = defineProps({
  modelValue: Boolean,
  data: Object,
})

const emit = defineEmits(['update:modelValue', 'create'])

const darkModeStore = useDarkModeStore()

const { loading, createUser, userData, resetUserData } = useCreateUser()
const { loading: loadingCatShops, loadCatShops, catShopsData } = useCatalogShops()
const isOpen = ref(props.modelValue)

const {
  formValidate,
  formErrors,
  touchField,
  touchedFields,
  resetValidations,
} = useCreateValidations({ userData })

const handleSaveShop = async () => {
  if (!formValidate.value) return

  const result = await createUser()

  if (!result) return

  resetValidations()
  resetUserData()
  emit('create')
  isOpen.value = false
}


function updatePhone(event) {
  const mask = IMask.createMask({
    mask: '(000) 000-0000',
  })

  if (event.target.value.length === 0) return
  mask.resolve(event.target.value || event)

  userData.value.phone = mask.value
}

watch(() => props.modelValue, val => {
  isOpen.value = val
  if (val) {
    loadCatShops()
  }
  resetValidations()
})

watch(isOpen, val => {
  emit('update:modelValue', val)
  resetUserData()
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
        📝 Agregar Nuevo Usuario
      </VCardTitle>

      <VCardText class="space-y-8">
        <div class="flex flex-col gap-4">
          <VTextField
            v-model="userData.name"
            label="Nombre"
            placeholder="Nombre"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.name ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.name && !!formErrors.name"
            :error-messages="touchedFields.name ? formErrors.name : ''"
            @enter="handleSaveShop"
            @input="touchField('name')"
          />
          <VTextField
            v-model="userData.last_name"
            label="Apellido"
            placeholder="Apellido"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.last_name ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.last_name && !!formErrors.last_name"
            :error-messages="touchedFields.last_name ? formErrors.last_name : ''"
            @enter="handleSaveShop"
            @input="touchField('last_name')"
          />
          <VTextField
            v-model="userData.email"
            label="Correo del contacto"
            placeholder="example@example.com"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.email ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.email && !!formErrors.email"
            :error-messages="touchedFields.email ? formErrors.email : ''"
            @enter="handleSaveShop"
            @input="touchField('email')"
          />
          <VTextField
            v-model="userData.phone"
            label="Telefono"
            placeholder="(###) ###-####"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.phone ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.phone && !!formErrors.phone"
            :error-messages="touchedFields.phone ? formErrors.phone : ''"
            @enter="handleSaveShop"
            @input="(e) => {touchField('phone'); updatePhone(e)}"
          />
          <VTextField
            v-model="userData.curp"
            label="CURP"
            placeholder="XXXXXXXXXXXXXXXXXX"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.curp ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.curp && !!formErrors.curp"
            :error-messages="touchedFields.curp ? formErrors.curp : ''"
            @enter="handleSaveShop"
            @input="touchField('curp')"
          />
          <VSelect
            v-model="userData.role"
            :items="[{id: 3, name: 'Moderador'}, {id: 4, name: 'Comerciante'}]"
            item-title="name"
            item-value="id"
            label="Tipo de usuario"
            placeholder="Tipo de usuario"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.role ? '!max-h-[60px]' : '!max-h-[38px]'"
            :disabled="loading"
            :error="touchedFields.role && !!formErrors.role"
            :error-messages="touchedFields.role ? formErrors.role : ''"
            @input="touchField('role')"
          />
          <VSelect
            v-if="userData.role == 4"
            v-model="userData.alliance"
            :items="catShopsData"
            item-title="name"
            item-value="id"
            label="Comercio al que pertenece"
            placeholder="Comercio al que pertenece"
            outlined
            :color="darkModeStore.darkMode ? 'white' : 'primary'" 
            :class="formErrors.alliance ? '!max-h-[60px]' : '!max-h-[38px]'"
            :loading="loadingCatShops"
            :disabled="loadingCatShops || loading"
            :error="touchedFields.alliance && !!formErrors.alliance"
            :error-messages="touchedFields.alliance ? formErrors.alliance : ''"
            @input="touchField('alliance')"
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
