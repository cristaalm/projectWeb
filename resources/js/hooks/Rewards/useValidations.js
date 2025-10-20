import { computed, reactive, ref, watch } from 'vue'
import { isValid, parse } from 'date-fns'

export function useValidations({ rewardData }) {
  if (!('value' in rewardData)) {
    throw new Error('rewardData debe ser una variable reactiva')
  }

  const isUnlimitedStock = ref(false)
  const isIndefiniteExpiration = ref(false)
  
  watch(() => isUnlimitedStock.value, () => {
    if (isUnlimitedStock.value) {
      rewardData.value.stock = ""
      validateField('stock')
      touchedFields.stock = true
    }
  })

  watch(() => isIndefiniteExpiration.value, () => {
    if (isIndefiniteExpiration.value) {
      rewardData.value.expires_at = "DD/MM/YYYY"
      validateField('expires_at')
      touchedFields.expires_at = true
    }
  })

  const originalForm = {
    name: "",
    description: "",
    points_required: "",
    stock: "",
    expires_at: "",
  }

  const formErrors = reactive({ ...originalForm })
  
  const touchedFields = reactive({
    name: false,
    description: false,
    points_required: false,
    stock: false,
    expires_at: false,
  })
  
  const validators = {
    name: value => {
      if (value === null || value === '') return 'El nombre no puede estar vacio'

      if (value.length > 150) return 'El nombre debe tener menos de 150 caracteres'

      return ''
    },
    description: value => {
      if (value === null || value === '') return 'La descripcion no puede estar vacia'
      
      if (value.length > 255) return 'La descripcion debe tener menos de 255 caracteres'

      return ''
    },
    points_required: value => {
      if (!value) return 'Los puntos requeridos debe ser mayor a 0'
      
      return ''
    },
    stock: value => {
      if (isUnlimitedStock.value) return ''
      
      if (!value) return 'El stock debe ser mayor a 0'
      
      return ''
    },
    expires_at: value => {
      if (isIndefiniteExpiration.value) return ''
      
      if (!value) return 'La fecha de expiracion es incorrecta'

      if (value == 'DD/MM/YYYY' || value == null) return 'Seleccione una fecha de expiracion'
  
      const date = parse(value, 'dd/MM/yyyy', new Date())
  
      if (!isValid(date)) return 'La fecha de expiracion es incorrecta'
  
      return ''
    },
  } 
  
  const validateField = field => {
    if (validators[field]) {
      const value = rewardData.value[field]

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
    isUnlimitedStock,
    isIndefiniteExpiration,
  }
}
