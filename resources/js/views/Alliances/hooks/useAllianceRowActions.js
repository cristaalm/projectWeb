import { useAllianceManagement } from '@/hooks/Alliances/useAllianceManagement'
import { useDialogStore } from '@/store/useAlertDialogStorage'
import { ref } from 'vue'

// Orquesta las acciones por fila de la tabla (abrir el diálogo de edición,
// confirmar el borrado) — específico de esta vista, calco de
// useContainerRowActions.js.
export function useAllianceRowActions(onSuccess) {
  const { deleteAlliance } = useAllianceManagement()
  const dialogStore = useDialogStore()

  const formDialog = ref(false)
  const formMode = ref('create') // 'create' | 'edit'
  const activeAlliance = ref(null)

  function openCreateDialog() {
    activeAlliance.value = null
    formMode.value = 'create'
    formDialog.value = true
  }

  function openEditDialog(item) {
    activeAlliance.value = item
    formMode.value = 'edit'
    formDialog.value = true
  }

  async function handleDelete(item) {
    const confirmed = await dialogStore.showDialog({
      title: 'Eliminar alianza',
      text: `Se eliminará la alianza "${item.name}" permanentemente. ¿Continuar?`,
      type: 'confirm',
      confirmText: 'Eliminar',
    })

    if (!confirmed) return

    const ok = await deleteAlliance(item.id)
    if (ok) onSuccess()
  }

  return {
    formDialog,
    formMode,
    activeAlliance,
    openCreateDialog,
    openEditDialog,
    handleDelete,
  }
}
