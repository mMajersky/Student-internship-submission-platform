<template>
  <footer class="bg-light border-top py-4 mt-5">
    <div class="container px-4">
      <div class="row align-items-center">
        <div class="col-md-6">
          <p class="text-muted mb-0">© 2025 Odborná prax CRM</p>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="d-flex flex-wrap justify-content-md-end gap-3">
            <router-link to="/login" class="text-decoration-none text-muted">
              Prihlásenie
            </router-link>
            <router-link :to="{ name: 'register', state: { role: 'student' } }" class="text-decoration-none text-muted">
              Registrácia študenta
            </router-link>
            <router-link :to="{ name: 'register', state: { role: 'company' } }" class="text-decoration-none text-muted">
              Registrácia firmy
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
// No additional logic needed for this static component
</script>
