<template>
  <div class="dashboard">
    <div class="container py-4">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h3 mb-1">{{ $t('dashboard.title') }}</h1>
          <p class="text-muted mb-0">{{ $t('dashboard.welcome') }}, {{ authStore.userDisplayName }} ({{ authStore.userRole }})</p>
        </div>
        <button @click="logout" class="btn btn-outline-danger">{{ $t('dashboard.logout') }}</button>
      </div>

      <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link" :class="{ active: activeTab === 'overview' }" @click="activeTab = 'overview'" type="button">
            <i class="bi bi-house me-2"></i>
            {{ $t('dashboard.tabs.overview') }}
          </button>
        </li>
        <li class="nav-item" role="presentation" v-if="canManageAnnouncements">
          <button class="nav-link" :class="{ active: activeTab === 'edit-announcement' }" @click="activeTab = 'edit-announcement'" type="button">
            <i class="bi bi-pencil-square me-2"></i>
            {{ $t('dashboard.tabs.editAnnouncement') }}
          </button>
        </li>
        <li class="nav-item" role="presentation" v-if="canManageAnnouncements">
          <button class="nav-link" :class="{ active: activeTab === 'manage-garants' }" @click="activeTab = 'manage-garants'" type="button">
            <i class="bi bi-people me-2"></i>
            {{ $t('dashboard.tabs.manageGarants') }}
          </button>
        </li>
      </ul>

      <div class="tab-content">
        <div v-if="activeTab === 'overview'" class="tab-pane fade show active">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">{{ $t('dashboard.welcomeMessage') }}</h5>
              <p class="card-text">{{ $t('dashboard.systemDescription') }}</p>
              <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>{{ $t('dashboard.yourRole') }}: <strong>{{ authStore.userRole }}</strong></div>
            </div>
          </div>
        </div>

        <div v-if="activeTab === 'edit-announcement'" class="tab-pane fade show active">
          <EditAnnouncement />
        </div>

        <div v-if="activeTab === 'manage-garants'" class="tab-pane fade show active">
          <ManageGarants />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../stores/auth'
import EditAnnouncement from '@/components/admin/EditAnnouncement.vue'
import ManageGarants from '@/components/garant/ManageGarants.vue'

const router = useRouter()
const authStore = useAuthStore()
const { t } = useI18n()

const activeTab = ref('overview')

const canManageAnnouncements = computed(() => authStore.canManageAnnouncements)

const logout = () => {
  authStore.logout()
  router.push('/login')
}
</script>
