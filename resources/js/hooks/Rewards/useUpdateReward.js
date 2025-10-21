import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { deepEqual } from '@/utils/utils'
import { parse, isValid, format } from 'date-fns'
import { formatDateToDDMMYYYY } from '@/utils/time'
import { computed, ref } from 'vue'

export function useUpdateReward() {
  const loading = ref(false)
  const toast = useToastStore()
  const originalData = ref({})

  const rewardData = ref({
    alliance_id: null,
    name: "",
    description: "",
    points_required: "",
    stock: "",
    is_active: true,
    expires_at: "DD/MM/YYYY",
  })

  const isUnchanged = computed(() => deepEqual(rewardData.value, originalData.value))

  const setNewData = newData => {
    let expires_at = newData.expires_at
    if (newData.expires_at == null) {
      expires_at = "DD/MM/YYYY"
    } else {
      expires_at = formatDateToDDMMYYYY(newData.expires_at)
    }

    rewardData.value = { ...newData,
      is_active: newData.is_active ? true : false,
      expires_at: expires_at,
    }
    originalData.value = { ...newData,
      is_active: newData.is_active ? true : false,
      expires_at: expires_at,
    }
  }

  const updateReward = async ({ isUnlimitedStock, isIndefiniteExpiration }) => {
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

      const response = await requestPost({ url: 'reward/update/' + rewardData.value.id, data, token: useAuthStore().getAccessToken() })
      if (response.success) {
        toast.showToast({ message: response.message, tipo: 'success', duration: 4000 })
        
        return true
      }
      toast.showToast({ message: response.message || (response.errors ?? 'Error al intentar actualizar los datos de la recompensa.'), tipo: 'error', duration: 4000 })
      
      return false
    } catch (error) {
      console.error('Error al intentar actualizar los datos de la recompensa:', error)
      toast.showToast({ message: 'Error al intentar actualizar los datos de la recompensa.', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    updateReward,
    setNewData,
    isUnchanged,
    rewardData,
  }
}
