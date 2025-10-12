<template>
  <div class="container">
    <div class="row justify-content-center pt-5">
      <div class="col-md-6 col-lg-4">
        <div class="card p-4 shadow border text-start">
          <div class="card-body">
            <!-- HEADER -->
            <div class="text-center mb-4">
              <h3 class="card-title fw-bolder">Prihlásenie</h3>
              <p class="text-muted">Použite email a heslo.</p>
            </div>

            <!-- ERROR -->
            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>

            <!-- FORM -->
            <form @submit.prevent="handleLogin" novalidate>
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

              <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    v-model="formData.remember"
                    id="rememberMe"
                  />
                  <label class="form-check-label" for="rememberMe">Zapamätať</label>
                </div>
                <a href="#" class="small">Zabudli ste heslo?</a>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary" :disabled="isLoading">
                  <span
                    v-if="isLoading"
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"
                  ></span>
                  <span v-else>Prihlásiť sa</span>
                </button>
              </div>
            </form>

            <div class="text-center mt-4">
              <p class="text-muted small">
                Nemáte účet?

                <a href="#">Registrácia študenta</a> alebo

                <a href="#">registrácia firmy</a>.
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
  email: '',
  password: '',
  remember: false,
})

const isLoading = ref(false)
const errorMessage = ref('')

const apiBaseUrl = import.meta.env.VITE_API_BASE_URL

const handleLogin = async () => {
  if (!formData.email || !formData.password) {
    errorMessage.value = 'Email a heslo sú povinné polia.'
    return
  }

  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch(`${apiBaseUrl}/api/auth/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        email: formData.email,
        password: formData.password,
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
    errorMessage.value = 'Neplatný email alebo heslo.'

    console.error('Login error:', error.message)
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
