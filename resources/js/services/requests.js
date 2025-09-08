import { useAuthStore } from '@/store/auth'
import { useToastStore } from '@/store/useToastStore'
import { apiURL } from "@/utils/constants"
import { useDebounceFn } from '@vueuse/core'
import { isRef, ref, unref, watch } from 'vue'

import useLogout from '@/hooks/Auth/useLogout'

const { logoutUser } = useLogout()

async function checkAuth(response) {
  if (response.status === 401) {
    logoutUser()
  }
}

export async function requestGet({ url, params = {}, token }) {
  const response = await fetch(`${apiURL}${url}?${new URLSearchParams(params)}`, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
  })

  const jsonResponse = await response.json()

  await checkAuth(response)
    
  return jsonResponse
}

export async function requestPost({ url, data = {}, params = {}, formData = false, token = false, responseType = 'json', auth = true }) {
  const headers = {}

  if (token) {
    headers['Authorization'] = `Bearer ${token}`
  }

  if (!formData) {
    headers['Content-Type'] = 'application/json'
  }

  const response = await fetch(`${apiURL}${url}?${new URLSearchParams(params)}`, {
    method: "POST",
    headers,
    body: formData ? data : JSON.stringify(data),
  })

  // ✅ revisa autenticación antes de devolver
  if (auth) await checkAuth(response)

  // 👇 decide si devolver JSON o Blob
  if (responseType === 'blob') {
    return { blob: await response.blob(), headers: response.headers }
  }

  return await response.json()
}


export async function requestDelete({ url, data = {}, params = {}, token }) {
  const response = await fetch(`${apiURL}${url}?${new URLSearchParams(params)}`, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(data),
  })

  const jsonResponse = await response.json()

  await checkAuth(response)
    
  return jsonResponse
}

export async function requestPut({ url, data = {}, params = {}, formData = false, token }) {
  const headers = {
    Authorization: `Bearer ${token}`,
  }

  if (!formData) {
    headers['Content-Type'] = 'application/json'
  }

  const response = await fetch(`${apiURL}${url}?${new URLSearchParams(params)}`, {
    method: "PUT",
    headers,
    body: formData ? data : JSON.stringify(data),
  })

  const jsonResponse = await response.json()

  await checkAuth(response)
    
  return jsonResponse
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
          sortBy: sortBy.value,
          status: status.value,
        },
        token: useAuthStore().getAccessToken(),
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
