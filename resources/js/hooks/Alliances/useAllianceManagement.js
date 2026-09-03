import { requestDelete, requestPost, requestPut } from '@/services/requests'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useAllianceManagement() {
  const error = ref(false)
  const loading = ref(false)
  const toast = useToastStore()

  const resetState = () => {
    error.value = false
    loading.value = false
  }

  const createAlliance = async payload => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: 'alliances', data: payload })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.alliance
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const updateAlliance = async (allianceId, payload) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPut({ url: `alliances/${allianceId}`, data: payload })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.alliance
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const deleteAlliance = async allianceId => {
    resetState()
    loading.value = true

    try {
      const response = await requestDelete({ url: `alliances/${allianceId}` })

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

  const uploadAllianceLogo = async (allianceId, file) => {
    resetState()
    loading.value = true

    try {
      const formData = new FormData()

      formData.append('logo', file)

      const response = await requestPost({ url: `alliances/${allianceId}/logo`, data: formData, formData: true })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.alliance
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const deleteAllianceLogo = async allianceId => {
    resetState()
    loading.value = true

    try {
      const response = await requestDelete({ url: `alliances/${allianceId}/logo` })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.alliance
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
    createAlliance,
    updateAlliance,
    deleteAlliance,
    uploadAllianceLogo,
    deleteAllianceLogo,
  }
}
