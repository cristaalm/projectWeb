import { ref } from 'vue'
import ModalDelete from './ModalDelete.vue'

function useModalDelete() {

  const showDeleteModal = ref(false)
  const selectedBadgeToDelete = ref({})

  const openDeleteModal = badge => {
    selectedBadgeToDelete.value = badge
    showDeleteModal.value = true
  }

  return {
    showDeleteModal,
    openDeleteModal,
    selectedBadgeToDelete,
  }
}

export { ModalDelete, useModalDelete }
