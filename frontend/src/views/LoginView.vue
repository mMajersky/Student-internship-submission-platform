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

            <!-- SUCCESS MESSAGE FOR PASSWORD RESET -->
            <div v-if="resetEmailSent" class="alert alert-success" role="alert">
              {{ $t('auth.login.resetEmailSent') }}
            </div>

            <!-- LOGIN FORM -->
            <form v-if="!showForgotPassword" @submit.prevent="handleLogin" novalidate>
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
                <a href="#" @click.prevent="showForgotPassword = true" class="small">{{ $t('auth.login.forgotPassword') }}</a>
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

            <!-- FORGOT PASSWORD FORM -->
            <form v-else @submit.prevent="handleForgotPassword" novalidate>
              <div class="mb-3">
                <label for="resetEmail" class="form-label">
                  {{ $t('auth.login.email') }} <span class="text-danger">*</span>
                </label>
                <input
                  type="email"
                  class="form-control"
                  id="resetEmail"
                  v-model="resetEmail"
                  required
                  :placeholder="$t('auth.login.emailPlaceholder')"
                />
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary" :disabled="isResetting">
                  <span
                    v-if="isResetting"
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  <span v-else>{{ $t('auth.login.sendResetLink') }}</span>
                </button>
                <button type="button" class="btn btn-outline-secondary" @click="showForgotPassword = false">
                  {{ $t('auth.login.backToLogin') }}
                </button>
              </div>
            </form>

            <!-- REGISTRATION LINKS -->
            <div class="text-center mt-4">
              <p class="text-muted small">
                {{ $t('auth.login.noAccount') }}
                <router-link
                  to="/register"
                  class="text-primary fw-semibold text-decoration-none">
                  {{ $t('auth.login.registerStudent') }}
                </router-link>
                {{ $t('auth.login.or') }}
                <router-link
                  to="/register-company"
                  class="text-primary fw-semibold text-decoration-none">
                  {{ $t('auth.login.registerCompany') }}
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

const router = useRouter()
const authStore = useAuthStore()
const { t } = useI18n()

const formData = reactive({
  email: '',
  password: '',
  remember: false,
})

// Forgot password state
const showForgotPassword = ref(false)
const resetEmail = ref('')
const isResetting = ref(false)
const resetEmailSent = ref(false)

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

const handleForgotPassword = async () => {
  if (!resetEmail.value) {
    authStore.error = t('auth.login.emailRequired')
    return
  }

  isResetting.value = true
  authStore.error = null
  resetEmailSent.value = false

  try {
    const response = await fetch('/api/password/forgot', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ email: resetEmail.value })
    })

    const data = await response.json()

    // Always show success message (don't reveal if email exists)
    resetEmailSent.value = true
    showForgotPassword.value = false
    resetEmail.value = ''
  } catch (error) {
    console.error('Password reset error:', error.message)
    authStore.error = t('auth.login.resetError')
  } finally {
    isResetting.value = false
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
