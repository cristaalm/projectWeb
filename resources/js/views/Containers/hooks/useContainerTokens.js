import { useContainerManagement } from '@/hooks/Containers/useContainerManagement'
import { useDialogStore } from '@/store/useAlertDialogStorage'
import { useToastStore } from '@/store/useToastStore'
import { reactive } from 'vue'

// Estado de los tokens de API en la tabla de contenedores. El token es un
// dato sensible: no viene en el listado, se pide al servidor solo cuando el
// usuario lo revela o lo copia, y se olvida al volver a ocultarlo.
export function useContainerTokens() {
  const { getContainerToken, regenerateContainerToken } = useContainerManagement()
  const dialogStore = useDialogStore()
  const toast = useToastStore()

  const revealedTokens = reactive({}) // { [containerId]: token }
  const loadingTokens = reactive({}) // { [containerId]: true }

  async function fetchToken(containerId) {
    loadingTokens[containerId] = true

    try {
      return await getContainerToken(containerId)
    } finally {
      delete loadingTokens[containerId]
    }
  }

  async function toggleToken(item) {
    if (revealedTokens[item.id]) {
      delete revealedTokens[item.id]

      return
    }

    const token = await fetchToken(item.id)
    if (token) revealedTokens[item.id] = token
  }

  async function copyToken(item) {
    const token = revealedTokens[item.id] ?? await fetchToken(item.id)
    if (!token) return

    try {
      await navigator.clipboard.writeText(token)
      toast.showToast({ message: `Token de "${item.name}" copiado al portapapeles.`, tipo: 'success' })
    } catch (err) {
      console.error(err)
      toast.showToast({ message: 'No se pudo copiar. Muestra el token y cópialo manualmente.', tipo: 'error', duration: 8000 })
    }
  }

  async function regenerateToken(item) {
    const confirmed = await dialogStore.showDialog({
      title: 'Regenerar token',
      text: `El token actual de "${item.name}" dejará de funcionar de inmediato y el contenedor no podrá registrar escaneos hasta que se le cargue el nuevo. ¿Continuar?`,
      type: 'confirm',
      confirmText: 'Regenerar',
    })

    if (!confirmed) return

    const token = await regenerateContainerToken(item.id)

    // Se deja a la vista: lo siguiente que hace falta es copiarlo al equipo.
    if (token) revealedTokens[item.id] = token
  }

  return {
    revealedTokens,
    loadingTokens,
    toggleToken,
    copyToken,
    regenerateToken,
  }
}
