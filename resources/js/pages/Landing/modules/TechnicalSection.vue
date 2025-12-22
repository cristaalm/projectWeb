<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, tm } = useI18n()

// Estados de carga
const loadingScanner = ref(true)
const loadingCamara = ref(true)
const loadingRaspberry = ref(true)
const loadingNuevaImagen = ref(true)

// Datos i18n
const hardwareRows = computed(() => tm('landing.technical.table.hardware'))
const softwareRows = computed(() => tm('landing.technical.table.software'))

// URLs de imágenes
const imageUrls = {
  scanner: '/images/componentes/Escaner.png',
  camara: '/images/componentes/Camara.png',
  raspberry: '/images/componentes/Raspberry4.png',
  nuevaImagen: '/images/contenedor/contenedor.png',
}

const onImageLoad = imageName => {
  if (imageName === 'scanner') loadingScanner.value = false
  if (imageName === 'camara') loadingCamara.value = false
  if (imageName === 'raspberry') loadingRaspberry.value = false
  if (imageName === 'nuevaImagen') loadingNuevaImagen.value = false
}

const onImageError = event => {
  event.target.src = '/images/placeholder.png'
}
</script>

<template>
  <section
    id="tecnico"
    class="pt-20 px-4 bg-muted/30"
  >
    <VContainer class="max-w-7xl">
      <!-- Header -->
      <div class="text-center mb-12 space-y-4">
        <h2 class="text-4xl md:text-5xl font-bold text-balance font-poppins">
          {{ t('landing.technical.title') }}
        </h2>
        <p class="text-xl text-muted-foreground max-w-2xl mx-auto text-pretty font-poppins">
          {{ t('landing.technical.subtitle') }}
        </p>
      </div>

      <!-- Imágenes -->
      <div class="flex flex-col md:flex-row items-center gap-8 mb-12">
        <div class="relative">
          <div
            v-if="loadingScanner"
            class="absolute inset-0 flex items-center justify-center bg-gray-200/50 rounded-lg"
          >
            <VProgressCircular
              indeterminate
              size="40"
              width="3"
              color="primary"
            />
          </div>
          <img
            :src="imageUrls.scanner"
            :alt="t('landing.technical.images.scannerAlt')"
            class="max-w-[350px] w-full object-contain"
            @load="onImageLoad('scanner')"
            @error="onImageError"
          >
        </div>

        <div class="relative">
          <div
            v-if="loadingCamara"
            class="absolute inset-0 flex items-center justify-center bg-gray-200/50 rounded-lg"
          >
            <VProgressCircular
              indeterminate
              size="40"
              width="3"
              color="primary"
            />
          </div>
          <img
            :src="imageUrls.camara"
            :alt="t('landing.technical.images.cameraAlt')"
            class="max-w-[350px] w-full object-contain"
            @load="onImageLoad('camara')"
            @error="onImageError"
          >
        </div>

        <div class="relative">
          <div
            v-if="loadingRaspberry"
            class="absolute inset-0 flex items-center justify-center bg-gray-200/50 rounded-lg"
          >
            <VProgressCircular
              indeterminate
              size="40"
              width="3"
              color="primary"
            />
          </div>
          <img
            :src="imageUrls.raspberry"
            :alt="t('landing.technical.images.raspberryAlt')"
            class="max-w-[350px] w-full object-contain"
            @load="onImageLoad('raspberry')"
            @error="onImageError"
          >
        </div>
      </div>

      <!-- Imagen + Tabla -->
      <div class="flex flex-col md:flex-row gap-8 mb-12">
        <!-- Imagen -->
        <div class="w-full md:w-1/2 relative">
          <div
            v-if="loadingNuevaImagen"
            class="absolute inset-0 flex items-center justify-center bg-gray-200/50 rounded-lg z-10"
          >
            <VProgressCircular
              indeterminate
              size="40"
              width="3"
              color="primary"
            />
          </div>
          <img
            :src="imageUrls.nuevaImagen"
            :alt="t('landing.technical.images.diagramAlt')"
            class="w-full h-full object-cover rounded-lg"
            @load="onImageLoad('nuevaImagen')"
            @error="onImageError"
          >
        </div>

        <!-- Tabla -->
        <div class="w-full md:w-1/2">
          <table class="w-full border-collapse">
            <thead>
              <tr>
                <th class="border-b p-3 text-start font-semibold text-xl font-poppins">
                  {{ t('landing.technical.table.hardwareTitle') }}
                </th>
                <th class="border-b p-3 text-end font-semibold text-xl font-poppins">
                  {{ t('landing.technical.table.productTitle') }}
                </th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="(row, index) in hardwareRows"
                :key="`h-${index}`"
              >
                <td class="border-b p-3 text-start font-poppins">
                  {{ row.label }}
                </td>
                <td class="border-b p-3 text-end font-poppins">
                  {{ row.value }}
                </td>
              </tr>

              <tr>
                <th class="border-b p-3 text-start font-semibold text-xl font-poppins">
                  {{ t('landing.technical.table.softwareTitle') }}
                </th>
                <th class="border-b p-3" />
              </tr>

              <tr
                v-for="(row, index) in softwareRows"
                :key="`s-${index}`"
              >
                <td class="border-b p-3 text-start font-poppins">
                  {{ row.label }}
                </td>
                <td class="border-b p-3 text-end font-poppins">
                  {{ row.value }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </VContainer>
  </section>
</template>
