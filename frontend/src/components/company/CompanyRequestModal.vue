<template>
  <!-- Modal -->
  <div 
    class="modal fade" 
    :class="{ show: show }" 
    :style="{ display: show ? 'block' : 'none' }"
    tabindex="-1" 
    @click.self="handleClose"
  >
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-building-add me-2"></i>
            {{ t('companyRequestModal.title') }}
          </h5>
          <button 
            type="button" 
            class="btn-close" 
            @click="handleClose"
            :disabled="isSubmitting"
          ></button>
        </div>
          <div class="modal-body">
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            {{ t('companyRequestModal.info') }}
          </div>

          <CompanyRegistrationForm
            ref="formRef"
            api-endpoint="/api/student/company-requests"
            :auth-token="authToken"
            :show-cancel="true"
            :submit-button-text="t('companyRequestModal.submitButton')"
            @success="handleSuccess"
            @cancel="handleClose"
          />
        </div>
      </div>
    </div>
  </div>

  <!-- Backdrop -->
  <div 
    v-if="show" 
    class="modal-backdrop fade show"
    @click="handleClose"
  ></div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../../stores/auth'
import CompanyRegistrationForm from './CompanyRegistrationForm.vue'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  }
})

const emit = defineEmits(['close', 'success'])

const authStore = useAuthStore()
const formRef = ref(null)
const isSubmitting = ref(false)

const authToken = computed(() => authStore.token)
const { t } = useI18n()

// Handle modal close
const handleClose = () => {
  if (!isSubmitting.value) {
    emit('close')
    // Reset form after modal closes
    setTimeout(() => {
      if (formRef.value) {
        formRef.value.resetForm()
      }
    }, 300)
  }
}

// Handle successful submission
const handleSuccess = (data) => {
  emit('success', data)
  setTimeout(() => {
    handleClose()
  }, 2000)
}

// Prevent body scroll when modal is open
watch(() => props.show, (newVal) => {
  if (newVal) {
    document.body.classList.add('modal-open')
  } else {
    document.body.classList.remove('modal-open')
  }
})
</script>

<style scoped>
.modal.show {
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-dialog {
  margin-top: 3rem;
}
</style>
