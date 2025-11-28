import { computed, reactive } from 'vue'

export function useValidations({ badgeData }) {
  if (!('value' in badgeData)) {
    throw new Error('badgeData debe ser una variable reactiva')
  }

  const originalForm = {
    name: '',
    points_required: '',
    points_awared: '',
  }

  const formErrors = reactive({ ...originalForm })
  
  const touchedFields = reactive({
    name: false,
    points_required: false,
    points_awared: false,
  })
  
  const validators = {
    name: value => {
      if (value === null || value === '') return 'El nombre no puede estar vacío'

      if (value.length > 150) return 'El nombre debe tener menos de 150 caracteres'

      return ''
    },
    points_required: value => {
      if (isNaN(value) || value === '' || value === null || value <= 0) return 'El número de puntos requeridos debe ser un número positivo'

      if (value.toString().length > 150) return 'El número de puntos requeridos debe tener menos de 150 caracteres'

      return ''
    },
    points_awared: value => {
      if (isNaN(value) || value === '' || value === null || value <= 0) return 'El número de puntos de recompensa debe ser un número positivo'

      if (value.toString().length > 150) return 'El número de puntos de recompensa debe tener menos de 150 caracteres'
      
      return ''
    },
  } 
  
  const validateField = field => {
    if (validators[field]) {
      const value = badgeData.value[field]

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
