import { useUpdateProfile } from '@/hooks/Profile/useUpdateProfile'
import { useAuthStore } from '@/store/auth'
import { computed, reactive } from 'vue'

// Estado del formulario elevado a este composable (en vez de vivir solo
// dentro de PersonalInfoCard) para que ProfileHeaderCard pueda reflejar el
// nombre en tiempo real mientras el usuario escribe, antes de guardar.
export function useProfileEditor() {
  const authStore = useAuthStore()
  const { loading, updateProfile } = useUpdateProfile()

  const form = reactive({
    name: authStore.user?.name ?? '',
    last_name: authStore.user?.last_name ?? '',
  })

  const fullName = computed(() => `${form.name} ${form.last_name}`.trim())

  // Comparar contra authStore.user (reactivo) en vez de una copia fija tomada
  // al montar — así, tras guardar, el botón vuelve a bloquearse solo.
  const hasChanges = computed(() =>
    form.name !== (authStore.user?.name ?? '')
    || form.last_name !== (authStore.user?.last_name ?? ''),
  )

  const canSubmit = computed(() =>
    hasChanges.value && !!form.name.trim() && !!form.last_name.trim(),
  )

  function submit() {
    updateProfile({ name: form.name, last_name: form.last_name })
  }

  return {
    form,
    fullName,
    loading,
    canSubmit,
    submit,
  }
}
