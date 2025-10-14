<template>
  <div class="min-vh-100 d-flex align-items-center bg-light">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
          <div class="card shadow">
            <div class="card-body p-4">
              <div class="text-center mb-4">
                <i class="bi bi-person-plus-fill text-primary fs-1"></i>
                <h4 class="mt-3 mb-1">Registrácia</h4>
                <p class="text-muted">Vytvorte si nový účet</p>
              </div>

              <!-- Error Alert -->
              <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ errorMessage }}
                <button type="button" class="btn-close" @click="errorMessage = ''"></button>
              </div>

              <!-- Success Alert -->
              <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
                {{ successMessage }}
                <button type="button" class="btn-close" @click="successMessage = ''"></button>
              </div>

              <form @submit.prevent="handleRegister">
                <div class="mb-3">
                  <label for="name" class="form-label">Meno</label>
                  <input
                    type="text"
                    class="form-control"
                    id="name"
                    v-model="formData.name"
                    required
                    :disabled="isLoading"
                  />
                </div>

                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="email"
                    v-model="formData.email"
                    required
                    :disabled="isLoading"
                  />
                </div>

                <div class="mb-3">
                  <label for="password" class="form-label">Heslo</label>
                  <input
                    type="password"
                    class="form-control"
                    id="password"
                    v-model="formData.password"
                    required
                    :disabled="isLoading"
                  />
                </div>

                <div class="mb-3">
                  <label for="password_confirmation" class="form-label">Potvrdenie hesla</label>
                  <input
                    type="password"
                    class="form-control"
                    id="password_confirmation"
                    v-model="formData.password_confirmation"
                    required
                    :disabled="isLoading"
                  />
                </div>

                <div class="mb-3">
                  <label for="role" class="form-label">Rola</label>
                  <select
                    class="form-select"
                    id="role"
                    v-model="formData.role"
                    required
                    :disabled="isLoading"
                  >
                    <option value="">Vyberte rolu</option>
                    <option value="student">Študent</option>
                    <option value="garant">Garant</option>
                    <option value="admin">Admin</option>
                  </select>
                </div>

                <button
                  type="submit"
                  class="btn btn-primary w-100"
                  :disabled="isLoading"
                >
                  <span
                    v-if="isLoading"
                    class="spinner-border spinner-border-sm me-2"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  {{ isLoading ? 'Registruje sa...' : 'Registrovať sa' }}
                </button>
              </form>

              <div class="text-center mt-4">
                <p class="text-muted mb-0">
                  Už máte účet?
                  <router-link to="/login" class="text-primary text-decoration-none fw-medium">
                    Prihláste sa
                  </router-link>
                </p>
              </div>
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

const router = useRouter()

const formData = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: ''
})

const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const handleRegister = async () => {
  if (!formData.name || !formData.email || !formData.password || !formData.password_confirmation || !formData.role) {
    errorMessage.value = 'Všetky polia sú povinné.'
    return
  }

  if (formData.password !== formData.password_confirmation) {
    errorMessage.value = 'Heslá sa nezhodujú.'
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch('/api/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        name: formData.name,
        email: formData.email,
        password: formData.password,
        password_confirmation: formData.password_confirmation,
        role: formData.role,
      }),
    })

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}))
      const serverMessage = errorData.message || `HTTP error! status: ${response.status}`
      throw new Error(serverMessage)
    }

    const { token, user } = await response.json()
    
    // Store token and user data for immediate login
    localStorage.setItem('jwt_token', token)
    localStorage.setItem('user_role', user.role)
    
    successMessage.value = 'Registrácia bola úspešná! Automaticky ste prihlásený.'
    
    // Clear form
    Object.keys(formData).forEach(key => {
      formData[key] = ''
    })
    
    // Redirect to dashboard after 1 second
    setTimeout(() => {
      router.push('/dashboard')
    }, 1000)
    
  } catch (error) {
    errorMessage.value = error.message || 'Chyba pri registrácii.'
    console.error('Registration error:', error.message)
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.form-check-label {
  padding-top: 2px;
}
</style>
