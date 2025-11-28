import { ref } from 'vue'
import ModalCreate from './ModalCreate.vue'

function useModalCreate() {

  const showCreateModal = ref(false)

  const openCreateModal = () => {
    showCreateModal.value = true
  }

  return {
    showCreateModal,
    openCreateModal,
  }
}

export { ModalCreate, useModalCreate }
