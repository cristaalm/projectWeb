import { ref } from 'vue'
import ModalViewTypeShop from './ModalView.vue'

function useModalViewTypeShop() {

  const showModalViewTypeShop = ref(false)

  const openModalViewTypeShop = () => {
    showModalViewTypeShop.value = true
  }

  return {
    showModalViewTypeShop,
    openModalViewTypeShop,
  }
}

export { ModalViewTypeShop, useModalViewTypeShop }
