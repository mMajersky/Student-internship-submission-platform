<template>
  <div class="container">
    <div class="row justify-content-center pt-5">
      <div class="col-md-8 col-lg-6">
        <div class="card p-4 shadow border text-start">
          <div class="card-body">
            <!-- HEADER -->
            <div class="text-center mb-4">
              <h3 class="card-title fw-bolder">
                {{ $t('auth.register.studentTitle') }}
              </h3>
              <p class="text-muted">{{ $t('auth.register.subtitle') }}</p>
            </div>

            <!-- ERROR -->
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>

            <!-- FORM -->
            <form @submit.prevent="handleRegister" novalidate>
              <!-- Name and Surname -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="name" class="form-label">
                    {{ $t('auth.register.name') }} <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="name"
                    v-model="formData.name"
                    required
                    :placeholder="$t('auth.register.namePlaceholder')"
                  />
                </div>

                <div class="col-md-6 mb-3">
                  <label for="surname" class="form-label">
                    {{ $t('auth.register.surname') }} <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="surname"
                    v-model="formData.surname"
                    required
                    :placeholder="$t('auth.register.surnamePlaceholder')"
                  />
                </div>
              </div>

              <!-- University Email -->
              <div class="mb-3">
                <label for="email" class="form-label">
                  {{ $t('auth.register.universityEmail') }} <span class="text-danger">*</span>
                </label>
                <input
                  type="email"
                  class="form-control"
                  :class="{ 'is-invalid': errors.email }"
                  id="email"
                  v-model="formData.email"
                  @input="validateEmail"
                  required
                  :placeholder="$t('auth.register.universityEmailPlaceholder')"
                />
                <div v-if="errors.email" class="invalid-feedback">
                  {{ errors.email }}
                </div>
                <small v-else class="form-text text-muted">
                  {{ $t('auth.register.universityEmailHelp') }}
                </small>
              </div>

              <!-- Password -->
              <div class="mb-3">
                <label for="password" class="form-label">
                  {{ $t('auth.register.password') }} <span class="text-danger">*</span>
                </label>
                <input
                  type="password"
                  class="form-control"
                  :class="{ 'is-invalid': errors.password }"
                  id="password"
                  v-model="formData.password"
                  @input="validatePassword"
                  required
                  minlength="8"
                  :placeholder="$t('auth.register.passwordPlaceholder')"
                />
                <div v-if="errors.password" class="invalid-feedback">
                  {{ errors.password }}
                </div>
                <small v-if="formData.password && !errors.password" class="form-text text-muted">
                  {{ $t('auth.register.passwordMinLength') }}
                </small>
              </div>

              <!-- Password Confirmation -->
              <div class="mb-3">
                <label for="password_confirmation" class="form-label">
                  {{ $t('auth.register.passwordConfirmation') }} <span class="text-danger">*</span>
                </label>
                <input
                  type="password"
                  class="form-control"
                  :class="{ 'is-invalid': errors.password_confirmation }"
                  id="password_confirmation"
                  v-model="formData.password_confirmation"
                  @input="validatePasswordConfirmation"
                  required
                  minlength="8"
                  :placeholder="$t('auth.register.passwordConfirmationPlaceholder')"
                />
                <div v-if="errors.password_confirmation" class="invalid-feedback">
                  {{ errors.password_confirmation }}
                </div>
              </div>

              <!-- Alternative Email -->
              <div class="mb-3">
                <label for="alternative_email" class="form-label">{{ $t('auth.register.alternativeEmail') }}</label>
                <input
                  type="email"
                  class="form-control"
                  :class="{ 'is-invalid': errors.alternative_email }"
                  id="alternative_email"
                  v-model="formData.alternative_email"
                  @input="validateAlternativeEmail"
                  :placeholder="$t('auth.register.alternativeEmailPlaceholder')"
                />
                <div v-if="errors.alternative_email" class="invalid-feedback">
                  {{ errors.alternative_email }}
                </div>
                <small class="form-text text-muted">
                  {{ $t('auth.register.alternativeEmailHelp') }}
                </small>
              </div>

              <!-- Phone Number -->
              <div class="mb-3">
                <label for="phone_number" class="form-label">{{ $t('auth.register.phoneNumber') }}</label>
                <input
                  type="tel"
                  class="form-control"
                  :class="{ 'is-invalid': errors.phone_number }"
                  id="phone_number"
                  v-model="formData.phone_number"
                  :placeholder="$t('auth.register.phonePlaceholder')"
                  pattern="[0-9+\s\-()]+"
                />
                <div v-if="errors.phone_number" class="invalid-feedback">
                  {{ errors.phone_number }}
                </div>
                <small v-if="formData.phone_number && !errors.phone_number" class="form-text text-muted">
                  {{ $t('auth.register.phoneFormat') }}
                </small>
              </div>

              <!-- Study Level and Field -->
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="study_level" class="form-label">
                    {{ $t('auth.register.studyLevel') }} <span class="text-danger">*</span>
                  </label>
                  <select
                    class="form-control"
                    id="study_level"
                    v-model="formData.study_level"
                    required
                  >
                    <option value="">{{ $t('auth.register.selectOption') }}</option>
                    <option value="Bc.">{{ $t('auth.register.bcLevel') }}</option>
                    <option value="Mgr.">{{ $t('auth.register.mgrLevel') }}</option>
                    <option value="Ing.">{{ $t('auth.register.ingLevel') }}</option>
                    <option value="PhD.">{{ $t('auth.register.phdLevel') }}</option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="study_field" class="form-label">
                    {{ $t('auth.register.studyField') }} <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="study_field"
                    v-model="formData.study_field"
                    required
                    :placeholder="$t('auth.register.studyFieldPlaceholder')"
                  />
                </div>
              </div>

              <!-- Address Section -->
              <h5 class="mt-4 mb-3">{{ $t('auth.register.address') }}</h5>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="state" class="form-label">
                    {{ $t('auth.register.state') }} <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="state"
                    v-model="formData.state"
                    required
                    :placeholder="$t('auth.register.statePlaceholder')"
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="region" class="form-label">
                    {{ $t('auth.register.region') }} <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="region"
                    v-model="formData.region"
                    required
                    :placeholder="$t('auth.register.regionPlaceholder')"
                  />
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="city" class="form-label">
                    {{ $t('auth.register.city') }} <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="city"
                    v-model="formData.city"
                    required
                    :placeholder="$t('auth.register.cityPlaceholder')"
                  />
                </div>
                <div class="col-md-6 mb-3">
                  <label for="postal_code" class="form-label">
                    {{ $t('auth.register.postalCode') }} <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.postal_code }"
                    id="postal_code"
                    v-model="formData.postal_code"
                    @input="validatePostalCode"
                    required
                    :placeholder="$t('auth.register.postalCodePlaceholder')"
                    pattern="[0-9]{5}"
                    maxlength="5"
                  />
                  <div v-if="errors.postal_code" class="invalid-feedback">
                    {{ errors.postal_code }}
                  </div>
                  <small v-if="formData.postal_code && !errors.postal_code" class="form-text text-muted">
                    {{ $t('auth.register.postalCodeFormat') }}
                  </small>
                </div>
              </div>

              <div class="row">
                <div class="col-md-8 mb-3">
                  <label for="street" class="form-label">
                    {{ $t('auth.register.street') }} <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="street"
                    v-model="formData.street"
                    required
                    :placeholder="$t('auth.register.streetPlaceholder')"
                  />
                </div>
                <div class="col-md-4 mb-3">
                  <label for="house_number" class="form-label">
                    {{ $t('auth.register.houseNumber') }} <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="house_number"
                    v-model="formData.house_number"
                    required
                    :placeholder="$t('auth.register.houseNumberPlaceholder')"
                  />
                </div>
              </div>

              <!-- Submit -->
              <div class="d-grid mt-3">
                <button type="submit" class="btn btn-primary" :disabled="isLoading">
                  <span
                    v-if="isLoading"
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  <span v-else>{{ $t('auth.register.registerButton') }}</span>
                </button>
              </div>
            </form>

            <div class="text-center mt-4">
              <p class="text-muted small">
                {{ $t('auth.register.hasAccount') }}
                <router-link to="/login" class="text-primary text-decoration-none fw-medium">
                  {{ $t('auth.register.login') }}
                </router-link>.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../stores/auth'

const { t } = useI18n()

const router = useRouter()
const authStore = useAuthStore()

const formData = reactive({
  name: '',
  surname: '',
  email: '',
  password: '',
  password_confirmation: '',
  alternative_email: '',
  phone_number: '',
  study_level: '',
  study_field: '',
  state: '',
  region: '',
  city: '',
  postal_code: '',
  street: '',
  house_number: '',
  role: 'student'
})

const isLoading = computed(() => authStore.isLoading)
const errorMessage = ref(null)
const errors = reactive({})

// Validation functions
const validatePostalCode = () => {
  if (!formData.postal_code) {
    errors.postal_code = null
    return
  }
  const postalCodeRegex = /^[0-9]{5}$/
  if (!postalCodeRegex.test(formData.postal_code)) {
    errors.postal_code = t('auth.register.validation.postalCodeInvalid')
  } else {
    errors.postal_code = null
  }
}

const validateEmail = () => {
  if (!formData.email) {
    errors.email = null
    return
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(formData.email)) {
    errors.email = t('auth.register.validation.emailInvalid')
  } else {
    const emailDomain = formData.email.split('@')[1]?.toLowerCase()
    if (!emailDomain || emailDomain !== 'student.ukf.sk') {
      errors.email = t('auth.register.validation.universityEmailRequired')
    } else {
      errors.email = null
    }
  }
}

const validateAlternativeEmail = () => {
  if (!formData.alternative_email) {
    errors.alternative_email = null
    return
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(formData.alternative_email)) {
    errors.alternative_email = t('auth.register.validation.emailInvalid')
  } else {
    errors.alternative_email = null
  }
}

const validatePassword = () => {
  if (!formData.password) {
    errors.password = null
    return
  }
  if (formData.password.length < 8) {
    errors.password = t('auth.register.validation.passwordTooShort')
  } else {
    errors.password = null
  }
}

const validatePasswordConfirmation = () => {
  if (!formData.password_confirmation) {
    errors.password_confirmation = null
    return
  }
  if (formData.password !== formData.password_confirmation) {
    errors.password_confirmation = t('auth.register.validation.passwordConfirmationMismatch')
  } else {
    errors.password_confirmation = null
  }
}

const validatePhone = () => {
  if (!formData.phone_number) {
    errors.phone_number = null
    return
  }
  const phoneRegex = /^[+]?[0-9\s\-()]{9,}$/
  if (!phoneRegex.test(formData.phone_number.replace(/\s/g, ''))) {
    errors.phone_number = t('auth.register.validation.phoneInvalid')
  } else {
    errors.phone_number = null
  }
}

const validateForm = () => {
  // Clear previous errors
  Object.keys(errors).forEach(key => errors[key] = null)
  
  // Validate required fields
  if (!formData.name || !formData.name.trim()) {
    errorMessage.value = t('auth.register.validation.nameRequired')
    return false
  }

  if (!formData.surname || !formData.surname.trim()) {
    errorMessage.value = t('auth.register.validation.surnameRequired')
    return false
  }

  if (!formData.email) {
    errorMessage.value = t('auth.register.validation.emailRequired')
    return false
  }

  validateEmail()
  if (errors.email) {
    errorMessage.value = errors.email
    return false
  }

  if (!formData.password) {
    errorMessage.value = t('auth.register.validation.passwordRequired')
    return false
  }

  validatePassword()
  if (errors.password) {
    errorMessage.value = errors.password
    return false
  }

  if (!formData.password_confirmation) {
    errorMessage.value = t('auth.register.validation.passwordConfirmationRequired')
    return false
  }

  validatePasswordConfirmation()
  if (errors.password_confirmation) {
    errorMessage.value = errors.password_confirmation
    return false
  }

  if (formData.alternative_email) {
    validateAlternativeEmail()
    if (errors.alternative_email) {
      errorMessage.value = errors.alternative_email
      return false
    }
  }

  if (formData.phone_number) {
    validatePhone()
    if (errors.phone_number) {
      errorMessage.value = errors.phone_number
      return false
    }
  }

  if (!formData.study_level) {
    errorMessage.value = t('auth.register.validation.studyLevelRequired')
    return false
  }

  if (!formData.study_field || !formData.study_field.trim()) {
    errorMessage.value = t('auth.register.validation.studyFieldRequired')
    return false
  }

  if (!formData.state || !formData.state.trim()) {
    errorMessage.value = t('auth.register.validation.stateRequired')
    return false
  }

  if (!formData.region || !formData.region.trim()) {
    errorMessage.value = t('auth.register.validation.regionRequired')
    return false
  }

  if (!formData.city || !formData.city.trim()) {
    errorMessage.value = t('auth.register.validation.cityRequired')
    return false
  }

  if (!formData.postal_code) {
    errorMessage.value = t('auth.register.validation.postalCodeRequired')
    return false
  }

  validatePostalCode()
  if (errors.postal_code) {
    errorMessage.value = errors.postal_code
    return false
  }

  if (!formData.street || !formData.street.trim()) {
    errorMessage.value = t('auth.register.validation.streetRequired')
    return false
  }

  if (!formData.house_number || !formData.house_number.trim()) {
    errorMessage.value = t('auth.register.validation.houseNumberRequired')
    return false
  }

  return true
}

const handleRegister = async () => {
  // Validate form
  if (!validateForm()) {
    return
  }

  try {
    errorMessage.value = null
    authStore.isLoading = true

    const response = await fetch(`/api/auth/register`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(formData),
    })

    const data = await response.json().catch(() => null)
    authStore.isLoading = false

    if (!response.ok) {
      console.error('Register error:', data)

      // Handle validation errors from Laravel
      if (data?.errors) {
        const firstError = Object.values(data.errors)[0]
        errorMessage.value = Array.isArray(firstError) ? firstError[0] : firstError
      } else {
        errorMessage.value = data?.message || t('auth.register.validation.registerFailed')
      }
      return
    }

    router.push('/login')
  } catch (error) {
    authStore.isLoading = false
    console.error('Fetch error:', error)
    errorMessage.value = t('auth.register.validation.registerError')
  }
}
</script>

<style scoped>
.form-check-label {
  padding-top: 2px;
}

.card.text-start,
.card.text-start * {
  text-align: left !important;
}

.card.text-start .btn.btn-primary {
  text-align: center !important;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
