import { computed, reactive } from 'vue'

export function useValidations({ passData }) {
  if (!('value' in passData)) {
    throw new Error('passData debe ser una variable reactiva')
  }

  const originalForm = {
    password: '',
    password_confirmation: '',
    current_password: '',
  }

  const formErrors = reactive({ ...originalForm })
  
  const touchedFields = reactive({
    password: false,
    password_confirmation: false,
    current_password: false,
  })
  
  const validators = {
    password: value => {
      if (value === null || value === '') return 'La contraseña no puede estar vacia'

      if (value.length < 8) return 'La contraseña debe tener al menos 8 caracteres'

      // un numero
      if (!/\d/.test(value)) return 'La contraseña debe contener al menos un numero'

      // una mayuscula
      if (!/[A-Z]/.test(value)) return 'La contraseña debe contener al menos una mayuscula'

      // una minuscula
      if (!/[a-z]/.test(value)) return 'La contraseña debe contener al menos una minuscula'

      // un simbolo
      if (!/[!@#$%^&*()_+\-=[\]{};':",./<>?]/.test(value)) return 'La contraseña debe contener al menos un simbolo'

      return ''
    },
    password_confirmation: value => {
      if (value === null || value === '') return 'La confirmacion de la contraseña no puede estar vacia'

      if (value !== passData.value.password) return 'Las confirmación de contraseña no coincide'

      return ''
    },
    current_password: value => {
      if (value === null || value === '') return 'La contraseña actual no puede estar vacia'

      return ''
    },
  } 
  
  const validateField = field => {
    if (validators[field]) {
      const value = passData.value[field]

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
