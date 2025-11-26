<template>
  <div class="container">
    <div class="row justify-content-center pt-5">
      <div class="col-md-10 col-lg-8">
        <div class="card p-4 shadow border text-start">
          <div class="card-body">
            <!-- HEADER -->
            <div class="text-center mb-4">
              <h3 class="card-title fw-bolder">
                {{ $t('auth.register.companyTitle') }}
              </h3>
              <p class="text-muted">{{ $t('auth.register.subtitle') }}</p>
            </div>

            <!-- ERROR -->
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>

            <!-- FORM -->
            <form @submit.prevent="handleRegister" novalidate>
              <!-- Company Information -->
                <div class="card-body">
                  <h5 class="card-title mb-3">
                    <i class="bi bi-building me-2"></i>
                    {{ $t('companyRegistration.companyInfo') }}
                  </h5>
                  
                  <!-- Company Name -->
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label for="company_name" class="form-label">
                        {{ $t('companyRegistration.companyName') }}<span class="text-danger">*</span>
                      </label>
                      <input
                        type="text"
                        class="form-control"
                        id="company_name"
                        v-model="formData.company_name"
                        required
                        :placeholder="$t('companyRegistration.companyNamePlaceholder')"
                        maxlength="100"
                      />
                    </div>
                  </div>

                  <!-- Address Fields -->
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="state" class="form-label">{{ $t('companyRegistration.state') }}</label>
                      <input
                        type="text"
                        class="form-control"
                        id="state"
                        v-model="formData.state"
                        :placeholder="$t('companyRegistration.statePlaceholder')"
                        maxlength="100"
                      />
                    </div>
                    <div class="col-md-6">
                      <label for="region" class="form-label">{{ $t('companyRegistration.region') }}</label>
                      <input
                        type="text"
                        class="form-control"
                        id="region"
                        v-model="formData.region"
                        :placeholder="$t('companyRegistration.regionPlaceholder')"
                        maxlength="100"
                      />
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-md-4">
                      <label for="city" class="form-label">{{ $t('companyRegistration.city') }}</label>
                      <input
                        type="text"
                        class="form-control"
                        id="city"
                        v-model="formData.city"
                        :placeholder="$t('companyRegistration.cityPlaceholder')"
                        maxlength="100"
                      />
                    </div>
                    <div class="col-md-4">
                      <label for="postal_code" class="form-label">{{ $t('companyRegistration.postalCode') }}</label>
                      <input
                        type="text"
                        class="form-control"
                        id="postal_code"
                        v-model="formData.postal_code"
                        :placeholder="$t('companyRegistration.postalCodePlaceholder')"
                        maxlength="20"
                      />
                    </div>
                    <div class="col-md-4">
                      <label for="street" class="form-label">{{ $t('companyRegistration.street') }}</label>
                      <input
                        type="text"
                        class="form-control"
                        id="street"
                        v-model="formData.street"
                        :placeholder="$t('companyRegistration.streetPlaceholder')"
                        maxlength="100"
                      />
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-4">
                      <label for="house_number" class="form-label">{{ $t('companyRegistration.houseNumber') }}</label>
                      <input
                        type="text"
                        class="form-control"
                        id="house_number"
                        v-model="formData.house_number"
                        :placeholder="$t('companyRegistration.houseNumberPlaceholder')"
                        maxlength="20"
                      />
                    </div>
                  </div>
                </div>

              <!-- Contact Person -->
                <div class="card-body">
                  <h5 class="card-title mb-3">
                    <i class="bi bi-person me-2"></i>
                    {{ $t('companyRegistration.contactPerson') }}
                  </h5>
                  
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="name" class="form-label">
                        {{ $t('companyRegistration.contactPersonName') }}<span class="text-danger">*</span>
                      </label>
                      <input
                        type="text"
                        class="form-control"
                        id="name"
                        v-model="formData.name"
                        required
                        :placeholder="$t('companyRegistration.contactPersonNamePlaceholder')"
                        maxlength="100"
                      />
                    </div>
                    <div class="col-md-6">
                      <label for="surname" class="form-label">
                        {{ $t('companyRegistration.contactPersonSurname') }}<span class="text-danger">*</span>
                      </label>
                      <input
                        type="text"
                        class="form-control"
                        id="surname"
                        v-model="formData.surname"
                        required
                        :placeholder="$t('companyRegistration.contactPersonSurnamePlaceholder')"
                        maxlength="100"
                      />
                    </div>
                  </div>

                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="email" class="form-label">
                        {{ $t('companyRegistration.contactPersonEmail') }}<span class="text-danger">*</span>
                      </label>
                      <input
                        type="email"
                        class="form-control"
                        id="email"
                        v-model="formData.email"
                        required
                        :placeholder="$t('companyRegistration.contactPersonEmailPlaceholder')"
                        maxlength="100"
                      />
                    </div>
                    <div class="col-md-6">
                      <label for="phone_number" class="form-label">{{ $t('companyRegistration.contactPersonPhone') }}</label>
                      <input
                        type="tel"
                        class="form-control"
                        id="phone_number"
                        v-model="formData.phone_number"
                        :placeholder="$t('companyRegistration.contactPersonPhonePlaceholder')"
                        maxlength="50"
                      />
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6">
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
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../stores/auth'

const { t } = useI18n()

const router = useRouter()
const authStore = useAuthStore()

const formData = reactive({
  name: '',
  surname: '',
  company_name: '',
  email: '',
  password: '',
  phone_number: '',
  state: '',
  region: '',
  city: '',
  postal_code: '',
  street: '',
  house_number: '',
  role: 'company'
})

const isLoading = ref(false)
const errorMessage = ref(null)

const handleRegister = async () => {
  // Basic client-side validation
  if (!formData.name || !formData.surname || !formData.company_name || !formData.email || !formData.password) {
    errorMessage.value = t('auth.register.validation.requiredFields')
    return
  }

  if (!formData.state || !formData.city || !formData.postal_code || !formData.street || !formData.house_number) {
    errorMessage.value = t('auth.register.validation.addressRequired')
    return
  }

  try {
    errorMessage.value = null
    isLoading.value = true

    const response = await fetch(`/api/auth/register`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(formData),
    })

    const data = await response.json().catch(() => null)
    isLoading.value = false

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
    isLoading.value = false
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
