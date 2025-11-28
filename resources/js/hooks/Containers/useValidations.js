import { computed, reactive } from 'vue'

export function useValidations({ containerData }) {
  if (!('value' in containerData)) {
    throw new Error('containerData debe ser una variable reactiva')
  }

  const originalForm = {
    name: '',
    serial_number: '',
    location: '',
  }

  const formErrors = reactive({ ...originalForm })
  
  const touchedFields = reactive({
    name: false,
    serial_number: false,
    location: false,
  })
  
  const validators = {
    name: value => {
      if (value === null || value === '') return 'El nombre no puede estar vacío'

      if (value.length > 150) return 'El nombre debe tener menos de 150 caracteres'

      return ''
    },
    serial_number: value => {
      if (value === null || value === '') return 'El número de serie no puede estar vacío'

      if (value.length > 150) return 'El número de serie debe tener menos de 150 caracteres'

      return ''
    },
    location: value => {
      if (value === null || value === '') return 'La ubicación no puede estar vacía'

      if (value.length > 255) return 'La ubicación debe tener menos de 255 caracteres'
      
      return ''
    },
  } 
  
  const validateField = field => {
    if (validators[field]) {
      const value = containerData.value[field]

      formErrors[field] = validators[field](value)
    }
  }
  
  const validateForm = () => {
    Object.keys(formErrors).forEach(field => validateField(field))
    
    return !Object.values(formErrors).some(Boolean)
  }

  const touchField = field => {
    touchedFields[field] = true
  }
  
  const formValidate = computed(() => validateForm())

  const resetValidations = () => {
    Object.assign(formErrors, originalForm)
    Object.keys(touchedFields).forEach(key => touchedFields[key] = false)
  }
  
  return {
    formValidate,
    formErrors,
    touchField,
    touchedFields,
    resetValidations,
  }
}
