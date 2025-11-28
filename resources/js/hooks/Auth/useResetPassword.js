import { requestPost } from '@/services/requests'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export default function useResetPassword() {
  const success = ref(false)
  const loading = ref(false)
  const toast = useToastStore()

  const resetState = () => {
    success.value = false
    loading.value = false
  }

  const validate = ({ newPassword, confirmPassword }) => {
    if (!newPassword || !confirmPassword) {
      toast.showToast({ message: 'Por favor, complete los campos', tipo: 'warning' })
        
      return false
    }

    if (newPassword.length < 8) {
      toast.showToast({ message: 'La contraseña debe tener al menos 8 caracteres', tipo: 'warning' })
        
      return false
    }

    if (newPassword !== confirmPassword) {
      toast.showToast({ message: 'Las contraseñas no coinciden', tipo: 'warning' })
        
      return false
    }

    return true
  }

  const resetPassword = async form => {
    const { newPassword, confirmPassword, token, email } = form
    if (!validate({ newPassword, confirmPassword })) return
    resetState()
    loading.value = true

    try {
      const response = await requestPost({ url: 'auth/reset-password', data: { email, password: newPassword, "password_confirmation": confirmPassword, token } })

      if (!response.success) {
        toast.showToast({ message: response.message ?? messageError, tipo: 'error' })
        
        return
      }
      success.value = true
      toast.showToast({ message: 'Contraseña restablecida con éxito', tipo: 'success' })
    } catch (error) {
      console.error(error)
      toast.showToast({ message: messageError, tipo: 'error' })
    } finally {
      loading.value = false
    }
  }

  return {
    success,
    loading,
    resetPassword,
  }
}
