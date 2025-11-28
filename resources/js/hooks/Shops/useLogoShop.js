import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import logo_placeholder from '@images/placeholders/shop.jpg?url'
import { computed, ref } from 'vue'

export function useLogoShop() {
  const loading = ref(false)
  const toast = useToastStore()
  const authStore = useAuthStore()

  // Estado local del logo
  const currentLogoFile = ref(null)     // Archivo seleccionado (File)
  const logoAction = ref('none')        // 'upload', 'delete', 'none'
  const initialHasLogo = ref(false)     // ¿Tenía logo al abrir el modal?
  const shopData = ref(null)

  // URL temporal para previsualizar
  const previewUrl = ref('')

  // Reinicia el estado del logo
  const resetLogoState = () => {
    currentLogoFile.value = null
    logoAction.value = 'none'
    previewUrl.value = ''
  }

  // Inicializa el estado
  const initializeLogoState = shop => {
    shopData.value = { ...shop } // Guardamos copia local
    initialHasLogo.value = !!shop.logo
    resetLogoState()
  }


  // Verifica si hay cambios reales
  const hasChanges = computed(() => {
    return (
      (initialHasLogo.value && logoAction.value === 'delete') ||
      (!initialHasLogo.value && logoAction.value === 'upload') ||
      (initialHasLogo.value && logoAction.value === 'upload')
    )
  })

  const shouldShowDbLogo = computed(() => {
    return initialHasLogo.value && logoAction.value === 'none'
  })
  
  const displayImageUrl = computed(() => {
    if (previewUrl.value) {
      return previewUrl.value
    }
    if (shouldShowDbLogo.value && shopData.value?.logo) {
      return `/storage/alliances/${shopData.value.id}/logo.${shopData.value.ext}`
    }
    
    return logo_placeholder
  })


  // Maneja selección de archivo
  const handleFileChange = event => {
    const file = event.target.files[0]
    if (!file) return

    currentLogoFile.value = file
    logoAction.value = 'upload'

    // Previsualización
    const reader = new FileReader()

    reader.onload = e => {
      previewUrl.value = e.target.result
    }
    reader.readAsDataURL(file)
  }

  // Elimina logo localmente
  const handleDeleteLocal = () => {
    currentLogoFile.value = null
    previewUrl.value = ''
    logoAction.value = 'delete'
  }

  // Guarda en el backend
  const saveLogo = async allianceId => {
    if (!hasChanges.value) return true

    loading.value = true
    try {
      const formData = new FormData()

      if (logoAction.value === 'upload' && currentLogoFile.value) {
        formData.append('logo', currentLogoFile.value)
      }

      // Si es 'delete', no enviamos archivo → backend lo elimina

      const response = await requestPost({
        url: `alianzas/logo/${allianceId}`,
        data: formData,
        token: authStore.getAccessToken(),
        formData: true,
      })

      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        return true
      } else {
        toast.showToast({
          message: response.message || 'Error al guardar el logo.',
          tipo: 'error',
          duration: 4000,
        })
        
        return false
      }
    } catch (error) {
      console.error('Error al guardar logo:', error)
      toast.showToast({ message: 'Error al guardar el logo.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    currentLogoFile,
    logoAction,
    previewUrl,
    hasChanges,
    handleFileChange,
    handleDeleteLocal,
    saveLogo,
    initializeLogoState,
    resetLogoState,
    displayImageUrl,
    initialHasLogo,
  }
}
