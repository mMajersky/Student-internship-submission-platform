<template>
  <div class="container">
    <div class="row justify-content-center pt-5">
      <div class="col-md-6 col-lg-4">
        <div class="card p-4 shadow border text-start">
          <div class="card-body">
            <!-- HEADER -->
            <div class="text-center mb-4">
              <h3 class="card-title fw-bolder">{{ $t('auth.login.title') }}</h3>
              <p class="text-muted">{{ $t('auth.login.subtitle') }}</p>
            </div>

            <!-- ERROR -->
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>

            <!-- FORM -->
            <form @submit.prevent="handleLogin" novalidate>
              <div class="mb-3">
                <label for="email" class="form-label">
                  {{ $t('auth.login.email') }} <span class="text-danger">*</span>
                </label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="formData.email"
                  required
                  :placeholder="$t('auth.login.emailPlaceholder')"
                />
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">
                  {{ $t('auth.login.password') }} <span class="text-danger">*</span>
                </label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="formData.password"
                  required
                  :placeholder="$t('auth.login.passwordPlaceholder')"
                />
              </div>

              <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="formData.remember"
                    id="rememberMe"
                  />
                  <label class="form-check-label" for="rememberMe">{{ $t('auth.login.remember') }}</label>
                </div>
                <a href="#" class="small">{{ $t('auth.login.forgotPassword') }}</a>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary" :disabled="isLoading">
                  <span
                    v-if="isLoading"
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  <span v-else>{{ $t('auth.login.loginButton') }}</span>
                </button>
              </div>
            </form>

            <div class="text-center mt-4">
              <p class="text-muted small">
                {{ $t('auth.login.noAccount') }}

                <a href="#">{{ $t('auth.login.registerStudent') }}</a> alebo

                <a href="#">{{ $t('auth.login.registerCompany') }}</a>.
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

const router = useRouter()
const authStore = useAuthStore()
const { t } = useI18n()

const formData = reactive({
  email: '',
  password: '',
  remember: false,
})

const isLoading = computed(() => authStore.isLoading)
const errorMessage = computed(() => authStore.error)

const handleLogin = async () => {
  if (!formData.email || !formData.password) {
    authStore.error = t('auth.login.requiredFields')
    return
  }

  try {
    await authStore.login({
      email: formData.email,
      password: formData.password,
    })

    // Redirect based on user role
    if (authStore.isAdmin || authStore.isGarant) {
      router.push('/dashboard')
    } else if (authStore.isStudent) {
      router.push('/student-dashboard')
    } else if (authStore.isCompany) {
      router.push('/company-dashboard')
    } else {
      router.push('/')
    }
  } catch (error) {
    console.error('Login error:', error.message)
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
