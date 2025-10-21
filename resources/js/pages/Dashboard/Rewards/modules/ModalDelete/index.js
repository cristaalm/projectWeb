import { ref } from 'vue'
import ModalDelete from './ModalDelete.vue'

function useModalDelete() {

  const showDeleteModal = ref(false)
  const selectedRewardToDelete = ref({})

  const openDeleteModal = shop => {
    selectedRewardToDelete.value = shop
    showDeleteModal.value = true
  }

  return {
    showDeleteModal,
    openDeleteModal,
    selectedRewardToDelete,
  }
}

export { ModalDelete, useModalDelete }
