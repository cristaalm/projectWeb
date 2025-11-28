import { ref } from 'vue'
import ModalLogo from './ModalLogo.vue'

function useModalLogo() {
  const showLogoModal = ref(false)
  const selectedShopForLogo = ref(null)

  const openLogoModal = shop => {
    selectedShopForLogo.value = { ...shop } // Clonamos para no mutar original
    showLogoModal.value = true
  }

  return {
    showLogoModal,
    openLogoModal,
    selectedShopForLogo,
  }
}

export { ModalLogo, useModalLogo }
