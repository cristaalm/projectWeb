import { computed, reactive } from 'vue'

export function useValidations({ typeShopData }) {
  if (!('value' in typeShopData)) {
    throw new Error('typeShopData debe ser una variable reactiva')
  }

  const originalForm = {
    name: '',
  }

  const formErrors = reactive({ ...originalForm })
  
  const touchedFields = reactive({
    name: false,
  })
  
  const validators = {
    name: value => {
      if (value === null || value === '') return 'El nombre no puede estar vacio'

      if (value.length > 150) return 'El nombre debe tener menos de 150 caracteres'

      return ''
    },
  } 
  
  const validateField = field => {
    if (validators[field]) {
      const value = typeShopData.value[field]

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
