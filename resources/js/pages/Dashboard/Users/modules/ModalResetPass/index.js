import { ref } from 'vue'
import ModalResetPass from './ModalResetPass.vue'

function useModalResetPass() {

  const showResetPassModal = ref(false)
  const selectedUserToResetPass = ref({})

  const openResetPassModal = data => {
    selectedUserToResetPass.value = data
    showResetPassModal.value = true
  }

  return {
    showResetPassModal,
    openResetPassModal,
    selectedUserToResetPass,
  }
}

export { ModalResetPass, useModalResetPass }
