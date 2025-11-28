<template>
  <section
    id="tecnico"
    class="pt-20 px-4 bg-muted/30"
  >
    <VContainer class="max-w-7xl">
      <div class="text-center mb-12 space-y-4">
        <h2 class="text-4xl md:text-5xl font-bold text-balance font-poppins">
          Tecnología de vanguardia
        </h2>
        <p class="text-xl text-muted-foreground max-w-2xl mx-auto text-pretty font-poppins">
          Un ecosistema completo que integra hardware, software y servicios en la nube.
        </p>
      </div>

      <!-- Imágenes en columna vertical -->
      <div class="flex flex-col md:flex-row items-center gap-8 mb-12">
        <div class="relative">
          <div
            v-if="loadingScanner"
            class="absolute inset-0 flex items-center justify-center bg-gray-200/50 rounded-lg"
          >
            <VProgressCircular
              :size="40"
              :width="3"
              color="primary"
              indeterminate
            />
          </div>
          <img
            :src="imageUrls.scanner"
            alt="Escáner QR"
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
              :size="40"
              :width="3"
              color="primary"
              indeterminate
            />
          </div>
          <img
            :src="imageUrls.camara"
            alt="Cámara Raspberry Pi 4"
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
              :size="40"
              :width="3"
              color="primary"
              indeterminate
            />
          </div>
          <img
            :src="imageUrls.raspberry"
            alt="Raspberry Pi 4"
            class="max-w-[350px] w-full object-contain"
            @load="onImageLoad('raspberry')"
            @error="onImageError"
          >
        </div>
      </div>

      <!-- Nuevo apartado: Imagen + Tabla -->
      <div class="flex flex-col md:flex-row gap-8 mb-12">
        <!-- Imagen -->
        <div class="w-full md:w-1/2 relative h-full">
          <div
            v-if="loadingNuevaImagen"
            class="absolute inset-0 flex items-center justify-center bg-gray-200/50 rounded-lg z-10"
          >
            <VProgressCircular
              :size="40"
              :width="3"
              color="primary"
              indeterminate
            />
          </div>
          <img
            :src="imageUrls.nuevaImagen"
            alt="Diagrama del sistema"
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
                <th class="border-b border-gray-300 p-3 text-start font-semibold font-poppins text-xl">
                  Descripción del hardware
                </th>
                <th class="border-b border-gray-300 p-3 text-end font-semibold font-poppins text-xl">
                  Contenedor Inteligente
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="border-b border-gray-300 p-3 text-start font-poppins">
                  Material
                </td>
                <td class="border-b border-gray-300 p-3 text-end font-poppins">
                  Melamina
                </td>
              </tr>
              <tr>
                <td class="border-b border-gray-300 p-3 text-start font-poppins">
                  Cámara
                </td>
                <td class="border-b border-gray-300 p-3 text-end font-poppins">
                  Full HD NoIR V2 
                </td>
              </tr>
              <tr>
                <td class="border-b border-gray-300 p-3 text-start font-poppins">
                  Pantalla
                </td>
                <td class="border-b border-gray-300 p-3 text-end font-poppins">
                  Display LCD 16x2
                </td>
              </tr>
              <tr>
                <td class="border-b border-gray-300 p-3 text-start font-poppins">
                  Escáner
                </td>
                <td class="border-b border-gray-300 p-3 text-end font-poppins">
                  Generico 1D y 2D/QR
                </td>
              </tr>
              <tr>
                <th class="border-b border-gray-300 p-3 text-start font-poppins font-semibold text-xl">
                  Descripción del software
                </th>
                <th class="border-b border-gray-300 p-3 text-end font-poppins font-semibold text-xl" />
              </tr>
              <tr>
                <td class="border-b border-gray-300 p-3 text-start font-poppins">
                  IA
                </td>
                <td class="border-b border-gray-300 p-3 text-end font-poppins">
                  GPT-4o para clasificación de materiales
                </td>
              </tr>
              <tr>
                <td class="border-b border-gray-300 p-3 text-start font-poppins">
                  Servidor
                </td>
                <td class="border-b border-gray-300 p-3 text-end font-poppins">
                  Render
                </td>
              </tr>
              <tr>
                <td class="border-b border-gray-300 p-3 text-start font-poppins">
                  Base de datos
                </td>
                <td class="border-b border-gray-300 p-3 text-end font-poppins">
                  PosgreSQL
                </td>
              </tr>
              <tr>
                <td class="border-b border-gray-300 p-3 text-start font-poppins">
                  Lenguajes y Frameworks
                </td>
                <td class="border-b border-gray-300 p-3 text-end font-poppins">
                  Laravel, Vue.js, Tailwind CSS, Kotlin, JavaScript, PHP
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </VContainer>
  </section>
</template>

<script setup>
import { ref } from 'vue'

// Estados de carga para cada imagen
const loadingScanner = ref(true)
const loadingCamara = ref(true)
const loadingRaspberry = ref(true)
const loadingNuevaImagen = ref(true)

// URLs de las imágenes
const imageUrls = {
  scanner: '/images/componentes/Escaner.png',
  camara: '/images/componentes/Camara.png',
  raspberry: '/images/componentes/Raspberry4.png',
  nuevaImagen: '/images/contenedor/contenedor.png', // Ajusta la ruta según tu imagen
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

<style scoped>
/* Eliminamos todos los estilos personalizados */
</style>
