// stores/useDialogStore.ts
import { defineStore } from 'pinia'
import { ref } from 'vue'

let uid = 0

export const useDialogStore = defineStore('dialog', () => {
  const dialogs = ref([])

  // Función para abrir un diálogo
  function showDialog(options) {
    const id = ++uid

    const dialog = {
      id,
      title: options.title || '',
      text: options.text || '',
      type: options.type || 'alert', // 'alert', 'confirm', 'loading'
      confirmText: options.confirmText || (options.type === 'confirm' ? 'Sí' : 'Aceptar'),
      cancelText: options.cancelText || 'Cancelar',
      resolvePromise: null,
      model: true,
    }

    dialogs.value.push(dialog)

    // Creamos una promesa que se resolverá cuando el usuario interactúe
    return new Promise(resolve => {
      dialog.resolvePromise = resolve
    })
  }

  // Función para cerrar un diálogo y resolver la promesa
  function closeDialog(id, value = null) {
    const dialog = dialogs.value.find(d => d.id === id)
    if (dialog) {
      if (dialog.resolvePromise) {
        dialog.resolvePromise(value)
      }
      dialogs.value = dialogs.value.filter(d => d.id !== id)
    }
  }

  return { dialogs, showDialog, closeDialog }
})
