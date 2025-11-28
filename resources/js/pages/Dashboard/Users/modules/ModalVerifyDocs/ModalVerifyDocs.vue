<script setup>
import useGetListDocs from '@/hooks/Users/useGetListDocs'
import useVerifyDocs from '@/hooks/Users/useVerifyDocs'
import { ref, watch, onBeforeUnmount } from 'vue'
import { useToastStore } from '@/store/useToastStore'
import ImageZoom from '@/components/Base/ImageZoom'
import { useDarkModeStore } from '@/store/dark-mode'

const props = defineProps({
  modelValue: Boolean,
  data: Object,
})

const emit = defineEmits(['update:modelValue', 'verify-docs'])

const darkModeStore = useDarkModeStore()
const { loadingListDocs, getListDocs, listDocs, resetListDocs, cleanupObjectUrls } = useGetListDocs()
const { loadingVerifyDocs, verifyDocs } = useVerifyDocs()
const toastStore = useToastStore()
const data = computed(() => props.data)

const itemsSelect = ref([
  { title: 'Aprobado', value: 1 },
  { title: 'Rechazado', value: 2 },
])

const status = ref(null)
const justification = ref('')

const isOpen = ref(props.modelValue)

watch(() => props.modelValue, val => {
  isOpen.value = val
  if (val) {
    getListDocs({ id: data.value.id })
  } else {
    resetListDocs()
    cleanupObjectUrls()
    status.value = null
    justification.value = ''
    itemsSelect.value = [
      { title: 'Aprobado', value: 1 },
      { title: 'Rechazado', value: 2 },
    ]
  }
})

watch(listDocs, val => {
  if (!val.front && !val.back && !val.selfie) {
    status.value = 2
    justification.value = 'El usuario no ha cargado ningún documento'
    itemsSelect.value = [
      { title: 'Rechazado', value: 2 },
    ]
  } else {
    status.value = null
    justification.value = ''
    itemsSelect.value = [
      { title: 'Aprobado', value: 1 },
      { title: 'Rechazado', value: 2 },
    ]
  }
})

watch(isOpen, val => emit('update:modelValue', val))

const confirmVerifyDocs = async () => {
  if (loadingVerifyDocs.value) return

  const response = await verifyDocs({
    id: data.value.id,
    status: status.value,
    justification: justification.value ? justification.value : null,
  })

  if (response) {
    emit('verify-docs', response)
    isOpen.value = false
  }
}

onBeforeUnmount(() => {
  resetListDocs()
  cleanupObjectUrls()
})
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
        Verificar Documentos
      </VCardTitle>

      <!-- Contenido -->
      <VCardText>
        <div 
          v-if="!loadingListDocs" 
          class="flex flex-col gap-4"
        >
          <span 
            v-if="listDocs.front || listDocs.back || listDocs.selfie" 
            class="font-bold"
          >
            Documentos cargados:
          </span>
          <div class="w-full grid grid-cols-3 gap-2">
            <!-- en caso de no haber ninguna -->
            <div 
              v-if="!listDocs.front && !listDocs.back && !listDocs.selfie" 
              class="col-span-3 bg-gray-200 dark:bg-gray-800 rounded-lg flex flex-col items-center justify-center gap-2 p-4"
            >
              <VIcon 
                icon="bx-user" 
                size="large"
              />
              <span>El usuario no ha cargado ningun documento</span>
            </div>

            <!-- credencial frontal (en todas estas ImageZoom estoy imprimiendo un true o un false, por que todavía no se como hacer para recuperar la imagen desde el endpoint) -->
            <div 
              v-if="listDocs.front" 
              class="col-span-1 flex flex-col items-center my-5 bg-gray-200 dark:bg-gray-800 rounded-lg p-2"
            >
              <div class="image-fit w-[120px] h-[120px] overflow-hidden">
                <ImageZoom :src="listDocs.front" />
              </div>
            </div>

            <!-- credencial trasera -->
            <div 
              v-if="listDocs.back" 
              class="col-span-1 flex flex-col items-center my-5 bg-gray-200 dark:bg-gray-800 rounded-lg p-2 "
            >
              <div class="image-fit w-[120px] h-[120px] overflow-hidden">
                <ImageZoom :src="listDocs.back" />
              </div>
            </div>

            <!-- selfie -->
            <div 
              v-if="listDocs.selfie" 
              class="col-span-1 flex flex-col items-center my-5 bg-gray-200 dark:bg-gray-800 rounded-lg p-2"
            >
              <div class="image-fit w-[120px] h-[120px] overflow-hidden">
                <ImageZoom :src="listDocs.selfie" />
              </div>
            </div>
          </div>

          <div class="flex flex-col gap-2">
            <VSelect
              v-model="status"
              :items="itemsSelect"
              :disabled="loadingVerifyDocs"
              :color="darkModeStore.darkMode ? 'white' : 'primary'"
              label="Verificación"
              placeholder="Seleccione una opción"
              item-title="title"
              item-value="value"
            />
            
            <VTextarea
              v-if="status == 2"
              v-model="justification"
              label="Justificación"
              placeholder="Ingrese la justificación para rechazar los documentos"
              :color="darkModeStore.darkMode ? 'white' : 'primary'"
              :disabled="loadingVerifyDocs"
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
        <VProgressLinear
          v-else
          indeterminate
          color="primary"
        />
      </VCardText>

      <!-- Acciones -->
      <VDivider />
      <VCardActions 
        v-if="!loadingListDocs" 
        class="justify-end gap-2 py-3"
      >
        <VBtn
          variant="outlined"
          color="grey darken-1"
          :disabled="loadingVerifyDocs"
          @click="isOpen = false"
        >
          Cancelar
        </VBtn>
        <VBtn
          :color="status == 2 ? 'error' : 'success'"
          variant="flat"
          :loading="loadingVerifyDocs"
          :disabled="loadingVerifyDocs || !status || (status == 2 && !justification)"
          @click="confirmVerifyDocs"
        >
          {{ status == 2 ? 'Rechazar' : 'Aprobar' }}
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

