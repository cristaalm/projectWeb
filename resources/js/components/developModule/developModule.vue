<template>
  <div class="relative w-full h-[calc(100vh-200px)] p-4 rounded-lg shadow-lg grid grid-cols-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-slate-900 dark:to-slate-800 overflow-hidden">
    <!-- Nubes decorativas SOLO en modo claro -->
    <div class="absolute inset-0 z-0 pointer-events-none dark:hidden">
      <svg
        v-for="n in 8"
        :key="n"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 512 512"
        class="absolute opacity-1 animate-cloud"
        :style="getRandomCloudStyle()"
      >
        <g><path
          fill="#FFFFFF"
          d="M443.875 272.982c4.238-59.343-50.4-105.087-107.758-91.164-11.628-52.393-58.363-91.574-114.257-91.574-66.241 0-119.656 55.044-116.937 121.884l-.107-.002C46.927 212.126 0 259.053 0 316.941c0 56.526 44.748 102.593 100.751 104.729v.086h336.715c41.164 0 74.534-33.37 74.534-74.534 0-39.002-29.964-70.988-68.125-74.24z"
          opacity="1"
          data-original="#d8ecfe"
          class=""
        /><path
          fill="#FFFFFF"
          d="M443.875 272.982c4.238-59.344-50.4-105.087-107.758-91.164-11.628-52.393-58.363-91.574-114.257-91.574-18.496 0-35.99 4.295-51.543 11.936 3.9-.393 7.855-.598 11.858-.598 55.894 0 102.629 39.181 114.257 91.574 57.358-13.924 111.996 31.82 107.758 91.164 38.161 3.253 68.125 35.238 68.125 74.241 0 26.65-13.997 50.019-35.03 63.195h.182c41.164 0 74.534-33.37 74.534-74.534-.001-39.002-29.965-70.988-68.126-74.24z"
          opacity="1"
          data-original="#c4e2ff"
          class=""
        /></g>
      </svg>
    </div>

    <!-- Estrellas decorativas SOLO en modo oscuro -->
    <template
      v-for="n in 50"
      :key="'s'+n"
    >
      <div
        class="hidden absolute z-0 bg-white rounded-full opacity-80 dark:block"
        :style="getRandomStarStyle()"
      />
    </template>

    <!-- Contenido principal -->
    <div class="flex z-10 flex-col col-span-12 justify-center items-center text-center">
      <h1 class="mb-4 text-4xl font-extrabold text-blue-800 dark:text-slate-500">
        🚧 Módulo <span class="text-amber-500 dark:text-slate-200">{{ props.title }}</span> Está en Desarrollo 🚧
      </h1>
      <p class="text-lg text-gray-700 dark:text-slate-300">
        Estamos trabajando arduamente para traerte una experiencia increíble. ¡Vuelve pronto!
      </p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    default: 'Actual',
  },
})

// Estrellas
function getRandomStarStyle() {
  const top = Math.random() * 100
  const left = Math.random() * 100
  const size = Math.random() * 2 + 1

  return {
    top: `${top}%`,
    left: `${left}%`,
    width: `${size}px`,
    height: `${size}px`,
  }
}

// Nubes (posición + escala aleatoria)
function getRandomCloudStyle() {
  const top = Math.random() * 80
  const left = Math.random() * 90
  const scale = 0.8 + Math.random() * 0.8
  const delay = `${Math.random() * 5}s`
  const duration = `${20 + Math.random() * 10}s`

  return {
    top: `${top}%`,
    left: `${left}%`,
    width: '100px',
    height: '100px',
    '--scale': scale, // Guardamos la escala como variable CSS
    animationDelay: delay,
    animationDuration: duration,
  }
}
</script>

<style scoped>
@keyframes cloud-float {
  0% {
    transform: translateX(0) scale(var(--scale));
  }

  50% {
    transform: translateX(50px) scale(var(--scale));
  }

  100% {
    transform: translateX(0) scale(var(--scale));
  }
}

.animate-cloud {
  animation-iteration-count: infinite;
  animation-name: cloud-float;
  animation-timing-function: ease-in-out;
  transform: scale(var(--scale)); /* Asegura que la escala se aplique incluso cuando no hay animación */
  will-change: transform;
}
</style>
