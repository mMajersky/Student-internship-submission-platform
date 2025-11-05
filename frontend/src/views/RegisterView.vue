<template>
  <div class="container">
    <div class="row justify-content-center pt-5">
      <div class="col-md-8 col-lg-6">
        <div class="card p-4 shadow border text-start">
          <div class="card-body">
            <!-- HEADER -->
            <div class="text-center mb-4">
              <h3 class="card-title fw-bolder">
                {{ formData.role === 'company' ? 'Registrácia firmy' : 'Registrácia študenta' }}
              </h3>
              <p class="text-muted">Vyplňte všetky potrebné údaje pre registráciu.</p>
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

                <div v-if="formData.role === 'student'" class="col-md-6 mb-3">
                  <label for="surname" class="form-label">
                    Priezvisko <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="surname"
                    v-model="formData.surname"
                    required
                    placeholder="Vaše priezvisko"
                  />
                </div>

                <div v-if="formData.role === 'company'" class="col-md-6 mb-3">
                  <label for="company_name" class="form-label">
                    Názov firmy <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    class="form-control"
                    id="company_name"
                    v-model="formData.company_name"
                    required
                    placeholder="Názov spoločnosti"
                  />
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">
                  {{ formData.role === 'student' ? 'Univerzitný email' : 'Email' }} <span class="text-danger">*</span>
                </label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="formData.email"
                  required
                  :placeholder="formData.role === 'student' ? 'meno@student.ukf.sk' : 'meno@priklad.sk'"
                />
                <small v-if="formData.role === 'student'" class="form-text text-muted">
                  Použite váš univerzitný email (@student.ukf.sk)
                </small>
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

              <!-- Student-specific fields -->
              <template v-if="formData.role === 'student'">
                <div class="mb-3">
                  <label for="alternative_email" class="form-label">Alternatívny email</label>
                  <input
                    type="email"
                    class="form-control"
                    id="alternative_email"
                    v-model="formData.alternative_email"
                    placeholder="napr. osobny@mail.sk"
                  />
                  <small class="form-text text-muted">
                    Musí byť odlišný od univerzitného emailu
                  </small>
                </div>

                <div class="mb-3">
                  <label for="phone_number" class="form-label">Telefónne číslo</label>
                  <input
                    type="text"
                    class="form-control"
                    id="phone_number"
                    v-model="formData.phone_number"
                    placeholder="+421..."
                  />
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="study_level" class="form-label">
                      Stupeň štúdia <span class="text-danger">*</span>
                    </label>
                    <select
                      class="form-control"
                      id="study_level"
                      v-model="formData.study_level"
                      required
                    >
                      <option value="">-- Vyberte --</option>
                      <option value="Bc.">Bc. (Bakalárske)</option>
                      <option value="Mgr.">Mgr. (Magisterské)</option>
                      <option value="Ing.">Ing. (Inžinierske)</option>
                      <option value="PhD.">PhD. (Doktorandské)</option>
                    </select>
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="study_field" class="form-label">
                      Študijný odbor <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="study_field"
                      v-model="formData.study_field"
                      required
                      placeholder="napr. Informatika"
                    />
                  </div>
                </div>
              </template>

              <!-- Address fields (for students - required) -->
              <template v-if="formData.role === 'student'">
                <h5 class="mt-4 mb-3">Adresa</h5>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="state" class="form-label">
                      Štát <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="state"
                      v-model="formData.state"
                      required
                      placeholder="Slovensko"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="region" class="form-label">
                      Región <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="region"
                      v-model="formData.region"
                      required
                      placeholder="Nitriansky kraj"
                    />
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="city" class="form-label">
                      Mesto <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="city"
                      v-model="formData.city"
                      required
                      placeholder="Nitra"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="postal_code" class="form-label">
                      PSČ <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="postal_code"
                      v-model="formData.postal_code"
                      required
                      placeholder="94901"
                    />
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-8 mb-3">
                    <label for="street" class="form-label">
                      Ulica <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="street"
                      v-model="formData.street"
                      required
                      placeholder="Štefánikova"
                    />
                  </div>
                  <div class="col-md-4 mb-3">
                    <label for="house_number" class="form-label">
                      Číslo domu <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="house_number"
                      v-model="formData.house_number"
                      required
                      placeholder="12/A"
                    />
                  </div>
                </div>
              </template>

              <!-- Address fields (for companies - required) -->
              <template v-if="formData.role === 'company'">
                <h5 class="mt-4 mb-3">Adresa</h5>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="state" class="form-label">
                      Štát <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="state"
                      v-model="formData.state"
                      required
                      placeholder="Slovensko"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="region" class="form-label">
                      Región <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="region"
                      v-model="formData.region"
                      required
                      placeholder="Nitriansky kraj"
                    />
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="city" class="form-label">
                      Mesto <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="city"
                      v-model="formData.city"
                      required
                      placeholder="Nitra"
                    />
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="postal_code" class="form-label">
                      PSČ <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="postal_code"
                      v-model="formData.postal_code"
                      required
                      placeholder="94901"
                    />
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-8 mb-3">
                    <label for="street" class="form-label">
                      Ulica <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="street"
                      v-model="formData.street"
                      required
                      placeholder="Štefánikova"
                    />
                  </div>
                  <div class="col-md-4 mb-3">
                    <label for="house_number" class="form-label">
                      Číslo domu <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control"
                      id="house_number"
                      v-model="formData.house_number"
                      required
                      placeholder="12/A"
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
                  <span v-else>Registrovať</span>
                </button>
              </div>
            </form>

            <div class="text-center mt-4">
              <p class="text-muted small">
                Už máte účet?
                <router-link to="/login" class="text-primary text-decoration-none fw-medium">
                  Prihláste sa
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
import { useAuthStore } from '../stores/auth'

// Use relative path to leverage Vite proxy
const API_URL = ''

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
    errorMessage.value = 'Meno, email a heslo sú povinné.'
    return
  }

  if (formData.role === 'student') {
    if (!formData.surname) {
      errorMessage.value = 'Priezvisko je povinné.'
      return
    }
    if (!formData.study_level) {
      errorMessage.value = 'Stupeň štúdia je povinný.'
      return
    }
    if (!formData.study_field) {
      errorMessage.value = 'Študijný odbor je povinný.'
      return
    }
    if (!formData.state || !formData.city || !formData.postal_code || !formData.street || !formData.house_number) {
      errorMessage.value = 'Všetky polia adresy sú povinné.'
      return
    }
    // Validate university email
    const emailDomain = formData.email.split('@')[1]?.toLowerCase()
    if (!emailDomain || emailDomain !== 'student.ukf.sk') {
      errorMessage.value = 'Použite univerzitný email (@student.ukf.sk).'
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
        errorMessage.value = data?.message || 'Registrácia zlyhala.'
      }
      return
    }

    router.push('/login')
  } catch (error) {
    authStore.isLoading = false
    console.error('Fetch error:', error)
    errorMessage.value = 'Chyba pri registrácii.'
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
