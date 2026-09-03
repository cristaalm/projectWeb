import { requestGet, requestPost } from '@/services/requests'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useUserManagement() {
  const error = ref(false)
  const loading = ref(false)
  const toast = useToastStore()

  const resetState = () => {
    error.value = false
    loading.value = false
  }

  const createUser = async payload => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: 'users', data: payload })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.user
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const modifyPoints = async (userId, { points, reason }) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: `users/${userId}/points`, data: { points, reason } })

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

  const deactivateUser = async (userId, reason) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: `users/${userId}/deactivate`, data: { reason } })

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

  const restoreUser = async (userId, reason) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: `users/${userId}/restore`, data: { reason } })

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

  const resetCredentials = async userId => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: `users/${userId}/reset-credentials` })

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

  const disableTwoFactor = async userId => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: `users/${userId}/disable-two-factor` })

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

  const getUserDetail = async userId => {
    resetState()
    loading.value = true

    try {
      const response = await requestGet({ url: `users/${userId}` })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      return response.data.user
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const getUserHistory = async (userId, { page = 1, perPage = 15 } = {}) => {
    resetState()
    loading.value = true

    try {
      const response = await requestGet({ url: `users/${userId}/history`, params: { page, per_page: perPage } })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return { history: [], lastPage: 1, total: 0 }
      }

      return {
        history: response.data.history,
        lastPage: response.data.last_page ?? 1,
        total: response.data.total ?? response.data.history.length,
      }
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return { history: [], lastPage: 1, total: 0 }
  }

  return {
    error,
    loading,
    createUser,
    modifyPoints,
    deactivateUser,
    restoreUser,
    resetCredentials,
    disableTwoFactor,
    getUserDetail,
    getUserHistory,
  }
}
