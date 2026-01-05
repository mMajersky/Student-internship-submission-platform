<template>
  <div class="container mt-4">
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>{{ $t('garantDashboard.title') }}</h2>
          <span class="badge bg-warning text-dark fs-6">{{ authStore.userRole }}</span>
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
          {{ $t('garantDashboard.tabs.overview') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button
          class="nav-link"
          :class="{ active: activeTab === 'create-internship' }"
          @click="handleNewInternship"
          type="button"
        >
          <i class="bi bi-plus-circle me-2"></i>
          {{ $t('garantDashboard.tabs.createInternship') }}
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
          {{ $t('garantDashboard.tabs.internships') }}
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
          {{ $t('garantDashboard.tabs.documents') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button
          class="nav-link"
          :class="{ active: activeTab === 'statistics' }"
          @click="activeTab = 'statistics'"
          type="button"
        >
          <i class="bi bi-bar-chart me-2"></i>
          Štatistiky
        </button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
      <!-- Overview Tab -->
      <div v-if="activeTab === 'overview'" class="tab-pane fade show active">
        <div class="row">
          <div class="col-md-4 mb-4">
            <div class="card h-100 border-primary">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-plus-circle me-2 text-primary"></i>
                  {{ $t('garantDashboard.cards.createInternship') }}
                </h5>
                <p class="card-text">{{ $t('garantDashboard.cards.createInternshipDesc') }}</p>
                <button class="btn btn-primary" @click="handleNewInternship">
                  <i class="bi bi-plus me-2"></i>
                  {{ $t('garantDashboard.cards.newInternship') }}
                </button>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card h-100 border-info">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-briefcase me-2 text-info"></i>
                  {{ $t('garantDashboard.cards.manageInternships') }}
                </h5>
                <p class="card-text">{{ $t('garantDashboard.cards.manageInternshipsDesc') }}</p>
                <button class="btn btn-info" @click="activeTab = 'internships'">
                  <i class="bi bi-eye me-2"></i>
                  {{ $t('garantDashboard.cards.viewInternships') }}
                </button>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-file-earmark-pdf me-2 text-success"></i>
                  {{ $t('garantDashboard.cards.documents') }}
                </h5>
                <p class="card-text">{{ $t('garantDashboard.cards.documentsDesc') }}</p>
                <button class="btn btn-success" @click="activeTab = 'documents'">
                  <i class="bi bi-folder me-2"></i>
                  {{ $t('garantDashboard.cards.viewDocuments') }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics -->
        <div class="row mt-4">
          <div class="col-12">
            <h4 class="mb-3">{{ $t('garantDashboard.statistics') }}</h4>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-body">
                <h3 class="text-primary mb-0">{{ stats.total }}</h3>
                <p class="text-muted mb-0">{{ $t('garantDashboard.totalInternships') }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-body">
                <h3 class="text-warning mb-0">{{ stats.inProgress }}</h3>
                <p class="text-muted mb-0">{{ $t('garantDashboard.inProgress') }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-body">
                <h3 class="text-success mb-0">{{ stats.completed }}</h3>
                <p class="text-muted mb-0">{{ $t('garantDashboard.completed') }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-body">
                <h3 class="text-info mb-0">{{ stats.planned }}</h3>
                <p class="text-muted mb-0">{{ $t('garantDashboard.planned') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Create Internship Tab -->
      <div v-if="activeTab === 'create-internship'" class="tab-pane fade show active">
        <div class="card">
          <div class="card-body">
            <CreateInternshipForm
              :internship="editingInternship"
              @submit="handleCreateInternship"
              @cancel="handleCancelForm"
            />
          </div>
        </div>
      </div>

      <!-- All Internships Tab -->
      <div v-if="activeTab === 'internships'" class="tab-pane fade show active">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="card-title mb-0">{{ $t('garantDashboard.internshipsList') }}</h5>
              <button 
                v-if="hasActiveFilters" 
                class="btn btn-sm btn-outline-secondary"
                @click="clearAllFilters"
              >
                <i class="bi bi-x-circle me-1"></i>
                {{ $t('garantDashboard.filters.clearAll') }}
              </button>
            </div>

            <!-- Filters Section -->
            <InternshipFilters
              v-if="internships.length > 0"
              :available-years="availableYears"
              :available-companies="availableCompanies"
              :available-study-fields="availableStudyFields"
              :available-students="availableStudents"
              v-model:selected-years="selectedYears"
              v-model:selected-companies="selectedCompanies"
              v-model:selected-study-fields="selectedStudyFields"
              v-model:student-search-query="studentSearchQuery"
              id-prefix="internships-"
            />

            <!-- Results count -->
            <div v-if="internships.length > 0 && hasActiveFilters" class="mb-3">
              <span class="text-muted">
                {{ $t('garantDashboard.filters.showing', { count: filteredInternships.length, total: internships.length }) }}
              </span>
            </div>

            <div v-if="internships.length === 0" class="text-center py-5">
              <i class="bi bi-inbox fs-1 text-muted"></i>
              <p class="text-muted mt-3">{{ $t('garantDashboard.noInternships') }}</p>
              <button class="btn btn-primary" @click="activeTab = 'create-internship'">
                <i class="bi bi-plus me-2"></i>
                {{ $t('garantDashboard.createFirstInternship') }}
              </button>
            </div>

            <div v-else-if="filteredInternships.length === 0" class="text-center py-5">
              <i class="bi bi-funnel fs-1 text-muted"></i>
              <p class="text-muted mt-3">{{ $t('garantDashboard.filters.noResults') }}</p>
              <button class="btn btn-outline-secondary" @click="clearAllFilters">
                <i class="bi bi-x-circle me-1"></i>
                {{ $t('garantDashboard.filters.clearAll') }}
              </button>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>{{ $t('garantDashboard.tableHeaders.student') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.company') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.year') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.semester') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.start') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.end') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.status') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="internship in filteredInternships" :key="internship.id">
                    <td>{{ getStudentFullName(internship) }}</td>
                    <td>{{ internship.company?.name || '-' }}</td>
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
                      <button class="btn btn-sm btn-outline-success me-1" :title="$t('garantDashboard.actions.addComment')" @click="handleAddComment(internship)">
                        <i class="bi bi-chat-left-text"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-primary me-1" :title="$t('garantDashboard.actions.edit')" @click="handleEditInternship(internship)">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-secondary me-1" :title="$t('garantDashboard.actions.viewDocuments')" @click="openDocuments(internship)">
                        <i class="bi bi-folder"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger" :title="$t('garantDashboard.actions.delete')" @click="handleDeleteInternship(internship.id)">
                        <i class="bi bi-trash"></i>
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
              <h5 class="card-title mb-0">{{ $t('garantDashboard.documentsTitle') }}</h5>
              <button 
                v-if="hasActiveFilters" 
                class="btn btn-sm btn-outline-secondary"
                @click="clearAllFilters"
              >
                <i class="bi bi-x-circle me-1"></i>
                {{ $t('garantDashboard.filters.clearAll') }}
              </button>
            </div>

            <!-- Filters Section -->
            <InternshipFilters
              v-if="internships.length > 0"
              :available-years="availableYears"
              :available-companies="availableCompanies"
              :available-study-fields="availableStudyFields"
              :available-students="availableStudents"
              v-model:selected-years="selectedYears"
              v-model:selected-companies="selectedCompanies"
              v-model:selected-study-fields="selectedStudyFields"
              v-model:student-search-query="studentSearchQuery"
              id-prefix="documents-"
            />

            <!-- Results count -->
            <div v-if="internships.length > 0 && hasActiveFilters" class="mb-3">
              <span class="text-muted">
                {{ $t('garantDashboard.filters.showing', { count: filteredInternships.length, total: internships.length }) }}
              </span>
            </div>

            <div v-if="internships.length === 0" class="text-center py-5">
              <i class="bi bi-inbox fs-1 text-muted"></i>
              <p class="text-muted mt-3">{{ $t('garantDashboard.noInternships') }}</p>
            </div>

            <div v-else-if="filteredInternships.length === 0" class="text-center py-5">
              <i class="bi bi-funnel fs-1 text-muted"></i>
              <p class="text-muted mt-3">{{ $t('garantDashboard.filters.noResults') }}</p>
              <button class="btn btn-outline-secondary" @click="clearAllFilters">
                <i class="bi bi-x-circle me-1"></i>
                {{ $t('garantDashboard.filters.clearAll') }}
              </button>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>{{ $t('garantDashboard.tableHeaders.student') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.company') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.year') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.semester') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.status') }}</th>
                    <th>{{ $t('garantDashboard.documentCount') }}</th>
                    <th>{{ $t('garantDashboard.tableHeaders.actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="internship in filteredInternships" :key="internship.id">
                    <td>{{ getStudentFullName(internship) }}</td>
                    <td>{{ internship.company?.name || '-' }}</td>
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
                      <button class="btn btn-sm btn-outline-primary" :title="$t('garantDashboard.actions.viewDocuments')" @click="openDocuments(internship)">
                        <i class="bi bi-folder-open me-1"></i>
                        {{ $t('garantDashboard.viewDocumentsBtn') }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- Statistics Tab -->
      <div class="card">
        <div class="card-body">
          <div v-if="activeTab === 'statistics'" class="tab-pane fade show active">
            <StatisticsTab :internships="internships" />
          </div>
        </div>
      </div>


    </div>

    <!-- Comment Modal -->
    <CommentModal
      :is-visible="showCommentModal"
      :internship="selectedInternshipForComment"
      :auth-token="authStore.token"
      @close="handleCloseCommentModal"
      @submit="handleSubmitComment"
    />

    <!-- Confirmation Dialog -->
    <ConfirmationDialog
      :is-visible="showDeleteConfirmation"
      :title="$t('confirmationDialog.deleteTitle')"
      :message="$t('confirmationDialog.deleteMessage')"
      :confirm-text="$t('confirmationDialog.confirm')"
      :cancel-text="$t('confirmationDialog.cancel')"
      type="danger"
      :requires-text-confirmation="true"
      confirmation-text-required="delete"
      :text-confirmation-label="$t('confirmationDialog.deleteTextLabel')"
      :text-confirmation-placeholder="$t('confirmationDialog.deleteTextPlaceholder')"
      :text-confirmation-hint="$t('confirmationDialog.deleteTextHint')"
      @confirm="confirmDeleteInternship"
      @cancel="cancelDeleteInternship"
    />

    <!-- Message Modal -->
    <MessageModal
      :is-visible="showMessageModal"
      :title="messageModalTitle"
      :message="messageModalMessage"
      :type="messageModalType"
      @close="closeMessageModal"
      @confirm="closeMessageModal"
    />

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useI18n } from 'vue-i18n'
import CreateInternshipForm from '@/components/garant/GarantInternshipForm.vue'
import CommentModal from '@/components/garant/CommentModal.vue'
import ConfirmationDialog from '@/components/common/ConfirmationDialog.vue'
import InternshipFilters from '@/components/garant/InternshipFilters.vue'
import MessageModal from '@/components/common/MessageModal.vue'
import StatisticsTab from '@/components/garant/tabs/StatisticsTab.vue'

const { t } = useI18n()

const authStore = useAuthStore()
const router = useRouter()

const activeTab = ref('overview')

// Editing state
const editingInternship = ref(null)

// Comment modal state
const showCommentModal = ref(false)
const selectedInternshipForComment = ref(null)

// Delete confirmation state
const showDeleteConfirmation = ref(false)
const internshipToDelete = ref(null)

// Message modal state
const showMessageModal = ref(false)
const messageModalTitle = ref('')
const messageModalMessage = ref('')
const messageModalType = ref('info')

const showMessage = (message, title = null, type = 'info') => {
  messageModalTitle.value = title || t('common.message')
  messageModalMessage.value = message
  messageModalType.value = type
  showMessageModal.value = true
}

const closeMessageModal = () => {
  showMessageModal.value = false
}

// Filter state
const selectedYears = ref([])
const selectedCompanies = ref([])
const selectedStudyFields = ref([])
const studentSearchQuery = ref('')

// Statistics
const stats = ref({
  total: 0,
  inProgress: 0,
  completed: 0,
  planned: 0
})

// Internships data
const internships = ref([])

// Fetch internships from API
const fetchInternships = async () => {
  try {
    const response = await fetch('/api/internships', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    })

    if (response.ok) {
      const data = await response.json()
      internships.value = data.data || data
      
      // Update statistics
      updateStatistics()
    } else {
      console.error('Failed to fetch internships:', response.status)
    }
  } catch (error) {
    console.error('Error fetching internships:', error)
  }
}

// Update statistics based on internships data (new status set)
const updateStatistics = () => {
  stats.value.total = internships.value.length
  stats.value.inProgress = internships.value.filter(i => ['approved by garant', 'confirmed by company', 'not confirmed by company'].includes(i.status)).length
  stats.value.completed = internships.value.filter(i => ['defended by student', 'not defended by student'].includes(i.status)).length
  stats.value.planned = internships.value.filter(i => i.status === 'created').length
}

// Computed: unique years from internships
const availableYears = computed(() => {
  const years = new Set()
  internships.value.forEach(i => {
    if (i.academy_year) years.add(i.academy_year)
    else if (i.start_date) years.add(getYear(i.start_date))
  })
  return Array.from(years).sort().reverse()
})

// Computed: unique companies from internships
const availableCompanies = computed(() => {
  const companies = new Map()
  internships.value.forEach(i => {
    if (i.company && i.company.id) {
      companies.set(i.company.id, { id: i.company.id, name: i.company.name })
    }
  })
  return Array.from(companies.values()).sort((a, b) => a.name.localeCompare(b.name))
})

// Computed: unique study fields from internships (via student)
const availableStudyFields = computed(() => {
  const fields = new Set()
  internships.value.forEach(i => {
    if (i.student && i.student.study_field) {
      fields.add(i.student.study_field)
    }
  })
  return Array.from(fields).sort()
})

// Computed: unique students from internships
const availableStudents = computed(() => {
  const students = new Map()
  internships.value.forEach(i => {
    if (i.student && i.student.id) {
      students.set(i.student.id, {
        id: i.student.id,
        name: `${i.student.name} ${i.student.surname}`
      })
    }
  })
  return Array.from(students.values()).sort((a, b) => a.name.localeCompare(b.name))
})

// Computed: filtered internships based on selected filters
const filteredInternships = computed(() => {
  return internships.value.filter(internship => {
    // Year filter
    if (selectedYears.value.length > 0) {
      const internshipYear = internship.academy_year || getYear(internship.start_date)
      if (!selectedYears.value.includes(internshipYear)) return false
    }
    
    // Company filter
    if (selectedCompanies.value.length > 0) {
      if (!internship.company || !selectedCompanies.value.includes(internship.company.id)) return false
    }
    
    // Study field filter
    if (selectedStudyFields.value.length > 0) {
      if (!internship.student || !selectedStudyFields.value.includes(internship.student.study_field)) return false
    }
    
    // Student search filter
    if (studentSearchQuery.value.trim()) {
      if (!internship.student) return false
      const fullName = `${internship.student.name} ${internship.student.surname}`.toLowerCase()
      if (!fullName.includes(studentSearchQuery.value.toLowerCase().trim())) return false
    }
    
    return true
  })
})

const clearAllFilters = () => {
  selectedYears.value = []
  selectedCompanies.value = []
  selectedStudyFields.value = []
  studentSearchQuery.value = ''
}

const hasActiveFilters = computed(() => {
  return selectedYears.value.length > 0 ||
    selectedCompanies.value.length > 0 ||
    selectedStudyFields.value.length > 0 ||
    studentSearchQuery.value.trim().length > 0
})

// Load data when component is mounted
onMounted(() => {
  fetchInternships()
})

const handleNewInternship = () => {
  editingInternship.value = null
  activeTab.value = 'create-internship'
}

const handleEditInternship = (internship) => {
  editingInternship.value = { ...internship }
  activeTab.value = 'create-internship'
}

const handleCancelForm = () => {
  editingInternship.value = null
  activeTab.value = 'overview'
}

const handleCreateInternship = async (formData) => {
  try {
    const isEditing = editingInternship.value !== null
    
    // Prepare data for backend API
    const payload = {
      student_id: formData.student_id,
      company_id: formData.company_id,
      status: formData.status || 'created',
      academy_year: formData.academy_year,
      semester: formData.semester,
      start_date: formData.start_date || null,
      end_date: formData.end_date || null
    }

    // Determine API endpoint and method
    const url = isEditing 
      ? `/api/internships/${editingInternship.value.id}`
      : '/api/internships'
    const method = isEditing ? 'PUT' : 'POST'

    // Send data to backend API
    const response = await fetch(url, {
      method: method,
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload)
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || data.error || `Failed to ${editingInternship.value ? 'update' : 'create'} internship`)
    }
    
    // Refresh internships list from API - force reload
    await fetchInternships()
    
    // Force Vue to re-render by updating the reference
    internships.value = [...internships.value]
    
    // Show success message
    showMessage(editingInternship.value ? t('garantDashboard.messages.internshipUpdated') : t('garantDashboard.messages.internshipCreated'), null, 'success')

    // Reset editing state
    editingInternship.value = null

    // Switch to internships tab
    activeTab.value = 'internships'
  } catch (error) {
    console.error('Error creating internship:', error)
    showMessage(error.message || t('garantDashboard.messages.createError'), null, 'error')
  }
}

const handleDeleteInternship = (internshipId) => {
  internshipToDelete.value = internshipId
  showDeleteConfirmation.value = true
}

const confirmDeleteInternship = async () => {
  if (!internshipToDelete.value) return
  
  try {
    const response = await fetch(`/api/internships/${internshipToDelete.value}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    })

    if (!response.ok) {
      const data = await response.json()
      throw new Error(data.message || data.error || 'Failed to delete internship')
    }
    
    // Refresh internships list
    await fetchInternships()
    
    // Show success message
    showMessage(t('garantDashboard.messages.internshipDeleted'), null, 'success')
  } catch (error) {
    console.error('Error deleting internship:', error)
    showMessage(error.message || t('garantDashboard.messages.deleteError'), null, 'error')
  } finally {
    showDeleteConfirmation.value = false
    internshipToDelete.value = null
  }
}

const cancelDeleteInternship = () => {
  showDeleteConfirmation.value = false
  internshipToDelete.value = null
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
  // Map API status values to translation keys
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

// Comment handling
const handleAddComment = (internship) => {
  selectedInternshipForComment.value = internship
  showCommentModal.value = true
}

const handleCloseCommentModal = () => {
  showCommentModal.value = false
  selectedInternshipForComment.value = null
}

const handleSubmitComment = async (commentData) => {
  try {
    // Extract internship_id from commentData and prepare the payload
    const { internship_id, comment_type, content } = commentData
    
    const response = await fetch(`/api/internships/${internship_id}/comments`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        comment_type,
        content
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || data.error || 'Chyba pri ukladaní komentára')
    }

    // Show success message
    showMessage(t('garantDashboard.messages.commentAdded'), null, 'success')
    
    // Close modal
    handleCloseCommentModal()
    
    // Optionally refresh internships list to show updated comment count
    await fetchInternships()
  } catch (error) {
    console.error('Error submitting comment:', error)
    throw error // Re-throw to let modal handle the error display
  }
}

const openDocuments = (internship) => {
  router.push({ name: 'garant-internship-documents', params: { id: internship.id } })
}

</script>
