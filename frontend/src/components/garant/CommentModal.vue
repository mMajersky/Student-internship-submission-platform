<template>
  <div v-if="isVisible" class="modal-overlay" @click.self="handleClose">
    <div class="modal-container">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-chat-left-text me-2"></i>
          Pridať komentár k praxi
        </h5>
        <button type="button" class="btn-close" @click="handleClose" aria-label="Zavrieť">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <div class="modal-body">
        <div v-if="internship" class="internship-info mb-4">
          <p class="mb-1">
            <strong>Študent:</strong> 
            {{ internship.student ? `${internship.student.name} ${internship.student.surname}` : '-' }}
          </p>
          <p class="mb-1">
            <strong>Firma:</strong> 
            {{ internship.company ? internship.company.name : '-' }}
          </p>
          <p class="mb-0">
            <strong>Aktuálny stav:</strong>
            <span class="badge ms-2" :class="getStatusClass(internship.status)">
              {{ internship.status }}
            </span>
          </p>
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="mb-3">
            <label for="commentType" class="form-label">
              <i class="bi bi-tag me-1"></i>
              Typ komentára <span class="text-danger">*</span>
            </label>
            <select 
              id="commentType" 
              v-model="formData.comment_type" 
              class="form-select"
              required
            >
              <option value="" disabled>Vyberte typ komentára</option>
              <option value="approval">Schválenie</option>
              <option value="rejection">Zamietnutie</option>
              <option value="correction">Požadovaná oprava</option>
              <option value="general">Všeobecný komentár</option>
            </select>
            <div class="form-text">
              {{ getCommentTypeDescription(formData.comment_type) }}
            </div>
          </div>

          <div class="mb-3">
            <label for="commentContent" class="form-label">
              <i class="bi bi-pencil me-1"></i>
              Komentár <span class="text-danger">*</span>
            </label>
            <textarea
              id="commentContent"
              v-model="formData.content"
              class="form-control"
              rows="6"
              placeholder="Napíšte váš komentár..."
              required
              maxlength="2000"
            ></textarea>
            <div class="form-text text-end">
              {{ formData.content.length }} / 2000 znakov
            </div>
          </div>

          <div v-if="error" class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ error }}
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="handleClose">
              <i class="bi bi-x-circle me-2"></i>
              Zrušiť
            </button>
            <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
              <span v-if="isSubmitting">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Ukladám...
              </span>
              <span v-else>
                <i class="bi bi-check-circle me-2"></i>
                Uložiť komentár
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'

const props = defineProps({
  isVisible: {
    type: Boolean,
    required: true
  },
  internship: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'submit'])

const formData = reactive({
  comment_type: '',
  content: ''
})

const isSubmitting = ref(false)
const error = ref('')

// Reset form when modal opens with new internship
watch(() => props.internship, (newInternship) => {
  if (newInternship) {
    formData.comment_type = ''
    formData.content = ''
    error.value = ''
  }
})

// Reset form when modal closes
watch(() => props.isVisible, (isVisible) => {
  if (!isVisible) {
    formData.comment_type = ''
    formData.content = ''
    error.value = ''
  }
})

const handleClose = () => {
  if (!isSubmitting.value) {
    emit('close')
  }
}

const handleSubmit = async () => {
  if (!formData.comment_type || !formData.content.trim()) {
    error.value = 'Prosím vyplňte všetky povinné polia.'
    return
  }

  if (!props.internship || !props.internship.id) {
    error.value = 'Chyba: Neplatná prax.'
    return
  }

  isSubmitting.value = true
  error.value = ''

  try {
    await emit('submit', {
      internship_id: props.internship.id,
      comment_type: formData.comment_type,
      content: formData.content.trim()
    })
    
    // Reset form after successful submission
    formData.comment_type = ''
    formData.content = ''
  } catch (err) {
    error.value = err.message || 'Chyba pri ukladaní komentára.'
  } finally {
    isSubmitting.value = false
  }
}

const getCommentTypeDescription = (type) => {
  const descriptions = {
    'approval': 'Komentár k schváleniu praxe študenta.',
    'rejection': 'Zdôvodnenie zamietnutia žiadosti o prax.',
    'correction': 'Požadované opravy alebo úpravy dokumentácie.',
    'general': 'Všeobecná poznámka alebo informácia.'
  }
  return descriptions[type] || 'Vyberte typ komentára pre zobrazenie popisu.'
}

const getStatusClass = (status) => {
  const statusClasses = {
    'pending': 'bg-secondary',
    'confirmed': 'bg-info',
    'approved': 'bg-success',
    'in_progress': 'bg-warning text-dark',
    'completed': 'bg-success',
    'rejected': 'bg-danger',
    'cancelled': 'bg-danger'
  }
  return statusClasses[status] || 'bg-secondary'
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
  padding: 1rem;
  overflow-y: auto;
}

.modal-container {
  background: white;
  border-radius: 0.5rem;
  width: 100%;
  max-width: 600px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  margin: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #dee2e6;
}

.modal-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #212529;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #6c757d;
  cursor: pointer;
  padding: 0.25rem;
  line-height: 1;
  transition: color 0.15s ease-in-out;
}

.btn-close:hover {
  color: #000;
}

.modal-body {
  padding: 1.5rem;
}

.internship-info {
  background-color: #f8f9fa;
  border-left: 4px solid #0d6efd;
  padding: 1rem;
  border-radius: 0.25rem;
}

.internship-info p {
  font-size: 0.9rem;
  color: #495057;
}

.form-label {
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.5rem;
}

.form-select,
.form-control {
  border: 1px solid #ced4da;
  border-radius: 0.375rem;
  padding: 0.625rem 0.875rem;
  font-size: 0.9375rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select:focus,
.form-control:focus {
  border-color: #86b7fe;
  outline: 0;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-text {
  font-size: 0.875rem;
  color: #6c757d;
  margin-top: 0.25rem;
}

.text-danger {
  color: #dc3545;
}

.alert {
  padding: 0.75rem 1rem;
  border-radius: 0.375rem;
  margin-bottom: 1rem;
}

.alert-danger {
  color: #842029;
  background-color: #f8d7da;
  border: 1px solid #f5c2c7;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding-top: 1rem;
  margin-top: 1rem;
  border-top: 1px solid #dee2e6;
}

.btn {
  display: inline-flex;
  align-items: center;
  padding: 0.625rem 1.25rem;
  font-size: 0.9375rem;
  font-weight: 500;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.15s ease-in-out;
}

.btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.btn-primary {
  background-color: #0d6efd;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: #0b5ed7;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #5c636a;
}

.badge {
  font-size: 0.875rem;
  padding: 0.375rem 0.75rem;
  border-radius: 0.25rem;
  font-weight: 600;
}

.spinner-border {
  width: 1rem;
  height: 1rem;
  border-width: 0.15rem;
}

@media (max-width: 576px) {
  .modal-container {
    margin: 0;
    max-height: 100vh;
  }
  
  .modal-overlay {
    padding: 0;
    align-items: flex-start;
  }
}
</style>
