<template>
  <div class="dashboard">
    <!-- Dashboard Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Vitajte, {{ userRole }}</p>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button 
          class="nav-link" 
          :class="{ active: activeTab === 'overview' }"
          @click="activeTab = 'overview'"
          type="button"
        >
          <i class="bi bi-house me-2"></i>
          Prehľad
        </button>
      </li>
      <li class="nav-item" role="presentation" v-if="canManageAnnouncements">
        <button 
          class="nav-link" 
          :class="{ active: activeTab === 'announcements' }"
          @click="activeTab = 'announcements'"
          type="button"
        >
          <i class="bi bi-megaphone me-2"></i>
          Správa oznámení
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
      <!-- Overview Tab -->
      <div v-if="activeTab === 'overview'" class="tab-pane fade show active">
        <div class="row">
          <div class="col-md-8">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Vitajte v systéme</h5>
                <p class="card-text">
                  Úspešne ste sa prihlásili do systému správy odbornej praxe.
                </p>
                <div class="alert alert-info">
                  <i class="bi bi-info-circle me-2"></i>
                  Vaša rola: <strong>{{ userRole }}</strong>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-body text-center">
                <i class="bi bi-person-circle fs-1 text-primary mb-3"></i>
                <h6>Profil používateľa</h6>
                <p class="text-muted small">Správa vášho účtu</p>
                <button class="btn btn-outline-primary btn-sm" disabled>
                  <i class="bi bi-gear me-1"></i>
                  Nastavenia
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Announcements Tab -->
      <div v-if="activeTab === 'announcements'" class="tab-pane fade show active">
        <ManageAnnouncements />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import ManageAnnouncements from '@/components/admin/ManageAnnouncements.vue'

const router = useRouter()

const userRole = ref(localStorage.getItem('user_role') || 'Not Found')
const activeTab = ref('overview')

const canManageAnnouncements = computed(() => {
  return userRole.value === 'admin' || userRole.value === 'garant'
})

// Logout is now handled in App.vue navbar
</script>
