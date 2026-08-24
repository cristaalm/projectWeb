<script setup>
import { useContainerManagement } from '@/hooks/Containers/useContainerManagement'
import { computed, ref, watch } from 'vue'
import LocationPickerDialog from './components/LocationPickerDialog.vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  mode: { type: String, default: 'create' }, // 'create' | 'edit'
  container: { type: Object, default: null },
})

const emit = defineEmits(['update:modelValue', 'saved'])

const { loading, createContainer, updateContainer } = useContainerManagement()

const form = ref({
  name: '',
  serial_number: '',
  location: '',
  latitude: null,
  longitude: null,
  status: true,
})

const locationDialog = ref(false)

const isEdit = computed(() => props.mode === 'edit')
const dialogTitle = computed(() => (isEdit.value ? 'Editar contenedor' : 'Crear contenedor'))

const hasCoordinates = computed(() => form.value.latitude !== null && form.value.longitude !== null)

const isValid = computed(() => (
  Boolean(form.value.name.trim())
  && Boolean(form.value.serial_number.trim())
  && Boolean(form.value.location.trim())
))

function resetForm() {
  if (isEdit.value && props.container) {
    form.value = {
      name: props.container.name,
      serial_number: props.container.serial_number,
      location: props.container.location,
      latitude: props.container.latitude,
      longitude: props.container.longitude,
      status: Boolean(props.container.status),
    }
  } else {
    form.value = { name: '', serial_number: '', location: '', latitude: null, longitude: null, status: true }
  }
}

watch(() => props.modelValue, open => {
  if (open) resetForm()
})

function applyLocation({ latitude, longitude, locationName }) {
  form.value.latitude = latitude
  form.value.longitude = longitude

  if (locationName) {
    form.value.location = locationName
  }
}

async function submit() {
  const payload = {
    name: form.value.name,
    serial_number: form.value.serial_number,
    location: form.value.location,
    latitude: form.value.latitude,
    longitude: form.value.longitude,
    status: form.value.status,
  }

  const result = isEdit.value
    ? await updateContainer(props.container.id, payload)
    : await createContainer(payload)

  if (!result) return

  emit('saved')
  emit('update:modelValue', false)
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="560"
    persistent
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard :title="dialogTitle">
      <VCardText>
        <VForm @submit.prevent="submit">
          <VRow>
            <VCol cols="6">
              <VTextField
                v-model="form.name"
                label="Nombre"
                required
              />
            </VCol>
            <VCol cols="6">
              <VTextField
                v-model="form.serial_number"
                label="Número de serie"
                required
              />
            </VCol>
            <VCol cols="12">
              <VTextField
                v-model="form.location"
                label="Ubicación"
                required
              />
            </VCol>
            <VCol cols="12">
              <VSwitch
                v-model="form.status"
                label="Activo"
                color="primary"
                hide-details
              />
            </VCol>
            <VCol cols="12">
              <div class="location-summary pa-3 rounded-lg border border-gray-200 d-flex align-center justify-space-between dark:border-gray-700">
                <div class="d-flex align-center gap-3">
                  <VIcon
                    icon="bx-map-pin"
                    :color="hasCoordinates ? 'primary' : 'default'"
                    size="24"
                  />
                  <div>
                    <div class="text-body-2 font-weight-medium">
                      {{ hasCoordinates ? 'Ubicación en el mapa' : 'Sin ubicación asignada' }}
                    </div>
                    <div
                      v-if="hasCoordinates"
                      class="text-caption text-medium-emphasis"
                    >
                      {{ form.latitude }}, {{ form.longitude }}
                    </div>
                  </div>
                </div>
                <VBtn
                  variant="tonal"
                  color="primary"
                  size="small"
                  @click="locationDialog = true"
                >
                  <VIcon
                    icon="bx-map"
                    class="me-2"
                  />
                  {{ hasCoordinates ? 'Cambiar' : 'Elegir en el mapa' }}
                </VBtn>
              </div>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn
          variant="tonal"
          @click="emit('update:modelValue', false)"
        >
          Cancelar
        </VBtn>
        <VBtn
          color="primary"
          :loading="loading"
          :disabled="!isValid"
          @click="submit"
        >
          {{ isEdit ? 'Guardar' : 'Crear' }}
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <LocationPickerDialog
    v-model="locationDialog"
    :latitude="form.latitude"
    :longitude="form.longitude"
    @confirm="applyLocation"
  />
</template>
