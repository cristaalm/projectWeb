import { ref } from 'vue'
import ModalUpdate from './ModalUpdate.vue'

function useModalUpdate() {

  const showUpdateModal = ref(false)
  const selectedBadgeToUpdate = ref({})

  const openUpdateModal = shop => {
    selectedBadgeToUpdate.value = shop
    showUpdateModal.value = true
  }

  return {
    showUpdateModal,
    openUpdateModal,
    selectedBadgeToUpdate,
  }
}

export { ModalUpdate, useModalUpdate }
