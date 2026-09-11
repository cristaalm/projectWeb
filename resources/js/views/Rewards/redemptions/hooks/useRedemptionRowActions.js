import { useRedemptionManagement } from '@/hooks/Redemptions/useRedemptionManagement'
import { useDialogStore } from '@/store/useAlertDialogStorage'

// Orquesta la única acción disponible sobre un canje — confirmar entrega —
// específico de esta vista, mismo patrón que useRewardRowActions.
export function useRedemptionRowActions(onSuccess) {
  const { markDelivered } = useRedemptionManagement()
  const dialogStore = useDialogStore()

  async function handleDeliver(item) {
    const confirmed = await dialogStore.showDialog({
      title: 'Marcar como entregado',
      text: `Se confirmará la entrega en persona del canje de "${item.reward?.name}" a ${item.user?.name}. ¿Continuar?`,
      type: 'confirm',
      confirmText: 'Confirmar entrega',
    })

    if (!confirmed) return

    const redemption = await markDelivered(item.id)
    if (redemption) onSuccess()
  }

  return {
    handleDeliver,
  }
}
