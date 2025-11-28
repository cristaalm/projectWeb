import { ref } from 'vue'
import { requestGet } from '@/services/requests'
import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'

export default function useGetListDocs() {
  const authStore = useAuthStore()
  const toastStore = useToastStore()
  const loadingListDocs = ref(false)
  
  const originalData = ref({
    front: false,
    back: false,
    selfie: false,
  })

  const listDocs = ref({ ...originalData.value })
  const objectUrls = ref([])

  const cleanupObjectUrls = () => {
    objectUrls.value.forEach(url => URL.revokeObjectURL(url))
    objectUrls.value = []
  }

  const resetListDocs = () => {
    cleanupObjectUrls()
    listDocs.value = { ...originalData.value }
  }

  const fetchImage = async (type, userId) => {
    const token = authStore.getAccessToken()

    const response = await fetch(`/api/users/documents/${type}/${userId}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    })

    if (!response.ok) {
      throw new Error(`Error al cargar ${type}`)
    }

    const blob = await response.blob()
    const url = URL.createObjectURL(blob)

    objectUrls.value.push(url) 

    return url
  }

  const getListDocs = async ({ id }) => {
    loadingListDocs.value = true
    try {
      cleanupObjectUrls()

      const response = await requestGet({ 
        url: 'users/list-docs', 
        token: authStore.getAccessToken(), 
        params: { user_id: id },
      })
      
      if (response.success) {
        const docs = { front: null, back: null, selfie: null }

        if (response.data?.front) {
          try {
            docs.front = await fetchImage('front', id)
          } catch (e) {
            console.error('Error al cargar frente:', e)
          }
        }

        if (response.data?.back) {
          try {
            docs.back = await fetchImage('back', id)
          } catch (e) {
            console.error('Error al cargar reverso:', e)
          }
        }

        if (response.data?.selfie) {
          try {
            docs.selfie = await fetchImage('selfie', id)
          } catch (e) {
            console.error('Error al cargar selfie:', e)
          }
        }

        listDocs.value = docs

        return docs
      }

      toastStore.showToast({ message: response.message, tipo: 'error', duration: 4000 })
      console.error(response.errors)

      return false

    } catch (e) {
      toastStore.showToast({ message: 'Error al obtener la lista de documentos', tipo: 'error', duration: 4000 })
      
      return false
    } finally {
      loadingListDocs.value = false
    }
  }

  return { 
    loadingListDocs, 
    getListDocs, 
    listDocs, 
    resetListDocs,
    cleanupObjectUrls,
  }
}
