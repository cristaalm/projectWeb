import { ref } from 'vue'
import ModalModifyPoints from './ModalModifyPoints.vue'

function useModalModifyPoints() {

  const showModifyPointsModal = ref(false)
  const selectedUserToModifyPoints = ref({})

  const openModifyPointsModal = user => {
    selectedUserToModifyPoints.value = user
    showModifyPointsModal.value = true
  }

  return {
    showModifyPointsModal,
    openModifyPointsModal,
    selectedUserToModifyPoints,
  }
}

export { ModalModifyPoints, useModalModifyPoints }
