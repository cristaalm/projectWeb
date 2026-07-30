import { useUserManagement } from '@/hooks/Users/useUserManagement'
import { useDialogStore } from '@/store/useAlertDialogStorage'
import { ref } from 'vue'

// Orquesta las acciones por fila de la tabla (abrir diálogos, confirmar
// acciones sin diálogo propio) — específico de esta vista, por eso vive
// junto a ella y no en el hooks/Users/ global (que sí es reusable: llama
// a la API sin saber nada de diálogos ni de la tabla).
export function useUserRowActions(onSuccess) {
  const {
    loading,
    deactivateUser,
    restoreUser,
    resetCredentials,
    disableTwoFactor,
  } = useUserManagement()

  const dialogStore = useDialogStore()

  const activeUser = ref(null)

  // ---------- Ver detalle / historial ----------
  const detailDialog = ref(false)
  const detailUserId = ref(null)

  function openDetailDialog(item) {
    detailUserId.value = item.id
    detailDialog.value = true
  }

  // ---------- Modificar puntos ----------
  const pointsDialog = ref(false)

  function openPointsDialog(item) {
    activeUser.value = item
    pointsDialog.value = true
  }

  // ---------- Dar de baja / restaurar ----------
  const reasonDialog = ref(false)
  const reasonMode = ref('deactivate') // 'deactivate' | 'restore'

  function openDeactivateDialog(item) {
    activeUser.value = item
    reasonMode.value = 'deactivate'
    reasonDialog.value = true
  }

  function openRestoreDialog(item) {
    activeUser.value = item
    reasonMode.value = 'restore'
    reasonDialog.value = true
  }

  async function confirmReasonAction(reason) {
    const ok = reasonMode.value === 'deactivate'
      ? await deactivateUser(activeUser.value.id, reason)
      : await restoreUser(activeUser.value.id, reason)

    if (!ok) return

    reasonDialog.value = false
    onSuccess()
  }

  // ---------- Resetear credenciales / deshabilitar 2FA (confirmación simple) ----------
  async function handleResetCredentials(item) {
    const confirmed = await dialogStore.showDialog({
      title: 'Resetear credenciales',
      text: `Se generará una contraseña nueva para ${item.name} ${item.last_name} y se le enviará por correo. ¿Continuar?`,
      type: 'confirm',
      confirmText: 'Resetear',
    })

    if (!confirmed) return

    const ok = await resetCredentials(item.id)
    if (ok) onSuccess()
  }

  async function handleDisableTwoFactor(item) {
    const confirmed = await dialogStore.showDialog({
      title: 'Deshabilitar 2FA',
      text: `Se deshabilitará la verificación en dos pasos de ${item.name} ${item.last_name}. ¿Continuar?`,
      type: 'confirm',
      confirmText: 'Deshabilitar',
    })

    if (!confirmed) return

    const ok = await disableTwoFactor(item.id)
    if (ok) onSuccess()
  }

  return {
    loading,
    activeUser,
    detailDialog,
    detailUserId,
    openDetailDialog,
    pointsDialog,
    openPointsDialog,
    reasonDialog,
    reasonMode,
    openDeactivateDialog,
    openRestoreDialog,
    confirmReasonAction,
    handleResetCredentials,
    handleDisableTwoFactor,
  }
}
