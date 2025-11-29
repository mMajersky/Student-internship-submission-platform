<template>
  <div class="company-registration-form">
    <form @submit.prevent="handleSubmit">
      <!-- Company Information -->
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">
            <i class="bi bi-building me-2"></i>
            {{ $t('companyRegistration.companyInfo') }}
          </h5>
          
          <div class="row mb-3">
            <!-- Company Name -->
            <div class="col-md-12">
              <label for="companyName" class="form-label">
                {{ $t('companyRegistration.companyName') }}<span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.company_name }"
                id="companyName"
                v-model="formData.companyName"
                :placeholder="$t('companyRegistration.companyNamePlaceholder')"
                maxlength="100"
                required
              />
              <div v-if="errors.company_name" class="invalid-feedback">
                {{ errors.company_name[0] }}
              </div>
            </div>
          </div>

          <!-- Address Fields -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="state" class="form-label">{{ $t('companyRegistration.state') }}</label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.state }"
                id="state"
                v-model="formData.state"
                :placeholder="$t('companyRegistration.statePlaceholder')"
              />
              <div v-if="errors.state" class="invalid-feedback">
                {{ errors.state[0] }}
              </div>
            </div>
            <div class="col-md-6">
              <label for="region" class="form-label">{{ $t('companyRegistration.region') }}</label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.region }"
                id="region"
                v-model="formData.region"
                :placeholder="$t('companyRegistration.regionPlaceholder')"
              />
              <div v-if="errors.region" class="invalid-feedback">
                {{ errors.region[0] }}
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label for="city" class="form-label">{{ $t('companyRegistration.city') }}</label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.city }"
                id="city"
                v-model="formData.city"
                :placeholder="$t('companyRegistration.cityPlaceholder')"
              />
              <div v-if="errors.city" class="invalid-feedback">
                {{ errors.city[0] }}
              </div>
            </div>
            <div class="col-md-4">
              <label for="postalCode" class="form-label">{{ $t('companyRegistration.postalCode') }}</label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.postal_code }"
                id="postalCode"
                v-model="formData.postalCode"
                :placeholder="$t('companyRegistration.postalCodePlaceholder')"
              />
              <div v-if="errors.postal_code" class="invalid-feedback">
                {{ errors.postal_code[0] }}
              </div>
            </div>
            <div class="col-md-4">
              <label for="street" class="form-label">{{ $t('companyRegistration.street') }}</label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.street }"
                id="street"
                v-model="formData.street"
                :placeholder="$t('companyRegistration.streetPlaceholder')"
              />
              <div v-if="errors.street" class="invalid-feedback">
                {{ errors.street[0] }}
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <label for="houseNumber" class="form-label">{{ $t('companyRegistration.houseNumber') }}</label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.house_number }"
                id="houseNumber"
                v-model="formData.houseNumber"
                :placeholder="$t('companyRegistration.houseNumberPlaceholder')"
              />
              <div v-if="errors.house_number" class="invalid-feedback">
                {{ errors.house_number[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Contact Person -->
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">
            <i class="bi bi-person me-2"></i>
            {{ $t('companyRegistration.contactPerson') }}
          </h5>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="contactPersonName" class="form-label">
                {{ $t('companyRegistration.contactPersonName') }}<span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.contact_person_name }"
                id="contactPersonName"
                v-model="formData.contactPersonName"
                :placeholder="$t('companyRegistration.contactPersonNamePlaceholder')"
                maxlength="100"
                required
              />
              <div v-if="errors.contact_person_name" class="invalid-feedback">
                {{ errors.contact_person_name[0] }}
              </div>
            </div>
            <div class="col-md-6">
              <label for="contactPersonSurname" class="form-label">
                {{ $t('companyRegistration.contactPersonSurname') }}<span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.contact_person_surname }"
                id="contactPersonSurname"
                v-model="formData.contactPersonSurname"
                :placeholder="$t('companyRegistration.contactPersonSurnamePlaceholder')"
                maxlength="100"
                required
              />
              <div v-if="errors.contact_person_surname" class="invalid-feedback">
                {{ errors.contact_person_surname[0] }}
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="contactPersonEmail" class="form-label">
                {{ $t('companyRegistration.contactPersonEmail') }}<span class="text-danger">*</span>
              </label>
              <input
                type="email"
                class="form-control"
                :class="{ 'is-invalid': errors.contact_person_email }"
                id="contactPersonEmail"
                v-model="formData.contactPersonEmail"
                :placeholder="$t('companyRegistration.contactPersonEmailPlaceholder')"
                maxlength="100"
                required
              />
              <div v-if="errors.contact_person_email" class="invalid-feedback">
                {{ errors.contact_person_email[0] }}
              </div>
            </div>
            <div class="col-md-6">
              <label for="contactPersonPhone" class="form-label">{{ $t('companyRegistration.contactPersonPhone') }}</label>
              <input
                type="tel"
                class="form-control"
                :class="{ 'is-invalid': errors.contact_person_phone }"
                id="contactPersonPhone"
                v-model="formData.contactPersonPhone"
                :placeholder="$t('companyRegistration.contactPersonPhonePlaceholder')"
                maxlength="50"
              />
              <div v-if="errors.contact_person_phone" class="invalid-feedback">
                {{ errors.contact_person_phone[0] }}
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label for="contactPersonPosition" class="form-label">{{ $t('companyRegistration.contactPersonPosition') }}</label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.contact_person_position }"
                id="contactPersonPosition"
                v-model="formData.contactPersonPosition"
                :placeholder="$t('companyRegistration.contactPersonPositionPlaceholder')"
                maxlength="100"
              />
              <div v-if="errors.contact_person_position" class="invalid-feedback">
                {{ errors.contact_person_position[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="submitError" class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ submitError }}
      </div>

      <!-- Success Message -->
      <div v-if="submitSuccess" class="alert alert-success mb-3">
        <i class="bi bi-check-circle me-2"></i>
        {{ submitSuccess }}
      </div>

      <!-- Action Buttons -->
      <div class="d-flex justify-content-end gap-2">
        <button
          v-if="showCancel"
          type="button"
          class="btn btn-secondary"
          @click="$emit('cancel')"
          :disabled="isSubmitting"
        >
          <i class="bi bi-x-circle me-2"></i>
          {{ $t('companyRegistration.cancel') }}
        </button>
        <button
          type="submit"
          class="btn btn-primary"
          :disabled="isSubmitting"
        >
          <span v-if="isSubmitting">
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            {{ $t('companyRegistration.submitting') }}
          </span>
          <span v-else>
            <i class="bi bi-send me-2"></i>
            {{ submitButtonText || $t('companyRegistration.submitRequest') }}
          </span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  apiEndpoint: {
    type: String,
    default: '/api/companies/create'
  },
  authToken: {
    type: String,
    default: null
  },
  showCancel: {
    type: Boolean,
    default: false
  },
  submitButtonText: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['success', 'cancel'])

// Form data
const formData = ref({
  companyName: '',
  state: '',
  region: '',
  city: '',
  postalCode: '',
  street: '',
  houseNumber: '',
  contactPersonName: '',
  contactPersonSurname: '',
  contactPersonEmail: '',
  contactPersonPhone: '',
  contactPersonPosition: ''
})

// Form state
const errors = ref({})
const isSubmitting = ref(false)
const submitError = ref(null)
const submitSuccess = ref(null)

// Reset form
const resetForm = () => {
  formData.value = {
    companyName: '',
    state: '',
    region: '',
    city: '',
    postalCode: '',
    street: '',
    houseNumber: '',
    contactPersonName: '',
    contactPersonSurname: '',
    contactPersonEmail: '',
    contactPersonPhone: '',
    contactPersonPosition: ''
  }
  errors.value = {}
  submitError.value = null
  submitSuccess.value = null
}

// Handle form submission
const handleSubmit = async () => {
  // Clear previous messages
  submitError.value = null
  submitSuccess.value = null
  errors.value = {}

  isSubmitting.value = true

  try {
    // Prepare headers
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }

    // Add auth token if provided
    if (props.authToken) {
      headers['Authorization'] = `Bearer ${props.authToken}`
    }

    // Send POST request
    const response = await fetch(props.apiEndpoint, {
      method: 'POST',
      headers: headers,
      body: JSON.stringify({
        name: formData.value.companyName.trim(),
        state: formData.value.state.trim() || null,
        region: formData.value.region.trim() || null,
        city: formData.value.city.trim() || null,
        postal_code: formData.value.postalCode.trim() || null,
        street: formData.value.street.trim() || null,
        house_number: formData.value.houseNumber.trim() || null,
        contact_person_name: formData.value.contactPersonName.trim(),
        contact_person_surname: formData.value.contactPersonSurname.trim(),
        contact_person_email: formData.value.contactPersonEmail.trim(),
        contact_person_phone: formData.value.contactPersonPhone.trim() || null,
        contact_person_position: formData.value.contactPersonPosition.trim() || null
      })
    })

    const data = await response.json()

    if (!response.ok) {
      // Handle validation errors (422)
      if (response.status === 422 && data.errors) {
        errors.value = data.errors
        submitError.value = data.message || t('companyRegistration.validationError')
      } else {
        throw new Error(data.message || t('companyRegistration.submitError'))
      }
    } else {
      // Success!
      submitSuccess.value = data.message || t('companyRegistration.submitSuccess')
      
      // Reset form after short delay
      setTimeout(() => {
        resetForm()
        emit('success', data)
      }, 2000)
    }
  } catch (error) {
    console.error('Error submitting company registration:', error)
    submitError.value = error.message || t('companyRegistration.unknownError')
  } finally {
    isSubmitting.value = false
  }
}

// Expose reset method
defineExpose({
  resetForm
})
</script>
