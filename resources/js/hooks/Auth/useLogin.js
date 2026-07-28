import { ensureCsrfCookie } from '@/services/http'
import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useTwoFactorChallengeStore } from '@/store/twoFactorChallenge'
import { useToastStore } from '@/store/useToastStore'
import { messageError } from '@/utils/constants'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

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
      await ensureCsrfCookie()

      const response = await requestPost({ url: 'auth/login', data: { email, password: pass, remember_me: remember } })

      if (!response.success) {
        error.value = true
        toast.showToast({ message: response.message ?? messageError, tipo: 'error', duration: 8000 })

        return
      }

      const data = response.data

      if (data.two_factor_required) {
        useTwoFactorChallengeStore().setChallenge(data.challenge_token, data.expires_at)
        router.push({ name: 'verify2FA' })

        return
      }

      user.value = data.user
      useAuthStore().setUser(data.user)

      success.value = true

      setTimeout(() => {
        router.push('/panel')
      }, 1000)

    } catch (err) {
      error.value = true
      console.error(err)
      toast.showToast({ message: messageError, tipo: 'error' })
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
