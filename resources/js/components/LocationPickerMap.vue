<script setup>
import { LMap, LMarker, LTileLayer } from '@vue-leaflet/vue-leaflet'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
  latitude: { type: Number, default: null },
  longitude: { type: Number, default: null },
  initialCenter: { type: Array, required: true },
  initialZoom: { type: Number, required: true },
  height: { type: String, default: '460px' },
})

const emit = defineEmits(['change'])

// Pin propio en el verde de marca (primary) en vez del marcador azul por
// defecto de Leaflet — más coherente con el resto de la interfaz.
const MARKER_ICON = new L.DivIcon({
  className: 'container-location-marker',
  html: `
    <svg width="34" height="44" viewBox="0 0 34 44" xmlns="http://www.w3.org/2000/svg">
      <path d="M17 0C7.6 0 0 7.6 0 17c0 12.4 17 27 17 27s17-14.6 17-27C34 7.6 26.4 0 17 0Z" fill="#05D16E" stroke="#0B3B2E" stroke-width="1.5" />
      <circle cx="17" cy="17" r="6.5" fill="#0B3B2E" />
    </svg>
  `,
  iconSize: [34, 44],
  iconAnchor: [17, 44],
})

function setCoordinates(lat, lng) {
  emit('change', {
    latitude: Math.round(lat * 1e6) / 1e6,
    longitude: Math.round(lng * 1e6) / 1e6,
  })
}

function onMapClick(event) {
  setCoordinates(event.latlng.lat, event.latlng.lng)
}

function onMarkerDragEnd(event) {
  const { lat, lng } = event.target.getLatLng()

  setCoordinates(lat, lng)
}
</script>

<template>
  <div
    class="location-map border border-gray-200 rounded-lg dark:border-gray-700"
    :style="{ 'block-size': height }"
  >
    <LMap
      :zoom="initialZoom"
      :center="initialCenter"
      :use-global-leaflet="false"
      @click="onMapClick"
    >
      <LTileLayer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        attribution="&copy; OpenStreetMap contributors"
      />
      <LMarker
        v-if="latitude !== null && longitude !== null"
        :lat-lng="[latitude, longitude]"
        :icon="MARKER_ICON"
        draggable
        @dragend="onMarkerDragEnd"
      />
    </LMap>
  </div>
</template>

<style scoped>
.location-map {
  position: relative;
  z-index: 0;
  overflow: hidden;
  cursor: crosshair;
}

.location-map :deep(.leaflet-container) {
  block-size: 100%;
  font-family: inherit;
}
</style>
