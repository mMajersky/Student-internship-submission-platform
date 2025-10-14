<script setup>
// Footer component
const currentYear = new Date().getFullYear()
</script>

<template>
  <footer class="bg-dark text-light py-5">
    <div class="container px-4">
      <div class="row g-4 mb-4">
        <!-- Company Info -->
        <div class="col-md-4">
          <div class="d-flex align-items-center mb-3">
            <i class="bi bi-mortarboard fs-3 text-primary me-2"></i>
            <span class="fs-4 fw-bold text-white">Odborná prax</span>
          </div>
          <p class="small text-muted">Evidencia a správa odbornej praxe pre študentov a firmy.</p>
        </div>

        <!-- Links -->
        <div class="col-md-4">
          <h5 class="text-white fw-semibold mb-3">Odkazy</h5>
          <ul class="list-unstyled small">
            <li class="mb-2">
              <router-link to="/login" class="text-light text-decoration-none">Prihlásenie</router-link>
            </li>
            <li class="mb-2">
              <a href="#" class="text-light text-decoration-none">Registrácia študenta</a>
            </li>
            <li class="mb-2">
              <a href="#" class="text-light text-decoration-none">Registrácia firmy</a>
            </li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="col-md-4">
          <h5 class="text-white fw-semibold mb-3">Kontakt</h5>
          <ul class="list-unstyled small text-muted">
            <li class="mb-2">Email: info@example.com</li>
            <li class="mb-2">Telefón: +421 123 456 789</li>
          </ul>
        </div>
      </div>

      <!-- Copyright -->
      <div class="border-top border-secondary pt-3 text-center small text-muted">
        <p class="mb-0">&copy; {{ currentYear }} Odborná prax CRM</p>
      </div>
    </div>
  </footer>
</template>

