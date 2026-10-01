import { requestDelete, requestGet, requestPost, requestPut } from '@/services/requests'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useContainerManagement() {
  const error = ref(false)
  const loading = ref(false)
  const toast = useToastStore()

  const resetState = () => {
    error.value = false
    loading.value = false
  }

  const createContainer = async payload => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: 'containers', data: payload })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.container
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const updateContainer = async (containerId, payload) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPut({ url: `containers/${containerId}`, data: payload })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.container
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const deleteContainer = async containerId => {
    resetState()
    loading.value = true

    try {
      const response = await requestDelete({ url: `containers/${containerId}` })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return false
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return true
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return false
  }

  // El token no viene en el listado: se pide bajo demanda, solo cuando el
  // usuario lo quiere ver o copiar. Sin toast de éxito (lo decide quien llama).
  const getContainerToken = async containerId => {
    try {
      const response = await requestGet({ url: `containers/${containerId}/token` })

      if (response.success) return response.data.token

      toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })
    } catch (err) {
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    }

    return null
  }

  const regenerateContainerToken = async containerId => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: `containers/${containerId}/token/regenerate` })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.token
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  return {
    error,
    loading,
    createContainer,
    updateContainer,
    deleteContainer,
    getContainerToken,
    regenerateContainerToken,
  }
}
