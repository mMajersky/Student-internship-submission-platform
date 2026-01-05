<template>
  <div class="container mt-4">

    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>{{ $t('studentDashboard.title') }}</h2>
          <span class="badge bg-primary fs-6">{{ authStore.userRole }}</span>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-briefcase me-2"></i>
              {{ $t('studentDashboard.myInternships') }}
            </h5>
            <p class="card-text">{{ $t('studentDashboard.myInternshipsDesc') }}</p>
            <button @click="handleZobrazitPraxe" class="btn btn-primary">
              <i class="bi bi-eye me-2"></i>
              {{ $t('studentDashboard.viewInternships') }}
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-file-earmark-text me-2"></i>
              {{ $t('studentDashboard.reports') }}
            </h5>
            <p class="card-text">{{ $t('studentDashboard.reportsDesc') }}</p>
            <button class="btn btn-success" disabled>
              <i class="bi bi-plus me-2"></i>
              {{ $t('studentDashboard.newReport') }}
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-person-circle me-2"></i>
              {{ $t('studentDashboard.profile') }}
            </h5>
            <p class="card-text">{{ $t('studentDashboard.profileDesc') }}</p>
            <button @click="handleEditProfile" class="btn btn-primary">
              <i class="bi bi-gear me-2"></i>
              {{ $t('studentDashboard.editProfile') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="bi bi-info-circle me-2"></i>
              {{ $t('studentDashboard.roleInfo') }}
            </h5>
          </div>
          <div class="card-body">
            <p><strong>{{ $t('studentDashboard.role') }}:</strong> {{ authStore.userRole }}</p>
            <p><strong>{{ $t('studentDashboard.name') }}:</strong> {{ authStore.userDisplayName }}</p>
            <p><strong>{{ $t('studentDashboard.email') }}:</strong> {{ authStore.userEmail }}</p>
            <p><strong>{{ $t('studentDashboard.permissions') }}:</strong></p>
            <ul>
              <li v-for="permission in authStore.userPermissions" :key="permission">
                {{ permission }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

const authStore = useAuthStore()
const router = useRouter()
const { t } = useI18n()

const handleZobrazitPraxe = () => {
  router.push('/internships')
}

const handleEditProfile = () => {
  router.push('/settings')
}
</script>
