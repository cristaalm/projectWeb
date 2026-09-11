import { useRewardManagement } from '@/hooks/Rewards/useRewardManagement'
import { useDialogStore } from '@/store/useAlertDialogStorage'
import { ref } from 'vue'

// Orquesta las acciones por fila (crear/editar, eliminar, aprobar/rechazar,
// pausar/reactivar) — específico de esta vista, calco de useAllianceRowActions.
export function useRewardRowActions(onSuccess) {
  const {
    loading: rejectLoading,
    deleteReward,
    approveReward,
    rejectReward,
    pauseReward,
    reactivateReward,
  } = useRewardManagement()

  const dialogStore = useDialogStore()

  const formDialog = ref(false)
  const formMode = ref('create') // 'create' | 'edit'
  const activeReward = ref(null)

  const rejectDialog = ref(false)
  const rejectingReward = ref(null)

  function openCreateDialog() {
    activeReward.value = null
    formMode.value = 'create'
    formDialog.value = true
  }

  function openEditDialog(item) {
    activeReward.value = item
    formMode.value = 'edit'
    formDialog.value = true
  }

  async function handleDelete(item) {
    const confirmed = await dialogStore.showDialog({
      title: 'Eliminar recompensa',
      text: `Se eliminará la recompensa "${item.name}" permanentemente. ¿Continuar?`,
      type: 'confirm',
      confirmText: 'Eliminar',
    })

    if (!confirmed) return

    const ok = await deleteReward(item.id)
    if (ok) onSuccess()
  }

  async function handleApprove(item) {
    const confirmed = await dialogStore.showDialog({
      title: 'Aprobar recompensa',
      text: `Se aprobará la recompensa "${item.name}" y quedará visible para canjearse. ¿Continuar?`,
      type: 'confirm',
      confirmText: 'Aprobar',
    })

    if (!confirmed) return

    const reward = await approveReward(item.id)
    if (reward) onSuccess()
  }

  function openRejectDialog(item) {
    rejectingReward.value = item
    rejectDialog.value = true
  }

  async function confirmReject(reason) {
    const reward = await rejectReward(rejectingReward.value.id, reason)
    if (!reward) return

    rejectDialog.value = false
    onSuccess()
  }

  async function handlePause(item) {
    const reward = await pauseReward(item.id)
    if (reward) onSuccess()
  }

  async function handleReactivate(item) {
    const reward = await reactivateReward(item.id)
    if (reward) onSuccess()
  }

  return {
    formDialog,
    formMode,
    activeReward,
    openCreateDialog,
    openEditDialog,
    handleDelete,
    handleApprove,
    rejectDialog,
    rejectLoading,
    openRejectDialog,
    confirmReject,
    handlePause,
    handleReactivate,
  }
}
