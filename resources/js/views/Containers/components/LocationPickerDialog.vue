<script setup>
import { useToastStore } from '@/store/useToastStore'
import { useDebounceFn } from '@vueuse/core'
import { computed, ref, watch } from 'vue'
import LocationPickerMap from './LocationPickerMap.vue'

const props = defineProps({
  modelValue: { type: Boolean, required: true },
  latitude: { type: Number, default: null },
  longitude: { type: Number, default: null },
})

const emit = defineEmits(['update:modelValue', 'confirm'])

const toast = useToastStore()

// Centro de Ciudad de México por defecto cuando aún no hay ninguna coordenada.
const DEFAULT_CENTER = [19.4326, -99.1332]

const draftLatitude = ref(null)
const draftLongitude = ref(null)
const mapKey = ref(0)

// Nombre a proponer para autocompletar el campo "Ubicación" del formulario —
// viene del resultado de búsqueda elegido, o se resuelve por geocodificación
// inversa cuando el punto se marca en el mapa o con "Mi ubicación". Solo se
// invalida (null) si el usuario tipea las coordenadas a mano, ahí ya no hay
// un lugar concreto que proponer.
const selectedLocationName = ref(null)

const searchQuery = ref('')
const searchResults = ref([])
const searching = ref(false)
const locating = ref(false)

const hasLocation = computed(() => draftLatitude.value !== null && draftLongitude.value !== null)
const mapCenter = computed(() => (hasLocation.value ? [draftLatitude.value, draftLongitude.value] : DEFAULT_CENTER))
const mapZoom = computed(() => (hasLocation.value ? 16 : 5))

watch(() => props.modelValue, open => {
  if (!open) return

  draftLatitude.value = props.latitude
  draftLongitude.value = props.longitude
  searchQuery.value = ''
  searchResults.value = []
  selectedLocationName.value = null
  mapKey.value += 1
})

function jumpTo(lat, lng) {
  draftLatitude.value = Math.round(lat * 1e6) / 1e6
  draftLongitude.value = Math.round(lng * 1e6) / 1e6
  mapKey.value += 1
}

const runSearch = useDebounceFn(async () => {
  const query = searchQuery.value.trim()
  if (query.length < 3) {
    searchResults.value = []

    return
  }

  searching.value = true

  try {
    const url = `https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${encodeURIComponent(query)}`
    const response = await fetch(url, { headers: { Accept: 'application/json' } })

    searchResults.value = response.ok ? await response.json() : []
  } catch (err) {
    console.error(err)
    searchResults.value = []
  } finally {
    searching.value = false
  }
}, 500)

watch(searchQuery, runSearch)

function selectResult(result) {
  jumpTo(parseFloat(result.lat), parseFloat(result.lon))
  searchQuery.value = result.display_name
  searchResults.value = []
  selectedLocationName.value = result.display_name
}

// Geocodificación inversa (coordenadas -> nombre de lugar) para cuando el
// punto se elige clickeando/arrastrando en el mapa o con "Mi ubicación" — el
// buscador ya trae el nombre directo (selectResult), esto es solo para las
// otras dos formas de elegir un punto.
async function reverseGeocode(lat, lng) {
  searching.value = true

  try {
    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
    const response = await fetch(url, { headers: { Accept: 'application/json' } })
    const result = response.ok ? await response.json() : null

    selectedLocationName.value = result?.display_name ?? null
    searchQuery.value = selectedLocationName.value ?? searchQuery.value
  } catch (err) {
    console.error(err)
    selectedLocationName.value = null
  } finally {
    searching.value = false
  }
}

// Clic o arrastre del marcador directamente en el mapa.
function onMapInteraction({ latitude, longitude }) {
  draftLatitude.value = latitude
  draftLongitude.value = longitude
  searchResults.value = []
  reverseGeocode(latitude, longitude)
}

function useMyLocation() {
  if (!navigator.geolocation) {
    toast.showToast({ message: 'Tu navegador no soporta geolocalización.', tipo: 'error' })

    return
  }

  locating.value = true

  navigator.geolocation.getCurrentPosition(
    position => {
      jumpTo(position.coords.latitude, position.coords.longitude)
      reverseGeocode(position.coords.latitude, position.coords.longitude)
      locating.value = false
    },
    () => {
      toast.showToast({ message: 'No se pudo obtener tu ubicación actual.', tipo: 'error' })
      locating.value = false
    },
    { enableHighAccuracy: true, timeout: 10000 },
  )
}

function emitLatitude(rawValue) {
  draftLatitude.value = rawValue === '' || rawValue === null ? null : Number(rawValue)
  selectedLocationName.value = null
}

function emitLongitude(rawValue) {
  draftLongitude.value = rawValue === '' || rawValue === null ? null : Number(rawValue)
  selectedLocationName.value = null
}

function confirm() {
  emit('confirm', {
    latitude: draftLatitude.value,
    longitude: draftLongitude.value,
    locationName: selectedLocationName.value,
  })
  emit('update:modelValue', false)
}
</script>

<template>
  <VDialog
    :model-value="modelValue"
    max-width="900"
    persistent
    @update:model-value="emit('update:modelValue', $event)"
  >
    <VCard title="Ubicación del contenedor">
      <VCardText>
        <div class="position-relative mb-4">
          <VTextField
            v-model="searchQuery"
            label="Buscar dirección o lugar"
            prepend-inner-icon="bx-search-alt-2"
            variant="outlined"
            density="comfortable"
            clearable
            :loading="searching"
            autocomplete="off"
            @click:clear="searchResults = []"
          >
            <template #append>
              <VBtn
                variant="tonal"
                color="primary"
                :loading="locating"
                @click="useMyLocation"
              >
                <VIcon
                  icon="bx-current-location"
                  class="me-2"
                />
                Mi ubicación
              </VBtn>
            </template>
          </VTextField>

          <VCard
            v-if="searchResults.length"
            class="search-results"
            elevation="8"
          >
            <VList density="compact">
              <VListItem
                v-for="result in searchResults"
                :key="result.place_id"
                @click="selectResult(result)"
              >
                <template #prepend>
                  <VIcon
                    icon="bx-map-pin"
                    class="me-2 text-primary"
                  />
                </template>
                <VListItemTitle class="text-wrap">
                  {{ result.display_name }}
                </VListItemTitle>
              </VListItem>
            </VList>
          </VCard>
        </div>

        <LocationPickerMap
          :key="mapKey"
          :latitude="draftLatitude"
          :longitude="draftLongitude"
          :initial-center="mapCenter"
          :initial-zoom="mapZoom"
          @change="onMapInteraction"
        />

        <p class="mt-3 mb-2 text-body-2 text-medium-emphasis">
          Clic en el mapa o arrastra el marcador para ajustar la ubicación — o escribe las coordenadas exactas.
        </p>
        <VRow>
          <VCol cols="6">
            <VTextField
              :model-value="draftLatitude"
              type="number"
              label="Latitud"
              density="compact"
              @update:model-value="emitLatitude"
            />
          </VCol>
          <VCol cols="6">
            <VTextField
              :model-value="draftLongitude"
              type="number"
              label="Longitud"
              density="compact"
              @update:model-value="emitLongitude"
            />
          </VCol>
        </VRow>
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
          :disabled="!hasLocation"
          @click="confirm"
        >
          Confirmar ubicación
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
.search-results {
  position: absolute;
  z-index: 20;
  inset-inline: 0;
  inset-block-start: calc(100% - 4px);
  max-block-size: 260px;
  overflow-y: auto;
}
</style>
