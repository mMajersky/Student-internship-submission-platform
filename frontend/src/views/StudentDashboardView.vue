<template>
  <div class="container mt-4">

    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Dashboard študenta</h2>
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
              Moje praxe
            </h5>
            <p class="card-text">Prehľad vašich praxí a aplikácií</p>
            <button class="btn btn-primary" disabled>
              <i class="bi bi-eye me-2"></i>
              Zobraziť praxe
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-file-earmark-text me-2"></i>
              Správy
            </h5>
            <p class="card-text">Odosielanie a správa správ z praxe</p>
            <button class="btn btn-success" disabled>
              <i class="bi bi-plus me-2"></i>
              Nová správa
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-person-circle me-2"></i>
              Profil
            </h5>
            <p class="card-text">Správa vášho profilu a údajov</p>
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
            <p><strong>Rola:</strong> {{ authStore.userRole }}</p>
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
