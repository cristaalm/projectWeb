import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { ref } from 'vue'

export default function useAuthToken() {
  const user = ref(null)
  const success = ref(false)
  const error = ref(false)
  const loading = ref(false)
    
  const resetState = () => {
    success.value = false
    error.value = null
    loading.value = false
  }

  const authToken = async () => {
    const token = useAuthStore().getAccessToken()
    if (!token) return false
    resetState()
    loading.value = true
    
    try {
      const response = await requestPost({ url: 'auth/validateToken', token, auth: false })

      if (!response.success) {
        error.value = true
        
        return false
      }
      success.value = true

      const data = response.data

      user.value = data.user
      useAuthStore().setUser(data.user)
      useAuthStore().setExpiresAt(data.expires_at)
      
      return true

    } catch (error) {
      error.value = true
      console.log(error)
    } finally {
      loading.value = false
    }

    return false
  }

  return {
    user,
    success,
    error,
    loading,
    authToken,
  }
}
