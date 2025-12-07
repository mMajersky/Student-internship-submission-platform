<!-- src/App.vue -->
<template>
  <div id="app-wrapper">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
      <div class="container">
        <router-link to="/" class="navbar-brand d-flex align-items-center text-decoration-none">
          <i class="bi bi-mortarboard me-2 fs-4 text-primary"></i>
          <span class="fs-5 fw-bold">{{ $t('nav.brand') }}</span>
        </router-link>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <router-link to="/" class="nav-link" :class="{ active: $route.path === '/' }">{{ $t('nav.home') }}</router-link>
            </li>
            <li class="nav-item" v-if="authStore.isAuthenticated && authStore.canManageAnnouncements">
              <router-link to="/dashboard" class="nav-link" :class="{ active: $route.path === '/dashboard' }">{{ $t('nav.dashboard') }}</router-link>
            </li>
            <li class="nav-item" v-if="authStore.isAuthenticated && authStore.isGarant">
              <router-link to="/garant-dashboard" class="nav-link" :class="{ active: $route.path === '/garant-dashboard' }">
                {{ $t('nav.management') }}
              </router-link>
            </li>
          </ul>

          <ul class="navbar-nav">
            <li class="nav-item" v-if="!authStore.isAuthenticated">
              <router-link to="/login" class="nav-link">{{ $t('nav.login') }}</router-link>
            </li>

            <!-- Language Switcher -->
            <li class="nav-item">
              <button
                @click="switchLanguage"
                class="btn btn-outline-secondary btn-sm me-2"
                :title="$t('common.language')"
              >
                <i :class="currentLanguageIcon"></i>
                {{ currentLanguageFlag }}
              </button>
            </li>

            <!-- Notification Bell (only for authenticated users) -->
            <li class="nav-item" v-if="authStore.isAuthenticated">
              <NotificationBell />
            </li>
            
            <li class="nav-item dropdown" v-if="authStore.isAuthenticated">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i>
                {{ authStore.userRole }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <router-link :to="dashboardRoute" class="dropdown-item">
                    <i class="bi bi-speedometer2 me-2"></i>
                    {{ $t('nav.dashboard') }}
                  </router-link>
                </li>
                <li>
                  <router-link to="/settings" class="dropdown-item">
                    <i class="bi bi-gear me-2"></i>
                    {{ $t('nav.settings') }}
                  </router-link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <button @click="logout" class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    {{ $t('nav.logout') }}
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
import { onMounted, onBeforeUnmount, watch, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from './stores/auth'
import NotificationBell from './components/NotificationBell.vue'

const router = useRouter()
const authStore = useAuthStore()
const { t, locale } = useI18n()

// Computed properties for language switcher
const currentLanguageFlag = computed(() => locale.value === 'sk' ? 'SK' : 'EN')
const currentLanguageIcon = computed(() => locale.value === 'sk' ? 'bi bi-flag' : 'bi bi-flag-fill')

// Methods
const logout = () => {
  authStore.logout()
  router.push('/')
}

const switchLanguage = () => {
  const newLocale = locale.value === 'sk' ? 'en' : 'sk'
  locale.value = newLocale
  localStorage.setItem('language', newLocale)
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
// Computed property for dashboard route based on user role
const dashboardRoute = computed(() => {
  if (authStore.isAdmin || authStore.isGarant) return '/dashboard'
  if (authStore.isStudent) return '/student-dashboard'
  if (authStore.isCompany) return '/company-dashboard'
  return '/'
})

</script>
