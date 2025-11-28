// eslint-disable-next-line import/extensions
import { requestPost } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { computed, ref } from 'vue'

export function useUpdateData(initialData) {
  const baseData = ref({ ...initialData })
  const loadingUpdateUser = ref(false)
  const showToast = useToastStore()
  const authStore = useAuthStore()

  const data = ref({
    name: initialData.name,
    last_name: initialData.last_name,
    phone: initialData.phone,
    curp: initialData.curp,
  })

  // eslint-disable-next-line
  const curpRegex = /^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/
  const nameRegex = /^[A-ZÁÉÍÓÚÑ ]+$/i
  const phoneRegex = /^\(\d{3}\) \d{3}-\d{4}$/

  const isChanged = computed(() => {
    return data.value.name !== baseData.value.name ||
    data.value.last_name !== baseData.value.last_name ||
    data.value.phone !== baseData.value.phone ||
    data.value.curp !== baseData.value.curp
  })

  const validateData = computed(() => {
    return {
      name: !!data.value.name && nameRegex.test(data.value.name),
      last_name: !!data.value.last_name && nameRegex.test(data.value.last_name),
      phone: !!data.value.phone && phoneRegex.test(data.value.phone),
      curp: !!data.value.curp && curpRegex.test(data.value.curp),
      success: !!data.value.name 
        && nameRegex.test(data.value.name) 
        && !!data.value.last_name 
        && nameRegex.test(data.value.last_name) 
        && !!data.value.phone 
        && phoneRegex.test(data.value.phone) 
        && !!data.value.curp 
        && curpRegex.test(data.value.curp)
        && isChanged.value,
    }
  })

  const resetBaseData = newData => {
    if (!newData) {
      baseData.value = { ...baseData.value }
    } else {
      baseData.value = { ...newData }
    }
  }

  const updateUser = async () => {
    loadingUpdateUser.value = true
    try {

      const phone = data.value.phone.replace(/\D/g, '')

      const dataSend = {
        "id": authStore.user.id,
        "name": data.value.name,
        "last_name": data.value.last_name,
        "phone": phone,
        "curp": data.value.curp,
      }

      const response = await requestPost({
        url: 'users/updateAccount',
        data: dataSend,
        token: authStore.accessToken,
      })

      if (!response.success) {
        showToast.showToast({
          message: response.message,
          tipo: 'error',
          duration: 4000,
        })

        return false
      }
      resetBaseData({ "name": data.value.name, "last_name": data.value.last_name, "phone": data.value.phone, "curp": data.value.curp })

      // cambiamos los datos del usuario en el store
      authStore.setUser({
        ...authStore.user,
        "name": data.value.name,
        "last_name": data.value.last_name,
        "phone": data.value.phone,
        "curp": data.value.curp,
      })
      showToast.showToast({
        message: response.message,
        tipo: 'success',
        duration: 4000,
      })
    } catch (error) {
      showToast.showToast({
        message: "Error al actualizar los datos del usuario",
        tipo: 'error',
        duration: 4000,
      })
    } finally {
      loadingUpdateUser.value = false
    }
  }

  return {
    data,
    validateData,
    isChanged,
    resetBaseData,
    updateUser,
    loadingUpdateUser,
  }
}
