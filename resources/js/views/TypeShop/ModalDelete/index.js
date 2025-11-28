import { ref } from 'vue'
import ModalDelete from './ModalDelete.vue'

function useModalDelete() {

  const showDeleteModal = ref(false)
  const selectedTypeShopToDelete = ref({})

  const openDeleteModal = typeShop => {
    selectedTypeShopToDelete.value = typeShop
    showDeleteModal.value = true
  }

  return {
    showDeleteModal,
    openDeleteModal,
    selectedTypeShopToDelete,
  }
}

export { ModalDelete, useModalDelete }
