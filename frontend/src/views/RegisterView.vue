<template>
  <div class="container">
    <div class="row justify-content-center pt-5">
      <div class="col-md-6 col-lg-4">
        <div class="card p-4 shadow border text-start">
          <div class="card-body">
            <!-- HEADER -->
            <div class="text-center mb-4">
              <h3 class="card-title fw-bolder">Registrácia</h3>
              <p class="text-muted">Vytvorte si nový účet.</p>
            </div>

            <!-- ERROR -->
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>

            <!-- FORM -->
            <form @submit.prevent="handleRegister" novalidate>
              <div class="mb-3">
                <label for="name" class="form-label">
                  Meno <span class="text-danger">*</span>
                </label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  v-model="formData.name"
                  required
                  placeholder="Vaše meno"
                />
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">
                  Email <span class="text-danger">*</span>
                </label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="formData.email"
                  required
                  placeholder="meno@priklad.sk"
                />
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">
                  Heslo <span class="text-danger">*</span>
                </label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="formData.password"
                  required
                  placeholder="********"
                />
              </div>

              <div class="mb-3">
                <label for="password_confirmation" class="form-label">
                  Potvrdenie hesla <span class="text-danger">*</span>
                </label>
                <input
                  type="password"
                  class="form-control"
                  id="password_confirmation"
                  v-model="formData.password_confirmation"
                  required
                  placeholder="********"
                />
              </div>

              <div class="mb-3">
                <label for="role" class="form-label">
                  Typ účtu <span class="text-danger">*</span>
                </label>
                <select
                  class="form-select"
                  id="role"
                  v-model="formData.role"
                  required
                >
                  <option value="">Vyberte typ účtu</option>
                  <option value="student">Študent</option>
                  <option value="company">Firma</option>
                </select>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary" :disabled="isLoading">
                  <span
                    v-if="isLoading"
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  <span v-else>Registrovať sa</span>
                </button>
              </div>
            </form>

            <div class="text-center mt-4">
              <p class="text-muted small">
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
  role: '',
})

const isLoading = ref(false)
const errorMessage = ref('')

const handleRegister = async () => {
  if (!formData.name || !formData.email || !formData.password || !formData.role) {
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
    localStorage.setItem('jwt_token', token)
    localStorage.setItem('user_role', user.role)
    router.push('/dashboard')
  } catch (error) {
    errorMessage.value = 'Registrácia zlyhala. Skúste to znova.'

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
