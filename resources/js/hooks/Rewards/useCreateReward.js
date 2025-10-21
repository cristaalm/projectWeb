import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { parse, isValid, format } from 'date-fns'
import { ref } from 'vue'

export function useCreateReward() {
  const loading = ref(false)
  const toast = useToastStore()

  const authStore = useAuthStore()

  const originalData = {
    alliance_id: null,
    name: "",
    description: "",
    points_required: "",
    stock: "",
    is_active: true,
    expires_at: "DD/MM/YYYY",
  }

  const rewardData = ref({ ...originalData })

  const createReward = async ({ isUnlimitedStock, isIndefiniteExpiration }) => {
    loading.value = true
    try {
      // Convertir y validar expires_at
      let expires_at = null
      const rawExpiresAt = rewardData.value.expires_at
      if (typeof rawExpiresAt === 'string' && rawExpiresAt.trim() !== '') {
        const parsedDate = parse(rawExpiresAt, 'dd/MM/yyyy', new Date())
        if (isValid(parsedDate)) {
          expires_at = format(parsedDate, 'yyyy-MM-dd')
        }
      }
  
      const data = {
        alliance_id: rewardData.value.alliance_id,
        name: rewardData.value.name,
        description: rewardData.value.description,
        points_required: rewardData.value.points_required,
        stock: isUnlimitedStock ? null : rewardData.value.stock,
        is_active: rewardData.value.is_active,
        expires_at: isIndefiniteExpiration ? null : expires_at,
      }
  
      const response = await requestPost({
        url: 'reward/create',
        data,
        token: authStore.getAccessToken(),
      })
  
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        resetRewardData()

        return true
      }
  
      toast.showToast({ message: response.message, tipo: 'error', duration: 4000 })

      return false
    } catch (error) {
      console.error('Error al intentar crear la recompensa:', error)
      toast.showToast({ message: 'Error al intentar crear la recompensa.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  const resetRewardData = () => {
    rewardData.value = { ...originalData }
  }

  return {
    loading,
    createReward,
    rewardData,
    resetRewardData,
  }
}
