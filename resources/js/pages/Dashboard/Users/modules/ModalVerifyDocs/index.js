import { ref } from 'vue'
import ModalVerifyDocs from './ModalVerifyDocs.vue'

function useModalVerifyDocs() {

  const showVerifyDocsModal = ref(false)
  const selectedUserToVerifyDocs = ref({})

  const openVerifyDocsModal = data => {
    selectedUserToVerifyDocs.value = data
    showVerifyDocsModal.value = true
  }

  return {
    showVerifyDocsModal,
    openVerifyDocsModal,
    selectedUserToVerifyDocs,
  }
}

export { ModalVerifyDocs, useModalVerifyDocs }
