<template>
  <div class="manage-garants">
    <h3 class="mb-4">{{ $t('manageGarants.title') }}</h3>

    <!-- Create Garant Form -->
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title mb-3">{{ editingGarant ? $t('manageGarants.editGarant') : $t('manageGarants.createGarant') }}</h5>
        
        <form @submit.prevent="handleSubmit">
          <div class="row mb-3">
            <!-- Name field -->
            <div class="col-md-3">
              <label for="name" class="form-label">
                {{ $t('manageGarants.nameRequired') }}
              </label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.name }"
                id="name"
                v-model="formData.name"
                :placeholder="$t('manageGarants.namePlaceholder')"
                required
              />
              <div v-if="errors.name" class="invalid-feedback">
                {{ errors.name }}
              </div>
            </div>

            <!-- Surname field -->
            <div class="col-md-3">
              <label for="surname" class="form-label">
                {{ $t('manageGarants.surnameRequired') }}
              </label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.surname }"
                id="surname"
                v-model="formData.surname"
                :placeholder="$t('manageGarants.surnamePlaceholder')"
                required
              />
              <div v-if="errors.surname" class="invalid-feedback">
                {{ errors.surname }}
              </div>
            </div>

            <!-- Email field -->
            <div class="col-md-3">
              <label for="email" class="form-label">
                {{ $t('manageGarants.emailRequired') }}
              </label>
              <input
                type="email"
                class="form-control"
                :class="{ 'is-invalid': errors.email }"
                id="email"
                v-model="formData.email"
                :placeholder="$t('manageGarants.emailPlaceholder')"
                required
              />
              <div v-if="errors.email" class="invalid-feedback">
                {{ errors.email }}
              </div>
              <small class="text-muted">{{ $t('manageGarants.emailUnique') }}</small>
            </div>

            <!-- Password field -->
            <div class="col-md-3">
              <label for="password" class="form-label">
                {{ editingGarant ? $t('manageGarants.password') : $t('manageGarants.passwordRequired') }}
              </label>
              <input
                type="password"
                class="form-control"
                :class="{ 'is-invalid': errors.password }"
                id="password"
                v-model="formData.password"
                :placeholder="editingGarant ? $t('manageGarants.passwordEditPlaceholder') : $t('manageGarants.passwordPlaceholder')"
                minlength="6"
                :required="!editingGarant"
              />
              <div v-if="errors.password" class="invalid-feedback">
                {{ errors.password }}
              </div>
              <small class="text-muted">{{ editingGarant ? $t('manageGarants.passwordEditNote') : $t('manageGarants.passwordMinLength') }}</small>
            </div>
          </div>

          <!-- Info message -->
          <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle me-2"></i>
            {{ editingGarant ? $t('manageGarants.infoMessageEdit') : $t('manageGarants.infoMessageCreate') }}
          </div>

          <!-- Error message -->
          <div v-if="submitError" class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ submitError }}
          </div>

          <!-- Success message -->
          <div v-if="submitSuccess" class="alert alert-success mb-3">
            <i class="bi bi-check-circle me-2"></i>
            {{ submitSuccess }}
          </div>

          <!-- Action buttons -->
          <div class="d-flex justify-content-end gap-2">
            <button
              type="button"
              class="btn btn-secondary"
              @click="resetForm"
              :disabled="isSubmitting"
            >
              <i class="bi bi-x-circle me-2"></i>
              {{ editingGarant ? $t('manageGarants.cancelEdit') : $t('manageGarants.cancel') }}
            </button>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="isSubmitting"
            >
              <span v-if="isSubmitting">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                {{ editingGarant ? $t('manageGarants.updating') : $t('manageGarants.creating') }}
              </span>
              <span v-else>
                <i :class="editingGarant ? 'bi bi-check-circle me-2' : 'bi bi-plus-circle me-2'"></i>
                {{ editingGarant ? $t('manageGarants.updateGarantBtn') : $t('manageGarants.createGarantBtn') }}
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Garants List Table (Optional) -->
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title mb-0">{{ $t('manageGarants.garantsList') }}</h5>
          <button
            class="btn btn-sm btn-outline-primary"
            @click="fetchGarants"
            :disabled="isLoadingGarants"
          >
            <i class="bi bi-arrow-clockwise me-1"></i>
            {{ $t('manageGarants.refresh') }}
          </button>
        </div>

        <!-- Loading state -->
        <div v-if="isLoadingGarants" class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{ $t('manageGarants.loadingGarants') }}</span>
          </div>
          <p class="text-muted mt-2">{{ $t('manageGarants.loadingGarantsText') }}</p>
        </div>

        <!-- Empty state -->
        <div v-else-if="garants.length === 0" class="text-center py-5">
          <i class="bi bi-people fs-1 text-muted"></i>
          <p class="text-muted mt-3">{{ $t('manageGarants.noGarants') }}</p>
        </div>

        <!-- Garants table -->
        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>{{ $t('manageGarants.id') }}</th>
                <th>{{ $t('manageGarants.name') }}</th>
                <th>{{ $t('manageGarants.surname') }}</th>
                <th>{{ $t('manageGarants.email') }}</th>
                <th>{{ $t('manageGarants.created') }}</th>
                <th>{{ $t('manageGarants.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="garant in garants" :key="garant.id">
                <td>{{ garant.id }}</td>
                <td>{{ garant.name }}</td>
                <td>{{ garant.surname }}</td>
                <td>{{ garant.email }}</td>
                <td>{{ formatDate(garant.created_at) }}</td>
                <td>
                  <button
                    class="btn btn-sm btn-outline-primary me-1"
                    :title="$t('manageGarants.edit')"
                    @click="handleEditGarant(garant)"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    :title="$t('manageGarants.delete')"
                    @click="handleDeleteGarant(garant.id)"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Confirmation Dialog -->
    <ConfirmationDialog
      :is-visible="showDeleteConfirmation"
      :title="$t('confirmationDialog.deleteTitle')"
      :message="$t('confirmationDialog.deleteMessage')"
      :confirm-text="$t('confirmationDialog.confirm')"
      :cancel-text="$t('confirmationDialog.cancel')"
      type="danger"
      :requires-text-confirmation="true"
      confirmation-text-required="delete"
      :text-confirmation-label="$t('confirmationDialog.deleteTextLabel')"
      :text-confirmation-placeholder="$t('confirmationDialog.deleteTextPlaceholder')"
      :text-confirmation-hint="$t('confirmationDialog.deleteTextHint')"
      @confirm="confirmDeleteGarant"
      @cancel="cancelDeleteGarant"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { useI18n } from 'vue-i18n'
import ConfirmationDialog from '@/components/common/ConfirmationDialog.vue'

const { t } = useI18n()

const authStore = useAuthStore()

// Form data
const formData = ref({
  name: '',
  surname: '',
  email: '',
  password: ''
})

// Form state
const errors = ref({})
const isSubmitting = ref(false)
const submitError = ref(null)
const submitSuccess = ref(null)

// Garants list state
const garants = ref([])
const isLoadingGarants = ref(false)

// Editing state
const editingGarant = ref(null)

// Delete confirmation state
const showDeleteConfirmation = ref(false)
const garantToDelete = ref(null)

// Validate form
const validateForm = () => {
  errors.value = {}
  let isValid = true

  // Validate name
  if (!formData.value.name || formData.value.name.trim().length === 0) {
    errors.value.name = t('manageGarants.nameRequiredError')
    isValid = false
  }

  // Validate surname
  if (!formData.value.surname || formData.value.surname.trim().length === 0) {
    errors.value.surname = t('manageGarants.surnameRequiredError')
    isValid = false
  }

  // Validate email
  if (!formData.value.email || formData.value.email.trim().length === 0) {
    errors.value.email = t('manageGarants.emailRequiredError')
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
    errors.value.email = t('manageGarants.emailInvalidError')
    isValid = false
  }

  // Validate password
  if (!formData.value.password || formData.value.password.length < 6) {
    // Only require password for new garants, not when editing
    if (!editingGarant.value) {
      errors.value.password = t('manageGarants.passwordRequiredError')
      isValid = false
    } else if (formData.value.password && formData.value.password.length < 6) {
      // If editing and password is provided, it must be at least 6 characters
      errors.value.password = t('manageGarants.passwordTooShortError')
      isValid = false
    }
  }

  return isValid
}

// Reset form
const resetForm = () => {
  formData.value = {
    name: '',
    surname: '',
    email: '',
    password: ''
  }
  errors.value = {}
  submitError.value = null
  submitSuccess.value = null
  editingGarant.value = null
}

// Handle form submission
const handleSubmit = async () => {
  // Clear previous messages
  submitError.value = null
  submitSuccess.value = null

  // Validate form
  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    const isEditing = editingGarant.value !== null
    
    // Prepare payload
    const payload = {
      name: formData.value.name.trim(),
      surname: formData.value.surname.trim(),
      email: formData.value.email.trim()
    }
    
    // Only include password if provided (for editing) or always (for creating)
    if (!isEditing || formData.value.password) {
      payload.password = formData.value.password
    }

    // Determine API endpoint and method
    const url = isEditing 
      ? `/api/garants/${editingGarant.value.id}`
      : '/api/garants'
    const method = isEditing ? 'PUT' : 'POST'

    // Send POST/PUT request
    const response = await fetch(url, {
      method: method,
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload)
    })

    const data = await response.json()

    if (!response.ok) {
      // Handle specific error cases
      if (response.status === 409 || data.error?.includes('email')) {
        errors.value.email = t('manageGarants.emailExistsError')
        throw new Error(t('manageGarants.emailExistsError'))
      }
      throw new Error(data.error || data.message || (isEditing ? t('manageGarants.updateError') : t('manageGarants.createError')))
    }

    // Show success message
    submitSuccess.value = isEditing ? t('manageGarants.updateSuccess') : t('manageGarants.createSuccess')

    // Reset form
    formData.value = {
      name: '',
      surname: '',
      email: '',
      password: ''
    }
    errors.value = {}
    editingGarant.value = null

    // Refresh garants list
    await fetchGarants()

    // Clear success message after 5 seconds
    setTimeout(() => {
      submitSuccess.value = null
    }, 5000)

  } catch (error) {
    console.error('Error creating/updating garant:', error)
    submitError.value = error.message || (editingGarant.value ? t('manageGarants.updateErrorGeneric') : t('manageGarants.createErrorGeneric'))
  } finally {
    isSubmitting.value = false
  }
}

// Fetch garants list
const fetchGarants = async () => {
  isLoadingGarants.value = true

  try {
    const response = await fetch('/api/garants', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    })

    if (response.ok) {
      const data = await response.json()
      garants.value = data.data || data
    } else {
      console.error('Failed to fetch garants:', response.status)
    }
  } catch (error) {
    console.error('Error fetching garants:', error)
  } finally {
    isLoadingGarants.value = false
  }
}

// Handle edit garant
const handleEditGarant = (garant) => {
  editingGarant.value = garant
  
  // Populate form with garant data
  formData.value = {
    name: garant.name,
    surname: garant.surname,
    email: garant.email,
    password: '' // Don't populate password for security
  }
  
  // Clear any previous errors
  errors.value = {}
  submitError.value = null
  submitSuccess.value = null
  
  // Scroll to top of form
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Handle delete garant
const handleDeleteGarant = (garantId) => {
  garantToDelete.value = garantId
  showDeleteConfirmation.value = true
}

const confirmDeleteGarant = async () => {
  if (!garantToDelete.value) return

  try {
    const response = await fetch(`/api/garants/${garantToDelete.value}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    })

    if (!response.ok) {
      const data = await response.json()
      throw new Error(data.error || data.message || 'Failed to delete garant')
    }

    // Refresh garants list
    await fetchGarants()

    // Show success message
    alert(t('manageGarants.deleteSuccess'))
  } catch (error) {
    console.error('Error deleting garant:', error)
    alert(error.message || t('manageGarants.deleteError'))
  } finally {
    showDeleteConfirmation.value = false
    garantToDelete.value = null
  }
}

const cancelDeleteGarant = () => {
  showDeleteConfirmation.value = false
  garantToDelete.value = null
}

// Format date
const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('sk-SK', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Load garants when component is mounted
onMounted(() => {
  fetchGarants()
})
</script>
