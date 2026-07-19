import { router } from '@/plugins/router'
import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'

export default function useLogout() {
  const successLogout = ref(false)
  const errorLogout = ref(false)
  const loadingLogout = ref(false)

  const resetState = () => {
    successLogout.value = false
    errorLogout.value = null
    loadingLogout.value = false
  }

  const logoutUser = async () => {
    resetState()
    loadingLogout.value = true

    const toast = useToastStore()
    const authStore = useAuthStore()

    try {
      const response = await requestPost({ url: 'auth/logout' })

      if (!response.success) {
        errorLogout.value = true
        console.error(response.errors)
        toast.showToast({ message: response.message ?? messageError, tipo: 'error' })
      } else {
        successLogout.value = true
      }
    } catch (error) {
      errorLogout.value = true
      toast.showToast({ message: messageError, tipo: 'error' })
      console.error(error)
    } finally {
      authStore.logout()
      router.push({ name: 'login' })
      loadingLogout.value = false
    }
  }

  return {
    successLogout,
    errorLogout,
    loadingLogout,
    logoutUser,
  }
}
