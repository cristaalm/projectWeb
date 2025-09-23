// path: directives/decimalTwo.js

export default {
  mounted(el, binding) {
    const useComma = binding.arg === 'comma'
    const decimalSeparator = useComma ? ',' : '.'
    const altSeparator = useComma ? '.' : ','
  
    let isProcessing = false
  
    const sanitizeInput = (value, forceTwoDecimals = false) => {
      // Reemplazar separador alternativo
      value = value.replace(altSeparator, decimalSeparator)
  
      // Permitir empezar con separador
      if (value === decimalSeparator) return value
  
      // Si empieza con separador, añadir 0
      if (value.startsWith(decimalSeparator)) {
        value = `0${value}`
      }
  
      // Dividir por el separador
      let parts = value.split(decimalSeparator)
      if (parts.length > 2) {
        value = parts[0] + decimalSeparator + parts.slice(1).join('')
        parts = value.split(decimalSeparator)
      }
  
      // Limpiar parte entera
      const integerPart = parts[0].replace(/\D/g, '')

      // Limpiar decimales
      let decimalPart = parts.length > 1 ? parts[1].replace(/\D/g, '') : ''
  
      // Si se pide forzar 2 decimales (blur), completar con ceros
      if (forceTwoDecimals) {
        decimalPart = decimalPart.padEnd(2, '0').slice(0, 2)
      } else {
        // Durante escritura, solo truncar a 2
        decimalPart = decimalPart.slice(0, 2)
      }
  
      // Reconstruir valor
      if (decimalPart) {
        return `${integerPart}${decimalSeparator}${decimalPart}`
      } else if (value.endsWith(decimalSeparator)) {
        return `${integerPart}${decimalSeparator}`
      } else {
        return integerPart || '0' // Si está vacío, devolver "0"
      }
    }
  
    const handleInput = e => {
      if (isProcessing) return
      isProcessing = true
  
      const input = e.target
      const oldValue = input.value
      const oldSelectionStart = input.selectionStart
  
      const newValue = sanitizeInput(oldValue)
  
      if (oldValue !== newValue) {
        input.value = newValue
  
        // Ajustar cursor
        let newCursor = oldSelectionStart
        const diff = oldValue.length - newValue.length
  
        if (diff > 0 && oldSelectionStart > 0) {
          newCursor = Math.max(0, oldSelectionStart - diff)
        }
  
        // Caso: acaba de insertar separador al final
        if (
          oldValue === oldValue.replace(/\D/g, '') &&
            newValue === oldValue + decimalSeparator &&
            oldSelectionStart === oldValue.length
        ) {
          newCursor = newValue.length
        }
  
        requestAnimationFrame(() => {
          input.setSelectionRange(newCursor, newCursor)
        })
      }
  
      if (oldValue !== newValue) {
        input.dispatchEvent(new CustomEvent('input', { bubbles: true, detail: newValue }))
      }
  
      isProcessing = false
    }
  
    const handleBlur = e => {
      if (isProcessing) return
      isProcessing = true
  
      const input = e.target
      const oldValue = input.value
  
      if (!oldValue) {
        input.value = `0${decimalSeparator}00`
        isProcessing = false
        input.dispatchEvent(new Event('input', { bubbles: true }))
        
        return
      }
  
      const newValue = sanitizeInput(oldValue, true) // 👈 forzar 2 decimales
  
      if (oldValue !== newValue) {
        input.value = newValue
        input.dispatchEvent(new Event('input', { bubbles: true }))
      }
  
      isProcessing = false
    }
  
    const handleKeyDown = e => {
      const key = e.key
      const value = e.target.value
  
      if (
        e.ctrlKey ||
          e.altKey ||
          ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter', 'Escape'].includes(key)
      ) {
        return
      }
  
      if (/^\d$/.test(key)) return
  
      if (key === decimalSeparator || key === altSeparator) {
        if (!value.includes(decimalSeparator)) return
        e.preventDefault()
        
        return
      }
  
      e.preventDefault()
    }
  
    const handlePaste = e => {
      console.log(e.target)
      setTimeout(() => {
        if (!isProcessing) {
          e.target.dispatchEvent(new Event('input', { bubbles: true }))
        }
      }, 10)
    }

    const handleFocus = e => {
      setTimeout(() => {
        e.target.select()
      }, 0)
    }
  
    // Registrar listeners
    el.addEventListener('input', handleInput)
    el.addEventListener('keydown', handleKeyDown)
    el.addEventListener('paste', handlePaste)
    el.querySelector('input').addEventListener('blur', handleBlur)
    el.querySelector('input').addEventListener('focus', handleFocus)
  
    // Cleanup
    el._decimalTwoCleanup = () => {
      el.removeEventListener('input', handleInput)
      el.removeEventListener('keydown', handleKeyDown)
      el.removeEventListener('paste', handlePaste)
      el.querySelector('input').removeEventListener('blur', handleBlur)
      el.querySelector('input').removeEventListener('focus', handleFocus)
    }
  },
  
  unmounted(el) {
    if (el._decimalTwoCleanup) {
      el._decimalTwoCleanup()
    }
  },
}
