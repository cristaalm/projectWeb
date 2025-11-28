import { ref } from 'vue'
import ModalDelete from './ModalDelete.vue'

function useModalDelete() {

  const showDeleteModal = ref(false)
  const selectedShopToDelete = ref({})

  const openDeleteModal = shop => {
    selectedShopToDelete.value = shop
    showDeleteModal.value = true
  }

  return {
    showDeleteModal,
    openDeleteModal,
    selectedShopToDelete,
  }
}

export { ModalDelete, useModalDelete }
