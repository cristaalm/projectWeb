import { ref } from 'vue'
import ModalToggleStatus from './ModalToggleStatus.vue'

function useModalToggleStatus() {

  const showToggleStatusModal = ref(false)
  const selectedUserToToggleStatus = ref({})

  const openToggleStatusModal = data => {
    selectedUserToToggleStatus.value = data
    showToggleStatusModal.value = true
  }

  return {
    showToggleStatusModal,
    openToggleStatusModal,
    selectedUserToToggleStatus,
  }
}

export { ModalToggleStatus, useModalToggleStatus }
