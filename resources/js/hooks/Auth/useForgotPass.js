import { requestPost } from '@/services/requests'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export default function useForgotPass() {
  const success = ref(false)
  const error = ref(false)
  const loading = ref(false)
  const toast = useToastStore()
    
  const resetState = () => {
    success.value = false
    error.value = null
    loading.value = false
  }

  const validate = ({ email }) => {
    if (!email) {
      error.value = true
      toast.showToast({ message: 'Por favor, complete los campos', tipo: 'warning' })
      
      return false
    }
    
    return true
  }

  const sendEmail = async form => {
    const { email } = form
    if (!validate({ email })) return
    resetState()
    loading.value = true
    
    try {
      const response = await requestPost({ url: 'auth/forgot-password', data: { email } })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error' })
        
        return
      }
      success.value = true

    } catch (error) {
      error.value = true
      console.log(error)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }
  }

  return {
    success,
    error,
    loading,
    sendEmail,
  }
}
