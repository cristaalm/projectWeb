// path: directives/numberOnly.js

export default {
  mounted(el, binding) {
    // Obtener el valor máximo de longitud desde el argumento (binding.arg)
    const maxLength = binding.arg ? parseInt(binding.arg, 10) : Infinity

    const handleInput = e => {
      let value = e.target.value

      // Eliminar caracteres no numéricos
      value = value.replace(/\D/g, '')

      // Truncar a la longitud máxima si se especificó
      if (maxLength && !isNaN(maxLength)) {
        value = value.slice(0, maxLength)
      }

      // Si queda vacío, forzar a "0"
      if (value === '' || value < 1) {
        value = '0'
      }

      // Solo actualizar si cambió
      if (e.target.value !== value) {
        const selectionStart = e.target.selectionStart
        const selectionEnd = e.target.selectionEnd

        e.target.value = value

        // Ajustar cursor (evitar que salte al final)
        const diff = e.target.value.length - value.length
        const newCursor = Math.max(0, selectionStart - diff)

        // Si se estableció "0" automáticamente, colocar cursor al final
        if (value === '0' && e.target.value === '0') {
          e.target.setSelectionRange(1, 1) // cursor después del 0
        } else {
          e.target.setSelectionRange(newCursor, newCursor)
        }

        // Emitir input para mantener v-model sincronizado
        e.target.dispatchEvent(new Event('input', { bubbles: true }))
      }
    }

    // Prevenir teclas no numéricas antes de que se inserten
    const handleKeyDown = e => {
      const key = e.key

      // Permitir teclas de control
      if (
        e.ctrlKey ||
        e.altKey ||
        ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter', 'Escape'].includes(key)
      ) {
        return
      }

      // Bloquear si no es dígito
      if (!/^\d$/.test(key)) {
        e.preventDefault()
        
        return
      }

      // Bloquear si alcanzó el máximo y no está seleccionando texto para reemplazar
      if (
        maxLength &&
        !isNaN(maxLength) &&
        e.target.value.length >= maxLength &&
        e.target.selectionStart === e.target.selectionEnd // No hay selección
      ) {
        e.preventDefault()
      }
    }

    // Manejar pegado
    const handlePaste = e => {
      setTimeout(() => {
        e.target.dispatchEvent(new Event('input', { bubbles: true }))
      }, 10)
    }

    const handleFocus = e => {
      setTimeout(() => {
        e.target.select()
      }, 0)
    }

    // Asegurar que el valor inicial no esté vacío
    if (el.value === '' || el.value == null || el.value < 1) {
      el.value = '0'
      el.dispatchEvent(new Event('input', { bubbles: true }))
    }

    // Registrar listeners
    el.addEventListener('input', handleInput)
    el.addEventListener('keydown', handleKeyDown)
    el.addEventListener('paste', handlePaste)
    el.querySelector('input').addEventListener('focus', handleFocus)

    // Cleanup
    el._numberOnlyCleanup = () => {
      el.removeEventListener('input', handleInput)
      el.removeEventListener('keydown', handleKeyDown)
      el.removeEventListener('paste', handlePaste)
      el.querySelector('input').removeEventListener('focus', handleFocus)
    }
  },

  unmounted(el) {
    if (el._numberOnlyCleanup) {
      el._numberOnlyCleanup()
    }
  },
}
