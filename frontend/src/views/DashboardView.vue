<template>
  <div class="dashboard">
    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h3 mb-1">Dashboard</h1>
          <p class="text-muted mb-0">Vitajte, {{ userRole }}</p>
        </div>
        <button @click="logout" class="btn btn-outline-danger">Odhlásiť sa</button>
      </div>

      <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link" :class="{ active: activeTab === 'overview' }" @click="activeTab = 'overview'" type="button">
            <i class="bi bi-house me-2"></i>
            Prehľad
          </button>
        </li>
        <li class="nav-item" role="presentation" v-if="canManageAnnouncements">
          <button class="nav-link" :class="{ active: activeTab === 'announcements' }" @click="activeTab = 'announcements'" type="button">
            <i class="bi bi-megaphone me-2"></i>
            Správa oznámení
          </button>
        </li>
      </ul>

      <div class="tab-content">
        <div v-if="activeTab === 'overview'" class="tab-pane fade show active">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Vitajte v systéme</h5>
              <p class="card-text">Úspešne ste sa prihlásili do systému správy odbornej praxe.</p>
              <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Vaša rola: <strong>{{ userRole }}</strong></div>
            </div>
          </div>
        </div>

        <div v-if="activeTab === 'announcements'" class="tab-pane fade show active">
          <ManageAnnouncements />
        </div>
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

const canManageAnnouncements = computed(() => userRole.value === 'admin' || userRole.value === 'garant')

const logout = () => {
  localStorage.removeItem('jwt_token')
  localStorage.removeItem('user_role')
  router.push('/login')
}
</script>
