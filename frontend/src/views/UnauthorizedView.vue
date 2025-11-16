<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body text-center">
            <div class="mb-4">
              <i class="bi bi-shield-exclamation text-danger" style="font-size: 4rem;"></i>
            </div>
            <h2 class="card-title text-danger mb-3">{{ $t('unauthorized.title') }}</h2>
            <p class="card-text text-muted mb-4">
              {{ $t('unauthorized.message') }}
              {{ $t('unauthorized.roleMessage') }}
            </p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
              <router-link to="/" class="btn btn-primary">
                <i class="bi bi-house me-2"></i>
                {{ $t('unauthorized.backHome') }}
              </router-link>
              <button @click="logout" class="btn btn-outline-secondary">
                <i class="bi bi-box-arrow-right me-2"></i>
                {{ $t('unauthorized.logout') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useI18n } from 'vue-i18n'

const router = useRouter()
const authStore = useAuthStore()
const { t } = useI18n()

const logout = () => {
  authStore.logout()
  router.push('/')
}
</script>

<style scoped>
.card {
  border: none;
}

.card-body {
  padding: 3rem 2rem;
}
</style>
