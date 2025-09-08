<script setup>
import { useToastStore } from '@/store/useToastStore'
import { AlertCircle, AlertTriangle, CheckCircle, Info, X } from 'lucide-vue-next'
import { storeToRefs } from 'pinia'

const store = useToastStore()
const { toasts } = storeToRefs(store)
const { removeToast } = store

const getToastTypeClass = tipo => {
  switch (tipo) {
  case 'error':
    return 'bg-red-100 text-red-800 dark:bg-red-900/80 dark:text-red-100 dark:border dark:border-red-700'
  case 'warning':
    return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/80 dark:text-yellow-100 dark:border dark:border-yellow-700'
  case 'success':
    return 'bg-green-100 text-green-800 dark:bg-green-900/80 dark:text-green-100 dark:border dark:border-green-700'
  case 'info':
  default:
    return 'bg-blue-100 text-blue-800 dark:bg-blue-900/80 dark:text-blue-100 dark:border dark:border-blue-700'
  }
}

const getIconComponent = tipo => {
  switch (tipo) {
  case 'error': return AlertCircle
  case 'warning': return AlertTriangle
  case 'success': return CheckCircle
  case 'info':
  default: return Info
  }
}
</script>

<template>
  <div class="fixed top-0 right-0 sm:right-5 p-4 transition-opacity w-screen sm:w-fit duration-300 z-[2147483647] flex flex-col gap-3">
    <TransitionGroup
      name="toast"
      tag="div"
      class="flex flex-col gap-3 justify-end items-end max-w-[550px]"
    >
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="p-4 rounded-lg shadow-lg flex items-center gap-3 min-w-[350px] w-full sm:w-fit max-w-[550px] backdrop-blur-sm"
        :class="getToastTypeClass(toast.tipo)"
      >
        <component
          :is="getIconComponent(toast.tipo)"
          class="w-6 h-6 shrink-0"
        />
        <p class="break-words max-w-[calc(100%-70px)] text-sm font-medium mb-0">
          {{ toast.message }}
        </p>
        <button
          v-if="toast.persistente || toast.disabled"
          class="ml-auto hover:text-black text-gray-600 dark:text-gray-300 dark:hover:text-white transition-colors"
          @click="removeToast(toast.id)"
        >
          <X class="w-5 h-5" />
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}

.toast-enter-active,
.toast-leave-active {
  transition: opacity 0.5s ease, transform 0.5s ease;
}
</style>
