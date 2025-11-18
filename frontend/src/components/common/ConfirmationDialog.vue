<template>
  <div v-if="isVisible" class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header" :class="headerClass">
          <h5 class="modal-title">
            <i :class="iconClass" class="me-2"></i>
            {{ title }}
          </h5>
          <button type="button" class="btn-close" @click="handleCancel" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>{{ message }}</p>
          
          <!-- Text confirmation input for critical actions -->
          <div v-if="requiresTextConfirmation" class="mt-3">
            <label for="confirmationText" class="form-label">
              {{ textConfirmationLabel }}
            </label>
            <input
              type="text"
              class="form-control"
              id="confirmationText"
              v-model="confirmationText"
              :placeholder="textConfirmationPlaceholder"
              @keyup.enter="handleConfirm"
            />
            <small class="text-muted">{{ textConfirmationHint }}</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="handleCancel">
            {{ cancelText }}
          </button>
          <button
            type="button"
            class="btn"
            :class="confirmButtonClass"
            @click="handleConfirm"
            :disabled="!canConfirm"
          >
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  confirmText: {
    type: String,
    default: 'Potvrdiť'
  },
  cancelText: {
    type: String,
    default: 'Zrušiť'
  },
  type: {
    type: String,
    default: 'warning', // 'warning', 'danger', 'info'
    validator: (value) => ['warning', 'danger', 'info'].includes(value)
  },
  requiresTextConfirmation: {
    type: Boolean,
    default: false
  },
  confirmationTextRequired: {
    type: String,
    default: 'delete'
  },
  textConfirmationLabel: {
    type: String,
    default: 'Pre potvrdenie napíšte:'
  },
  textConfirmationPlaceholder: {
    type: String,
    default: ''
  },
  textConfirmationHint: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const confirmationText = ref('')

const headerClass = computed(() => {
  const classes = {
    warning: 'bg-warning text-dark',
    danger: 'bg-danger text-white',
    info: 'bg-info text-white'
  }
  return classes[props.type] || classes.warning
})

const confirmButtonClass = computed(() => {
  const classes = {
    warning: 'btn-warning',
    danger: 'btn-danger',
    info: 'btn-info'
  }
  return classes[props.type] || classes.warning
})

const iconClass = computed(() => {
  const icons = {
    warning: 'bi bi-exclamation-triangle',
    danger: 'bi bi-exclamation-triangle-fill',
    info: 'bi bi-info-circle'
  }
  return icons[props.type] || icons.warning
})

const canConfirm = computed(() => {
  if (!props.requiresTextConfirmation) {
    return true
  }
  return confirmationText.value.toLowerCase().trim() === props.confirmationTextRequired.toLowerCase()
})

const handleConfirm = () => {
  if (canConfirm.value) {
    emit('confirm')
    confirmationText.value = ''
  }
}

const handleCancel = () => {
  emit('cancel')
  confirmationText.value = ''
}
</script>

<style scoped>
.modal {
  z-index: 1055;
}
</style>

