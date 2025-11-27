<template>
  <div class="container mt-4">

    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>{{ $t('companyDashboard.title') }}</h2>
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
              {{ $t('companyDashboard.internships') }}
            </h5>
            <p class="card-text">{{ $t('companyDashboard.internshipsDesc') }}</p>
            <div class="d-flex gap-2">
              <button class="btn btn-primary" disabled>
                <i class="bi bi-plus me-2"></i>
                {{ $t('companyDashboard.addInternship') }}
              </button>
              <button class="btn btn-info" @click="showInternships = !showInternships">
                <i class="bi bi-eye me-2"></i>
                {{ $t('companyDashboard.viewInternships') }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-people me-2"></i>
              {{ $t('companyDashboard.applications') }}
            </h5>
            <p class="card-text">{{ $t('companyDashboard.applicationsDesc') }}</p>
            <button class="btn btn-info" disabled>
              <i class="bi bi-eye me-2"></i>
              {{ $t('companyDashboard.viewApplications') }}
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-building me-2"></i>
              {{ $t('companyDashboard.companyProfile') }}
            </h5>
            <p class="card-text">{{ $t('companyDashboard.companyProfileDesc') }}</p>
            <button class="btn btn-outline-primary" disabled>
              <i class="bi bi-gear me-2"></i>
              {{ $t('companyDashboard.editProfile') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Internships List Section -->
    <div v-if="showInternships" class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bi bi-briefcase me-2"></i>
              {{ $t('companyDashboard.internshipsList') }}
            </h5>
            <button class="btn btn-sm btn-outline-secondary" @click="showInternships = false">
              <i class="bi bi-x"></i>
            </button>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ $t('common.loading') }}</span>
              </div>
            </div>

            <div v-else-if="internships.length === 0" class="text-center py-5">
              <i class="bi bi-inbox fs-1 text-muted"></i>
              <p class="text-muted mt-3">{{ $t('companyDashboard.noInternships') }}</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>{{ $t('companyDashboard.tableHeaders.student') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.year') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.start') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.end') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.status') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.documents') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="internship in internships" :key="internship.id">
                    <td>{{ getStudentFullName(internship) }}</td>
                    <td>{{ internship.academy_year || '-' }}</td>
                    <td>{{ formatDate(internship.start_date) }}</td>
                    <td>{{ formatDate(internship.end_date) }}</td>
                    <td>
                      <span class="badge" :class="getStatusClass(internship.status)">
                        {{ getTranslatedStatus(internship.status) }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-info">{{ internship.documents_count || 0 }}</span>
                    </td>
                    <td>
                      <button 
                        class="btn btn-sm btn-outline-primary" 
                        :title="$t('companyDashboard.viewDocumentsBtn')" 
                        @click="openDocuments(internship)"
                      >
                        <i class="bi bi-folder-open me-1"></i>
                        {{ $t('companyDashboard.viewDocumentsBtn') }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">
              <i class="bi bi-info-circle me-2"></i>
              {{ $t('companyDashboard.roleInfo') }}
            </h5>
          </div>
          <div class="card-body">
            <p><strong>{{ $t('companyDashboard.role') }}:</strong> {{ authStore.userRole }}</p>
            <p><strong>{{ $t('companyDashboard.name') }}:</strong> {{ authStore.userDisplayName }}</p>
            <p><strong>{{ $t('companyDashboard.email') }}:</strong> {{ authStore.userEmail }}</p>
            <p><strong>{{ $t('companyDashboard.permissions') }}:</strong></p>
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useI18n } from 'vue-i18n'

const authStore = useAuthStore()
const router = useRouter()
const { t } = useI18n()

const showInternships = ref(false)
const loading = ref(false)
const internships = ref([])

const fetchInternships = async () => {
  loading.value = true
  try {
    const response = await fetch('/api/company/internships', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    })

    if (response.ok) {
      const data = await response.json()
      internships.value = data.data || data
    } else {
      console.error('Failed to fetch internships:', response.status)
    }
  } catch (error) {
    console.error('Error fetching internships:', error)
  } finally {
    loading.value = false
  }
}

const getStudentFullName = (internship) => {
  if (internship.student) {
    return `${internship.student.name} ${internship.student.surname}`
  }
  return '-'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('sk-SK')
}

const getStatusClass = (status) => {
  const statusClasses = {
    'vytvorená': 'bg-secondary',
    'potvrdená': 'bg-info',
    'schválená': 'bg-success',
    'obhájená': 'bg-primary',
    'neobhájená': 'bg-danger',
    'zamietnutá': 'bg-danger',
  }
  return statusClasses[status] || 'bg-secondary'
}

const getTranslatedStatus = (status) => {
  const statusMap = {
    'vytvorená': 'studentInternship.status.vytvorena',
    'potvrdená': 'studentInternship.status.potvrdena',
    'schválená': 'studentInternship.status.schvalena',
    'obhájená': 'studentInternship.status.obhajena',
    'neobhájená': 'studentInternship.status.neobhajena',
    'zamietnutá': 'studentInternship.status.zamietnuta',
  }
  const translationKey = statusMap[status] || 'studentInternship.status.vytvorena'
  return t(translationKey)
}

const openDocuments = (internship) => {
  router.push({ name: 'company-internship-documents', params: { id: internship.id } })
}

onMounted(() => {
  fetchInternships()
})
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
