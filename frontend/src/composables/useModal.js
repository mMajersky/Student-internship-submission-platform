import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

export function useModal() {
  const { t } = useI18n()
  
  // Message modal state
  const showMessageModal = ref(false)
  const messageModalTitle = ref('')
  const messageModalMessage = ref('')
  const messageModalType = ref('info')
  const messageModalCallback = ref(null)

  // Confirmation modal state
  const showConfirmModal = ref(false)
  const confirmModalTitle = ref('')
  const confirmModalMessage = ref('')
  const confirmModalType = ref('warning')
  const confirmModalConfirmText = ref('')
  const confirmModalCancelText = ref('')
  const confirmCallback = ref(null)

  // Input modal state
  const showInputModal = ref(false)
  const inputModalTitle = ref('')
  const inputModalMessage = ref('')
  const inputModalLabel = ref('')
  const inputModalPlaceholder = ref('')
  const inputModalHint = ref('')
  const inputModalDefaultValue = ref('')
  const inputModalRequired = ref(true)
  const inputModalType = ref('info')
  const inputCallback = ref(null)

  // Alert function
  const alert = (message, title = null, type = 'info') => {
    return new Promise((resolve) => {
      messageModalTitle.value = title || t('common.message')
      messageModalMessage.value = message
      messageModalType.value = type
      showMessageModal.value = true
      
      messageModalCallback.value = () => {
        showMessageModal.value = false
        resolve()
      }
    })
  }

  const closeMessageModal = () => {
    if (messageModalCallback.value) {
      messageModalCallback.value()
      messageModalCallback.value = null
    } else {
      showMessageModal.value = false
    }
  }

  // Confirm function
  const confirm = (message, title = null, type = 'warning') => {
    return new Promise((resolve) => {
      confirmModalTitle.value = title || t('common.confirm')
      confirmModalMessage.value = message
      confirmModalType.value = type
      confirmModalConfirmText.value = t('common.confirm')
      confirmModalCancelText.value = t('common.cancel')
      showConfirmModal.value = true
      
      confirmCallback.value = (result) => {
        showConfirmModal.value = false
        resolve(result)
      }
    })
  }

  // Prompt function
  const prompt = (message, defaultValue = '', title = null, label = null, placeholder = null, hint = null, required = true) => {
    return new Promise((resolve) => {
      inputModalTitle.value = title || t('common.input')
      inputModalMessage.value = message
      inputModalLabel.value = label || t('common.value')
      inputModalPlaceholder.value = placeholder || ''
      inputModalHint.value = hint || ''
      inputModalDefaultValue.value = defaultValue
      inputModalRequired.value = required
      inputModalType.value = 'info'
      showInputModal.value = true
      
      inputCallback.value = (value) => {
        showInputModal.value = false
        resolve(value)
      }
    })
  }

  // Success message
  const showSuccess = (message, title = null) => {
    return alert(message, title || t('common.success'), 'success')
  }

  // Error message
  const showError = (message, title = null) => {
    return alert(message, title || t('common.error'), 'error')
  }

  // Warning message
  const showWarning = (message, title = null) => {
    return alert(message, title || t('common.warning'), 'warning')
  }

  // Info message
  const showInfo = (message, title = null) => {
    return alert(message, title || t('common.info'), 'info')
  }

  return {
    // State
    showMessageModal,
    messageModalTitle,
    messageModalMessage,
    messageModalType,
    showConfirmModal,
    confirmModalTitle,
    confirmModalMessage,
    confirmModalType,
    confirmModalConfirmText,
    confirmModalCancelText,
    confirmCallback,
    showInputModal,
    inputModalTitle,
    inputModalMessage,
    inputModalLabel,
    inputModalPlaceholder,
    inputModalHint,
    inputModalDefaultValue,
    inputModalRequired,
    inputModalType,
    inputCallback,
    // Functions
    alert,
    confirm,
    prompt,
    showSuccess,
    showError,
    showWarning,
    showInfo,
    closeMessageModal
  }
}
