import { requestDelete, requestPost, requestPut } from '@/services/requests'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useRewardManagement() {
  const error = ref(false)
  const loading = ref(false)
  const toast = useToastStore()

  const resetState = () => {
    error.value = false
    loading.value = false
  }

  const createReward = async payload => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: 'rewards', data: payload })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.reward
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const updateReward = async (rewardId, payload) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPut({ url: `rewards/${rewardId}`, data: payload })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.reward
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const deleteReward = async rewardId => {
    resetState()
    loading.value = true

    try {
      const response = await requestDelete({ url: `rewards/${rewardId}` })

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

  const uploadRewardImage = async (rewardId, file) => {
    resetState()
    loading.value = true

    try {
      const formData = new FormData()

      formData.append('image', file)

      const response = await requestPost({ url: `rewards/${rewardId}/image`, data: formData, formData: true })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.reward
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const deleteRewardImage = async rewardId => {
    resetState()
    loading.value = true

    try {
      const response = await requestDelete({ url: `rewards/${rewardId}/image` })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.reward
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const approveReward = async rewardId => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: `rewards/${rewardId}/approve` })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.reward
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const rejectReward = async (rewardId, reason) => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: `rewards/${rewardId}/reject`, data: { reason } })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.reward
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const pauseReward = async rewardId => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: `rewards/${rewardId}/pause` })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.reward
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }

    return null
  }

  const reactivateReward = async rewardId => {
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: `rewards/${rewardId}/reactivate` })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return null
      }

      toast.showToast({ message: response.message, tipo: 'success' })

      return response.data.reward
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
    createReward,
    updateReward,
    deleteReward,
    uploadRewardImage,
    deleteRewardImage,
    approveReward,
    rejectReward,
    pauseReward,
    reactivateReward,
  }
}
