import { ref } from 'vue'
import ModalViewTerms from './ModalViewTerms.vue'

function useModalViewTerms() {

  const showViewTermsModal = ref(false)

  const openViewTermsModal = data => {
    showViewTermsModal.value = true
  }

  return {
    showViewTermsModal,
    openViewTermsModal,
  }
}

export { ModalViewTerms, useModalViewTerms }
