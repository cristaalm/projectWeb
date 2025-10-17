import { computed, reactive } from 'vue'

export function useCreateValidations({ userData }) {
  if (!('value' in userData)) {
    throw new Error('userData debe ser una variable reactiva')
  }

  const originalForm = {
    name: "",
    last_name: "",
    email: "",
    phone: "",
    curp: "",
    role: null,
    alliance: null,
  }

  const formErrors = reactive({ ...originalForm })
  
  const touchedFields = reactive({
    name: false,
    last_name: false,
    email: false,
    phone: false,
    curp: false,
    role: false,
    alliance: false,
  })
  
  const validators = {
    name: value => {
      if (value === null || value === '') return 'El nombre no puede estar vacio'

      if (value.length > 150) return 'El nombre debe tener menos de 150 caracteres'

      return ''
    },
    last_name: value => {
      if (value === null || value === '') return 'El nombre no puede estar vacio'
      
      if (value.length > 150) return 'El nombre debe tener menos de 150 caracteres'

      return ''
    },
    email: value => {
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
    curp: value => {
      if (value === null || value === '') return 'La CURP no puede estar vacia'

      // eslint-disable-next-line
      const regex = /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/

      if (!regex.test(value)) return 'La CURP debe ser valida.'

      if (value.length > 18) return 'La CURP debe tener menos de 18 caracteres'
      
      return ''
    },
    role: value => {
      if (value === null) return 'Selecciona un rol'
      
      return ''
    },
    alliance: value => {
      if (value == null && userData.value.role == 4) return 'Selecciona una alianza'
      
      return ''
    },
  } 
  
  const validateField = field => {
    if (validators[field]) {
      const value = userData.value[field]

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
