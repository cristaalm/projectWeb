import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

/**
 * Custom hook for handling user login functionality.
 * 
 * @returns {Object} An object containing the following properties and methods:
 * - `user` {Ref<null|Object>} - A reactive reference to the logged-in user data.
 * - `success` {Ref<boolean>} - A reactive reference indicating if the login was successful.
 * - `error` {Ref<boolean>} - A reactive reference indicating if there was an error during login.
 * - `loading` {Ref<boolean>} - A reactive reference indicating if the login process is in progress.
 * - `loginUser` {Function} - A function to handle the login process.
 */
export default function useLogin() {
  const router = useRouter()
  const user = ref(null)
  const success = ref(false)
  const error = ref(false)
  const loading = ref(false)
  const toast = useToastStore()
    
  const resetState = () => {
    success.value = false
    error.value = null
    loading.value = false
  }

  const validate = ({ email, pass }) => {
    if (!email || !pass) {
      error.value = true
      toast.showToast({ message: 'Por favor, complete los campos', tipo: 'warning' })
      
      return false
    }
    
    return true
  }

  const loginUser = async form => {
    const { email, pass, remember } = form
    if (!validate({ email, pass })) return
    resetState()
    loading.value = true
    
    try {
      const response = await requestPost({ url: 'auth/login', data: { email, password: pass, remember_me: remember } })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })
        
        return
      }
      success.value = true

      const data = response.data

      user.value = data.user
      useAuthStore().setUser(data.user)
      useAuthStore().setExpiresAt(data.expires_at)
      useAuthStore().setAccessToken(data.access_token)
      setTimeout(() => {
        router.push('/panel')
      }, 1000)

    } catch (error) {
      error.value = true
      console.log(error)
    } finally {
      loading.value = false
    }
  }

  return {
    user,
    success,
    error,
    loading,
    loginUser,
  }
}
