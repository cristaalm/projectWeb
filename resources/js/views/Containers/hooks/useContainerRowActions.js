import { useContainerManagement } from '@/hooks/Containers/useContainerManagement'
import { useDialogStore } from '@/store/useAlertDialogStorage'
import { ref } from 'vue'

// Orquesta las acciones por fila de la tabla (abrir el diálogo de
// edición, confirmar el borrado) — específico de esta vista.
export function useContainerRowActions(onSuccess) {
  const { deleteContainer } = useContainerManagement()
  const dialogStore = useDialogStore()

  const formDialog = ref(false)
  const formMode = ref('create') // 'create' | 'edit'
  const activeContainer = ref(null)

  function openCreateDialog() {
    activeContainer.value = null
    formMode.value = 'create'
    formDialog.value = true
  }

  function openEditDialog(item) {
    activeContainer.value = item
    formMode.value = 'edit'
    formDialog.value = true
  }

  async function handleDelete(item) {
    const confirmed = await dialogStore.showDialog({
      title: 'Eliminar contenedor',
      text: `Se eliminará el contenedor "${item.name}" permanentemente. ¿Continuar?`,
      type: 'confirm',
      confirmText: 'Eliminar',
    })

    if (!confirmed) return

    const ok = await deleteContainer(item.id)
    if (ok) onSuccess()
  }

  return {
    formDialog,
    formMode,
    activeContainer,
    openCreateDialog,
    openEditDialog,
    handleDelete,
  }
}
