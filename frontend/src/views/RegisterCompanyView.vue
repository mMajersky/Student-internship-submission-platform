<template>
  <div class="register-company-view">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <!-- Header -->
          <div class="text-center mb-5">
            <h1 class="mb-3">
              <i class="bi bi-building text-primary"></i>
              Registrácia firmy
            </h1>
            <p class="text-muted">
              Zaregistrujte svoju firmu do systému odbornej praxe. 
              Vaša žiadosť bude skontrolovaná garantom pred schválením.
            </p>
          </div>

          <!-- Success State -->
          <div v-if="showSuccessState" class="card border-success">
            <div class="card-body text-center py-5">
              <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
              <h3 class="mt-4 mb-3">Žiadosť bola úspešne odoslaná!</h3>
              <p class="text-muted mb-4">
                Vaša žiadosť o registráciu firmy bola odoslaná garantovi na schválenie. 
                Po schválení vás budeme kontaktovať na uvedený email.
              </p>
              <div class="d-flex gap-2 justify-content-center">
                <button class="btn btn-primary" @click="resetSuccessState">
                  <i class="bi bi-plus-circle me-2"></i>
                  Registrovať ďalšiu firmu
                </button>
                <button class="btn btn-outline-secondary" @click="goToHome">
                  <i class="bi bi-house me-2"></i>
                  Späť na domovskú stránku
                </button>
              </div>
            </div>
          </div>

          <!-- Registration Form -->
          <div v-else>
            <CompanyRegistrationForm
              api-endpoint="/api/company-requests/public"
              submit-button-text="Registrovať firmu"
              @success="handleSuccess"
            />
            
            <div class="text-center mt-4">
              <a href="/" class="text-decoration-none">
                <i class="bi bi-arrow-left me-2"></i>
                Späť na domovskú stránku
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import CompanyRegistrationForm from '@/components/company/CompanyRegistrationForm.vue'

const router = useRouter()

const showSuccessState = ref(false)

const handleSuccess = () => {
  showSuccessState.value = true
  // Scroll to top
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const resetSuccessState = () => {
  showSuccessState.value = false
}

const goToHome = () => {
  router.push('/')
}
</script>

<style scoped>
.register-company-view {
  min-height: 100vh;
  background-color: #f8f9fa;
}
</style>
