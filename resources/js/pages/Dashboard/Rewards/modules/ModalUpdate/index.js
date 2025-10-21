import { ref } from 'vue'
import ModalUpdate from './ModalUpdate.vue'

function useModalUpdate() {

  const showUpdateModal = ref(false)
  const selectedRewardToUpdate = ref({})

  const openUpdateModal = reward => {
    selectedRewardToUpdate.value = reward
    showUpdateModal.value = true
  }

  return {
    showUpdateModal,
    openUpdateModal,
    selectedRewardToUpdate,
  }
}

export { ModalUpdate, useModalUpdate }
