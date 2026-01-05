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
          <p v-if="message">{{ message }}</p>
          <div class="mt-3">
            <label for="inputValue" class="form-label">{{ inputLabel }}</label>
            <input
              type="text"
              class="form-control"
              id="inputValue"
              v-model="inputValue"
              :placeholder="inputPlaceholder"
              @keyup.enter="handleConfirm"
              ref="inputRef"
            />
            <small v-if="inputHint" class="text-muted">{{ inputHint }}</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="handleCancel">
            {{ $t('common.cancel') }}
          </button>
          <button
            type="button"
            class="btn btn-primary"
            @click="handleConfirm"
            :disabled="!inputValue || (required && !inputValue.trim())"
          >
            {{ $t('common.confirm') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    default: ''
  },
  inputLabel: {
    type: String,
    default: ''
  },
  inputPlaceholder: {
    type: String,
    default: ''
  },
  inputHint: {
    type: String,
    default: ''
  },
  defaultValue: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: true
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['info', 'warning', 'error'].includes(value)
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const inputValue = ref('')
const inputRef = ref(null)

const headerClass = computed(() => {
  const classes = {
    info: 'bg-info text-white',
    warning: 'bg-warning text-dark',
    error: 'bg-danger text-white'
  }
  return classes[props.type] || classes.info
})

const iconClass = computed(() => {
  const icons = {
    info: 'bi bi-info-circle',
    warning: 'bi bi-exclamation-triangle',
    error: 'bi bi-x-circle'
  }
  return icons[props.type] || icons.info
})

watch(() => props.isVisible, (newVal) => {
  if (newVal) {
    inputValue.value = props.defaultValue || ''
    nextTick(() => {
      if (inputRef.value) {
        inputRef.value.focus()
      }
    })
  }
})

const handleConfirm = () => {
  if (props.required && !inputValue.value.trim()) {
    return
  }
  emit('confirm', inputValue.value)
  inputValue.value = ''
}

const handleCancel = () => {
  emit('cancel')
  inputValue.value = ''
}
</script>

<style scoped>
.modal {
  z-index: 1055;
}
</style>

