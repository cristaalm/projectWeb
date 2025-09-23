import { computed, reactive } from 'vue'

export function useValidations({ shopData }) {
  if (!('value' in shopData)) {
    throw new Error('shopData debe ser una variable reactiva')
  }

  const originalForm = {
    name: '',
    contact_name: '',
    contact_email: '',
    phone: '',
    address: '',
    status: '',
  }

  const formErrors = reactive({ ...originalForm })
  
  const touchedFields = reactive({
    name: false,
    contact_name: false,
    contact_email: false,
    phone: false,
    address: false,
    status: false,
  })
  
  const validators = {
    name: value => {
      if (value === null || value === '') return 'El nombre no puede estar vacio'

      if (value.length > 150) return 'El nombre debe tener menos de 150 caracteres'

      return ''
    },
    contact_name: value => {
      if (value === null || value === '') return 'El nombre no puede estar vacio'
      
      if (value.length > 150) return 'El nombre debe tener menos de 150 caracteres'

      return ''
    },
    contact_email: value => {
      if (value === null || value === '') return 'El correo no puede estar vacio'
      
      const regex = /^[\w.%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i
      
      if (!regex.test(value)) return 'El correo debe ser valido.'
      
      return ''
    },
    phone: value => {
      if (value === null || value === '') return 'El telefono no puede estar vacio'

      value = value.replace(/\D/g, '')

      if (value.length !== 10) return 'El telefono debe tener 10 digitos.'

      return ''
    },
    address: value => {
      if (value === null || value === '') return 'La direccion no puede estar vacia'

      if (value.length > 255) return 'La direccion debe tener menos de 255 caracteres'
      
      return ''
    },
  } 
  
  const validateField = field => {
    if (validators[field]) {
      const value = shopData.value[field]

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
