<template>
  <div class="container mt-4">

    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>{{ $t('adminPanel.title') }}</h2>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-people me-2"></i>
              {{ $t('adminPanel.users') }}
            </h5>
            <p class="card-text">{{ $t('adminPanel.usersDesc') }}</p>
            <button class="btn btn-primary" disabled>
              <i class="bi bi-gear me-2"></i>
              {{ $t('adminPanel.manage') }}
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-3 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-briefcase me-2"></i>
              {{ $t('adminPanel.internships') }}
            </h5>
            <p class="card-text">{{ $t('adminPanel.internshipsDesc') }}</p>
            <button class="btn btn-success" disabled>
              <i class="bi bi-eye me-2"></i>
              {{ $t('adminPanel.overview') }}
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-3 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-building me-2"></i>
              {{ $t('adminPanel.companies') }}
            </h5>
            <p class="card-text">{{ $t('adminPanel.companiesDesc') }}</p>
            <button class="btn btn-info" disabled>
              <i class="bi bi-list me-2"></i>
              {{ $t('adminPanel.list') }}
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-3 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-gear me-2"></i>
              {{ $t('adminPanel.systemSettings') }}
            </h5>
            <p class="card-text">{{ $t('adminPanel.systemSettingsDesc') }}</p>
            <button class="btn btn-warning" disabled>
              <i class="bi bi-sliders me-2"></i>
              {{ $t('adminPanel.configuration') }}
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
              {{ $t('adminPanel.roleInfo') }}
            </h5>
          </div>
          <div class="card-body">
            <p><strong>{{ $t('adminPanel.role') }}:</strong> {{ authStore.userRole }}</p>
            <p><strong>{{ $t('adminPanel.name') }}:</strong> {{ authStore.userDisplayName }}</p>
            <p><strong>{{ $t('adminPanel.email') }}:</strong> {{ authStore.userEmail }}</p>
            <p><strong>{{ $t('adminPanel.permissions') }}:</strong></p>
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
import { useI18n } from 'vue-i18n'

const authStore = useAuthStore()
const { t } = useI18n()
</script>

<style scoped>
.card {
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card:hover {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  transition: box-shadow 0.15s ease-in-out;
}
</style>
