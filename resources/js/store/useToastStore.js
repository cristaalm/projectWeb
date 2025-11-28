// stores/useToastStore.ts
import { defineStore } from 'pinia'
import { ref } from 'vue'

let uid = 0

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([])

  function showToast({ message, tipo = 'info', duration = 3000, persistente = false, disabled = true }) {
    const id = ++uid

    const toast = {
      id,
      message,
      tipo,
      persistente,
      disabled,
    }

    toasts.value.push(toast)

    if (!persistente) {
      setTimeout(() => removeToast(id), duration)
    }
  }

  function removeToast(id) {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index !== -1) {
      toasts.value.splice(index, 1)
    }
  }

  return { toasts, showToast, removeToast }
})
