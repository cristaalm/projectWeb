import { ref } from 'vue'
import ModalUpdate from './ModalUpdate.vue'

function useModalUpdate() {

  const showUpdateModal = ref(false)
  const selectedShopToUpdate = ref({})

  const openUpdateModal = shop => {
    selectedShopToUpdate.value = shop
    showUpdateModal.value = true
  }

  return {
    showUpdateModal,
    openUpdateModal,
    selectedShopToUpdate,
  }
}

export { ModalUpdate, useModalUpdate }
