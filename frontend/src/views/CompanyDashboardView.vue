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

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" role="tablist">
      <li class="nav-item" role="presentation">
        <button
          class="nav-link"
          :class="{ active: activeTab === 'overview' }"
          @click="activeTab = 'overview'"
          type="button"
        >
          <i class="bi bi-house me-2"></i>
          {{ $t('companyDashboard.tabs.overview') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button
          class="nav-link"
          :class="{ active: activeTab === 'internships' }"
          @click="activeTab = 'internships'"
          type="button"
        >
          <i class="bi bi-briefcase me-2"></i>
          {{ $t('companyDashboard.tabs.internships') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button
          class="nav-link"
          :class="{ active: activeTab === 'documents' }"
          @click="activeTab = 'documents'"
          type="button"
        >
          <i class="bi bi-folder me-2"></i>
          {{ $t('companyDashboard.tabs.documents') }}
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
      <!-- Overview Tab -->
      <div v-if="activeTab === 'overview'" class="tab-pane fade show active">
        <div class="row">
          <div class="col-md-4 mb-4">
            <div class="card h-100 border-info">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-briefcase me-2 text-info"></i>
                  {{ $t('companyDashboard.cards.manageInternships') }}
                </h5>
                <p class="card-text">{{ $t('companyDashboard.cards.manageInternshipsDesc') }}</p>
                <button class="btn btn-info" @click="activeTab = 'internships'">
                  <i class="bi bi-eye me-2"></i>
                  {{ $t('companyDashboard.cards.viewInternships') }}
                </button>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-file-earmark-pdf me-2 text-success"></i>
                  {{ $t('companyDashboard.cards.documents') }}
                </h5>
                <p class="card-text">{{ $t('companyDashboard.cards.documentsDesc') }}</p>
                <button class="btn btn-success" @click="activeTab = 'documents'">
                  <i class="bi bi-folder me-2"></i>
                  {{ $t('companyDashboard.cards.viewDocuments') }}
                </button>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card h-100 border-warning">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-people me-2 text-warning"></i>
                  {{ $t('companyDashboard.cards.applications') }}
                </h5>
                <p class="card-text">{{ $t('companyDashboard.cards.applicationsDesc') }}</p>
                <button class="btn btn-warning" disabled>
                  <i class="bi bi-eye me-2"></i>
                  {{ $t('companyDashboard.cards.viewApplications') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- All Internships Tab -->
      <div v-if="activeTab === 'internships'" class="tab-pane fade show active">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="card-title mb-0">{{ $t('companyDashboard.internshipsList') }}</h5>
              <button 
                v-if="hasActiveFiltersInternships" 
                class="btn btn-sm btn-outline-secondary"
                @click="clearFiltersInternships"
              >
                <i class="bi bi-x-circle me-1"></i>
                {{ $t('companyDashboard.filters.clearAll') }}
              </button>
            </div>

            <!-- Search and Filter Controls -->
            <div v-if="internships.length > 0" class="mb-4">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">{{ $t('companyDashboard.filters.searchStudent') }}</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    v-model="searchStudent"
                    :placeholder="$t('companyDashboard.filters.searchStudentPlaceholder')"
                  >
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ $t('companyDashboard.filters.status') }}</label>
                  <select class="form-select" v-model="selectedStatus">
                    <option value="">{{ $t('companyDashboard.filters.allStatuses') }}</option>
                    <option value="created">{{ $t('studentInternship.status.created') }}</option>
                    <option value="approved by garant">{{ $t('studentInternship.status.approvedByGarant') }}</option>
                    <option value="rejected by garant">{{ $t('studentInternship.status.rejectedByGarant') }}</option>
                    <option value="confirmed by company">{{ $t('studentInternship.status.confirmedByCompany') }}</option>
                    <option value="not confirmed by company">{{ $t('studentInternship.status.notConfirmedByCompany') }}</option>
                    <option value="defended by student">{{ $t('studentInternship.status.defendedByStudent') }}</option>
                    <option value="not defended by student">{{ $t('studentInternship.status.notDefendedByStudent') }}</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Results count -->
            <div v-if="internships.length > 0 && hasActiveFiltersInternships" class="mb-3">
              <span class="text-muted">
                {{ $t('companyDashboard.filters.showing', { count: filteredInternshipsTab.length, total: internships.length }) }}
              </span>
            </div>

            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ $t('common.loading') }}</span>
              </div>
            </div>

            <div v-else-if="internships.length === 0" class="text-center py-5">
              <i class="bi bi-inbox fs-1 text-muted"></i>
              <p class="text-muted mt-3">{{ $t('companyDashboard.noInternships') }}</p>
            </div>

            <div v-else-if="filteredInternshipsTab.length === 0" class="text-center py-5">
              <i class="bi bi-funnel fs-1 text-muted"></i>
              <p class="text-muted mt-3">{{ $t('companyDashboard.filters.noResults') }}</p>
              <button class="btn btn-outline-secondary" @click="clearFiltersInternships">
                <i class="bi bi-x-circle me-1"></i>
                {{ $t('companyDashboard.filters.clearAll') }}
              </button>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>{{ $t('companyDashboard.tableHeaders.student') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.year') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.semester') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.start') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.end') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.status') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="internship in filteredInternshipsTab" :key="internship.id">
                    <td>{{ getStudentFullName(internship) }}</td>
                    <td>{{ getYear(internship.start_date) }}</td>
                    <td><span class="badge bg-secondary">{{ getSemester(internship.start_date) }}</span></td>
                    <td>{{ formatDate(internship.start_date) }}</td>
                    <td>{{ formatDate(internship.end_date) }}</td>
                    <td>
                      <span class="badge" :class="getStatusClass(internship.status)">
                        {{ getTranslatedStatus(internship.status) }}
                      </span>
                    </td>
                    <td>
                      <button 
                        class="btn btn-sm btn-outline-secondary me-1" 
                        :title="$t('companyDashboard.actions.viewDocuments')" 
                        @click="openDocuments(internship)"
                      >
                        <i class="bi bi-folder"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Documents Tab -->
      <div v-if="activeTab === 'documents'" class="tab-pane fade show active">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="card-title mb-0">{{ $t('companyDashboard.documentsTitle') }}</h5>
              <button 
                v-if="hasActiveFiltersDocuments" 
                class="btn btn-sm btn-outline-secondary"
                @click="clearFiltersDocuments"
              >
                <i class="bi bi-x-circle me-1"></i>
                {{ $t('companyDashboard.filters.clearAll') }}
              </button>
            </div>

            <!-- Search and Filter Controls -->
            <div v-if="internships.length > 0" class="mb-4">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">{{ $t('companyDashboard.filters.searchStudent') }}</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    v-model="searchStudentDocs"
                    :placeholder="$t('companyDashboard.filters.searchStudentPlaceholder')"
                  >
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ $t('companyDashboard.filters.status') }}</label>
                  <select class="form-select" v-model="selectedStatusDocs">
                    <option value="">{{ $t('companyDashboard.filters.allStatuses') }}</option>
                    <option value="created">{{ $t('studentInternship.status.created') }}</option>
                    <option value="approved by garant">{{ $t('studentInternship.status.approvedByGarant') }}</option>
                    <option value="rejected by garant">{{ $t('studentInternship.status.rejectedByGarant') }}</option>
                    <option value="confirmed by company">{{ $t('studentInternship.status.confirmedByCompany') }}</option>
                    <option value="not confirmed by company">{{ $t('studentInternship.status.notConfirmedByCompany') }}</option>
                    <option value="defended by student">{{ $t('studentInternship.status.defendedByStudent') }}</option>
                    <option value="not defended by student">{{ $t('studentInternship.status.notDefendedByStudent') }}</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Results count -->
            <div v-if="internships.length > 0 && hasActiveFiltersDocuments" class="mb-3">
              <span class="text-muted">
                {{ $t('companyDashboard.filters.showing', { count: filteredInternshipsDocs.length, total: internships.length }) }}
              </span>
            </div>

            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ $t('common.loading') }}</span>
              </div>
            </div>

            <div v-if="internships.length === 0" class="text-center py-5">
              <i class="bi bi-inbox fs-1 text-muted"></i>
              <p class="text-muted mt-3">{{ $t('companyDashboard.noInternships') }}</p>
            </div>

            <div v-else-if="filteredInternshipsDocs.length === 0" class="text-center py-5">
              <i class="bi bi-funnel fs-1 text-muted"></i>
              <p class="text-muted mt-3">{{ $t('companyDashboard.filters.noResults') }}</p>
              <button class="btn btn-outline-secondary" @click="clearFiltersDocuments">
                <i class="bi bi-x-circle me-1"></i>
                {{ $t('companyDashboard.filters.clearAll') }}
              </button>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>{{ $t('companyDashboard.tableHeaders.student') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.year') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.semester') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.status') }}</th>
                    <th>{{ $t('companyDashboard.documentCount') }}</th>
                    <th>{{ $t('companyDashboard.tableHeaders.actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="internship in filteredInternshipsDocs" :key="internship.id">
                    <td>{{ getStudentFullName(internship) }}</td>
                    <td>{{ getYear(internship.start_date) }}</td>
                    <td><span class="badge bg-secondary">{{ getSemester(internship.start_date) }}</span></td>
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
                        :title="$t('companyDashboard.actions.viewDocuments')" 
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
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useI18n } from 'vue-i18n'

const authStore = useAuthStore()
const router = useRouter()
const { t } = useI18n()

const activeTab = ref('overview')
const loading = ref(false)
const internships = ref([])

// Filter state for Internships tab
const searchStudent = ref('')
const selectedStatus = ref('')

// Filter state for Documents tab
const searchStudentDocs = ref('')
const selectedStatusDocs = ref('')

// Computed: filtered internships for Internships tab
const filteredInternshipsTab = computed(() => {
  return internships.value.filter(internship => {
    // Search by student name
    if (searchStudent.value) {
      const fullName = getStudentFullName(internship).toLowerCase()
      if (!fullName.includes(searchStudent.value.toLowerCase())) {
        return false
      }
    }
    
    // Filter by status
    if (selectedStatus.value && internship.status !== selectedStatus.value) {
      return false
    }
    
    return true
  })
})

// Computed: filtered internships for Documents tab
const filteredInternshipsDocs = computed(() => {
  return internships.value.filter(internship => {
    // Search by student name
    if (searchStudentDocs.value) {
      const fullName = getStudentFullName(internship).toLowerCase()
      if (!fullName.includes(searchStudentDocs.value.toLowerCase())) {
        return false
      }
    }
    
    // Filter by status
    if (selectedStatusDocs.value && internship.status !== selectedStatusDocs.value) {
      return false
    }
    
    return true
  })
})

const hasActiveFiltersInternships = computed(() => {
  return searchStudent.value !== '' || selectedStatus.value !== ''
})

const hasActiveFiltersDocuments = computed(() => {
  return searchStudentDocs.value !== '' || selectedStatusDocs.value !== ''
})

const clearFiltersInternships = () => {
  searchStudent.value = ''
  selectedStatus.value = ''
}

const clearFiltersDocuments = () => {
  searchStudentDocs.value = ''
  selectedStatusDocs.value = ''
}

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

const getYear = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.getFullYear()
}

const getSemester = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  const month = date.getMonth() + 1 // 0-indexed
  // ZS (Winter Semester): September-February (9-2)
  // LS (Summer Semester): February-June (2-6)
  return month >= 9 || month <= 1 ? 'ZS' : 'LS'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('sk-SK')
}

const getStatusClass = (status) => {
  const statusClasses = {
    // New statuses
    'created': 'bg-secondary',
    'approved by garant': 'bg-info',
    'rejected by garant': 'bg-danger',
    'confirmed by company': 'bg-success',
    'not confirmed by company': 'bg-danger',
    'defended by student': 'bg-primary',
    'not defended by student': 'bg-danger'
  }
  return statusClasses[status] || 'bg-secondary'
}

const getTranslatedStatus = (status) => {
  const statusMap = {
    // New statuses
    'created': 'studentInternship.status.created',
    'approved by garant': 'studentInternship.status.approvedByGarant',
    'rejected by garant': 'studentInternship.status.rejectedByGarant',
    'confirmed by company': 'studentInternship.status.confirmedByCompany',
    'not confirmed by company': 'studentInternship.status.notConfirmedByCompany',
    'defended by student': 'studentInternship.status.defendedByStudent',
    'not defended by student': 'studentInternship.status.notDefendedByStudent'
  }
  const translationKey = statusMap[status] || 'studentInternship.status.created'
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
