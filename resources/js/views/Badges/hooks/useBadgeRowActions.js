import { useBadgeManagement } from '@/hooks/Badges/useBadgeManagement'
import { useDialogStore } from '@/store/useAlertDialogStorage'
import { ref } from 'vue'

export function useBadgeRowActions(onSuccess) {
  const { deleteBadge } = useBadgeManagement()
  const dialogStore = useDialogStore()

  const formDialog = ref(false)
  const formMode = ref('create') // 'create' | 'edit'
  const activeBadge = ref(null)

  function openCreateDialog() {
    activeBadge.value = null
    formMode.value = 'create'
    formDialog.value = true
  }

  function openEditDialog(item) {
    activeBadge.value = item
    formMode.value = 'edit'
    formDialog.value = true
  }

  async function handleDelete(item) {
    const confirmed = await dialogStore.showDialog({
      title: 'Eliminar insignia',
      text: `Se eliminará la insignia "${item.name}" permanentemente. ¿Continuar?`,
      type: 'confirm',
      confirmText: 'Eliminar',
    })

    if (!confirmed) return

    const ok = await deleteBadge(item.id)
    if (ok) onSuccess()
  }

  return { formDialog, formMode, activeBadge, openCreateDialog, openEditDialog, handleDelete }
}
