<template>
  <div class="container mt-4">

    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Dashboard firmy</h2>
          <span class="badge bg-success fs-6">{{ authStore.userRole }}</span>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-briefcase me-2"></i>
              Praxe
            </h5>
            <p class="card-text">Správa ponúkaných praxí</p>
            <button class="btn btn-primary" disabled>
              <i class="bi bi-plus me-2"></i>
              Pridať prax
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-people me-2"></i>
              Aplikácie
            </h5>
            <p class="card-text">Prehľad aplikácií študentov</p>
            <button class="btn btn-info" disabled>
              <i class="bi bi-eye me-2"></i>
              Zobraziť aplikácie
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-building me-2"></i>
              Profil firmy
            </h5>
            <p class="card-text">Správa profilu firmy</p>
            <button class="btn btn-outline-primary" disabled>
              <i class="bi bi-gear me-2"></i>
              Upraviť profil
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
              Informácie o vašej roli
            </h5>
          </div>
          <div class="card-body">
            <p><strong>Rola:</strong> {{ authStore.userRole }}</strong></p>
            <p><strong>Meno:</strong> {{ authStore.userDisplayName }}</p>
            <p><strong>Email:</strong> {{ authStore.userEmail }}</p>
            <p><strong>Oprávnenia:</strong></p>
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

const authStore = useAuthStore()
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
