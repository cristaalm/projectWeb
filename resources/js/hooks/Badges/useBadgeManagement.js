import { requestDelete, requestPost, requestPut } from '@/services/requests'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export function useBadgeManagement() {
  const error = ref(false)
  const loading = ref(false)
  const toast = useToastStore()

  const resetState = () => { error.value = false; loading.value = false }

  const createBadge = async payload => {
    resetState()
    loading.value = true
    try {
      const response = await requestPost({ url: 'badges', data: payload })
      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })
        
        return null
      }
      toast.showToast({ message: response.message, tipo: 'success' })
      
      return response.data.badge
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }
    
    return null
  }

  const updateBadge = async (badgeId, payload) => {
    resetState()
    loading.value = true
    try {
      const response = await requestPut({ url: `badges/${badgeId}`, data: payload })
      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })
        
        return null
      }
      toast.showToast({ message: response.message, tipo: 'success' })
      
      return response.data.badge
    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }
    
    return null
  }

  const deleteBadge = async badgeId => {
    resetState()
    loading.value = true
    try {
      const response = await requestDelete({ url: `badges/${badgeId}` })
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

  return { error, loading, createBadge, updateBadge, deleteBadge }
}
