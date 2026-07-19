import { router } from '@/plugins/router'
import { useToastStore } from '@/store/useToastStore'
import http from '@/services/http'
import { useDebounceFn } from '@vueuse/core'
import { isRef, ref, unref, watch } from 'vue'

// La sesión murió (cookie inválida/expirada o token revocado): tanto
// AuthenticationException como EnsureUserIsActive responden con este shape.
// Un 401 "credenciales incorrectas" (AuthException del login) no lo trae,
// así que no dispara un logout de por medio.
async function checkAuth(response) {
  if (response && 'authenticated' in response && response.authenticated === false) {
    router.push({ name: 'logout' })
  }
}

export async function requestGet({ url, params = {} }) {
  const response = await http.get(url, { params })

  await checkAuth(response)

  return response
}

export async function requestPost({ url, data = {}, params = {}, formData = false, responseType = 'json' }) {
  const headers = formData ? { 'Content-Type': 'multipart/form-data' } : {}

  const response = await http.post(url, data, {
    params,
    headers,
    responseType: responseType === 'blob' ? 'blob' : 'json',
  })

  if (responseType === 'blob') return { blob: response }

  await checkAuth(response)

  return response
}

export async function requestDelete({ url, data = {}, params = {} }) {
  const response = await http.delete(url, { params, data })

  await checkAuth(response)

  return response
}

export async function requestPut({ url, data = {}, params = {}, formData = false }) {
  const headers = formData ? { 'Content-Type': 'multipart/form-data' } : {}

  const response = await http.put(url, data, { params, headers })

  await checkAuth(response)

  return response
}

export function requestOrderTable({ url, params = {}, defaults = { page: 1, perPage: 5, search: '', sortBy: [{ key: null, order: null }], status: null }, config = { autoload: true, isGhostLoading: false } }) {
  const data = ref([])
  const total = ref(0)
  const loading = ref(false)
  const ghostLoading = ref(false)
  const firstLoad = ref(true)

  const page = ref(defaults.page || 1)
  const perPage = ref(defaults.perPage || 5)
  const search = ref(defaults.search || '')
  const sortBy = ref(defaults.sortBy || [{ key: null, order: null }])
  const status = ref(defaults.status || null)

  const isGhostLoading = ref(config.isGhostLoading || false)

  const showToast = useToastStore()

  const loadData = async () => {
    if (isGhostLoading.value && firstLoad.value) {
      firstLoad.value = false
      loading.value = true
    }else if (isGhostLoading.value && !firstLoad.value) {
      loading.value = false
    } else {
      loading.value = true
    }
    ghostLoading.value = true
    try {
      const response = await requestGet({
        url: url,
        params: {
          ...normalizeParams(params),
          page: page.value,
          query: search.value,
          "per_page": perPage.value,
          key: sortBy.value[0]?.key || null,
          order: sortBy.value[0]?.order || null,
          status: status.value,
        },
      })

      if (response.success) {
        data.value = response.data?.data || []
        total.value = response.data?.total || 0
        if (page.value > response.data.last_page) page.value = response.data.last_page
      } else {
        await checkAuth(response)
        data.value = []
        total.value = 0
        showToast.showToast({ message: response.message || (response.errors ?? 'Error al cargar los datos'), tipo: 'error', duration: 4000 })
      }
    } catch (error) {
      console.error('Error al cargar los datos:', error)
      showToast.showToast({ message: 'Error al cargar los datos', tipo: 'error', duration: 4000 })
    } finally {
      loading.value = false
      ghostLoading.value = false
    }
  }

  const debouncedLoad = useDebounceFn(loadData, 500)

  watch([page, perPage, sortBy, status], loadData, { immediate: config.autoload === true })
  watch(search, debouncedLoad)

  for (const key in params) {
    if (isRef(params[key])) {
      watch(params[key], loadData)
    }
  }

  return {
    data,
    total,
    loading,
    ghostLoading,
    page,
    perPage,
    sortBy,
    search,
    status,
    loadData,
  }

}

function normalizeParams(params) {
  const result = {}
  for (const key in params) {
    result[key] = unref(params[key])
  }

  return result
}
