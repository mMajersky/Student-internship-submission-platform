<!-- src/App.vue -->
<template>
  <div id="app-wrapper">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
      <div class="container">
        <router-link to="/" class="navbar-brand d-flex align-items-center text-decoration-none">
          <i class="bi bi-mortarboard me-2 fs-4 text-primary"></i>
          <span class="fs-5 fw-bold">Odborná prax</span>
        </router-link>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link to="/" class="nav-link" :class="{ active: $route.path === '/' }">Domov</router-link>
            </li>
            <li class="nav-item" v-if="authStore.isAuthenticated && authStore.canManageAnnouncements">
              <router-link to="/dashboard" class="nav-link" :class="{ active: $route.path === '/dashboard' }">Dashboard</router-link>
            </li>
          </ul>

          <ul class="navbar-nav">
            <li class="nav-item" v-if="!authStore.isAuthenticated">
              <router-link to="/login" class="nav-link">Prihlásiť sa</router-link>
            </li>
            <li class="nav-item dropdown" v-if="authStore.isAuthenticated">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i>
                {{ authStore.userRole }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <router-link to="/dashboard" class="dropdown-item">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                  </router-link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <button @click="logout" class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Odhlásiť sa
                  </button>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Methods
const logout = () => {
  authStore.logout()
  router.push('/')
}

// Lifecycle
onMounted(() => {
  authStore.initializeAuth()
  
  // Listen for storage changes (login/logout from other tabs)
  window.addEventListener('storage', () => {
    authStore.initializeAuth()
  })
})

onBeforeUnmount(() => {
  window.removeEventListener('storage', authStore.initializeAuth)
})

// Watch for route changes to update auth status
watch(() => router.currentRoute.value, () => {
  authStore.initializeAuth()
})
</script>

<style>
#app-wrapper {
  background-color: #f8f9fa;
  min-height: 100vh;
}
</style>
