<template>
  <div class="container">
    <div class="row justify-content-center pt-5">
      <div class="col-md-8 col-lg-6">
        <div class="card p-4 shadow border text-start">
          <div class="card-body">
            <!-- HEADER -->
            <div class="text-center mb-4">
              <h3 class="card-title fw-bolder">
                {{ formData.role === 'company' ? $t('auth.register.companyTitle') : $t('auth.register.studentTitle') }}
              </h3>
              <p class="text-muted">{{ $t('auth.register.subtitle') }}</p>
            </div>

            <!-- ERROR -->
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>

            <!-- FORM -->
            <form @submit.prevent="handleRegister" novalidate>
              <!-- Common fields -->
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

                <div v-if="formData.role === 'student'" class="col-md-6 mb-3">
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

                <div v-if="formData.role === 'company'" class="col-md-6 mb-3">
                  <label for="company_name" class="form-label">
                    {{ $t('auth.register.companyName') }} <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="company_name"
                    v-model="formData.company_name"
                    required
                    :placeholder="$t('auth.register.companyNamePlaceholder')"
                  />
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">
                  {{ formData.role === 'student' ? $t('auth.register.universityEmail') : $t('auth.register.email') }} <span class="text-danger">*</span>
                </label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="formData.email"
                  required
                  :placeholder="formData.role === 'student' ? $t('auth.register.universityEmailPlaceholder') : $t('auth.register.alternativeEmailPlaceholder')"
                />
                <small v-if="formData.role === 'student'" class="form-text text-muted">
                  {{ $t('auth.register.universityEmailHelp') }}
                </small>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">
                  {{ $t('auth.register.password') }} <span class="text-danger">*</span>
                </label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="formData.password"
                  required
                  :placeholder="$t('auth.register.passwordPlaceholder')"
                />
              </div>

              <!-- Student-specific fields -->
              <template v-if="formData.role === 'student'">
                <div class="mb-3">
                  <label for="alternative_email" class="form-label">{{ $t('auth.register.alternativeEmail') }}</label>
                  <input
                    type="email"
                    class="form-control"
                    id="alternative_email"
                    v-model="formData.alternative_email"
                    :placeholder="$t('auth.register.alternativeEmailPlaceholder')"
                  />
                  <small class="form-text text-muted">
                    {{ $t('auth.register.alternativeEmailHelp') }}
                  </small>
                </div>

                <div class="mb-3">
                  <label for="phone_number" class="form-label">{{ $t('auth.register.phoneNumber') }}</label>
                  <input
                    type="text"
                    class="form-control"
                    id="phone_number"
                    v-model="formData.phone_number"
                    :placeholder="$t('auth.register.phonePlaceholder')"
                  />
                </div>

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
              </template>

              <!-- Address fields (for students - required) -->
              <template v-if="formData.role === 'student'">
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
                      id="postal_code"
                      v-model="formData.postal_code"
                      required
                      :placeholder="$t('auth.register.postalCodePlaceholder')"
                    />
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
              </template>

              <!-- Address fields (for companies - required) -->
              <template v-if="formData.role === 'company'">
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
                      id="postal_code"
                      v-model="formData.postal_code"
                      required
                      :placeholder="$t('auth.register.postalCodePlaceholder')"
                    />
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
              </template>

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

const API_URL = import.meta.env.VITE_API_URL

const router = useRouter()
const authStore = useAuthStore()

const formData = reactive({
  name: '',
  surname: '',
  email: '',
  password: '',
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
  company_name: '',
  role: history.state.role || 'student',
})

const isLoading = computed(() => authStore.isLoading)
const errorMessage = ref(null)

const handleRegister = async () => {
  // Basic client-side validation
  if (!formData.name || !formData.email || !formData.password) {
    errorMessage.value = t('auth.register.validation.requiredFields')
    return
  }

  if (formData.role === 'student') {
    if (!formData.surname) {
      errorMessage.value = t('auth.register.validation.surnameRequired')
      return
    }
    if (!formData.study_level) {
      errorMessage.value = t('auth.register.validation.studyLevelRequired')
      return
    }
    if (!formData.study_field) {
      errorMessage.value = t('auth.register.validation.studyFieldRequired')
      return
    }
    if (!formData.state || !formData.city || !formData.postal_code || !formData.street || !formData.house_number) {
      errorMessage.value = t('auth.register.validation.addressRequired')
      return
    }
    // Validate university email
    const emailDomain = formData.email.split('@')[1]?.toLowerCase()
    if (!emailDomain || emailDomain !== 'student.ukf.sk') {
      errorMessage.value = t('auth.register.validation.universityEmailRequired')
      return
    }
  }

  try {
    errorMessage.value = null
    authStore.isLoading = true

    const response = await fetch(`${API_URL}/api/auth/register`, {
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
