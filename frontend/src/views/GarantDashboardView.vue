<template>
  <div class="container mt-4">
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Dashboard garanta</h2>
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
          Prehľad
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
          Nová prax
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
          Všetky praxe
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
                  Vytvoriť prax
                </h5>
                <p class="card-text">Vytvorte novú prax pre študenta</p>
                <button class="btn btn-primary" @click="handleNewInternship">
                  <i class="bi bi-plus me-2"></i>
                  Nová prax
                </button>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card h-100 border-info">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-briefcase me-2 text-info"></i>
                  Správa praxí
                </h5>
                <p class="card-text">Zobrazenie a správa všetkých praxí</p>
                <button class="btn btn-info" @click="activeTab = 'internships'">
                  <i class="bi bi-eye me-2"></i>
                  Zobraziť praxe
                </button>
              </div>
            </div>
          </div>

          <div class="col-md-4 mb-4">
            <div class="card h-100 border-success">
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-file-earmark-pdf me-2 text-success"></i>
                  Dokumenty
                </h5>
                <p class="card-text">Správa dokumentov a dohôd</p>
                <button class="btn btn-success" disabled>
                  <i class="bi bi-folder me-2"></i>
                  Zobraziť dokumenty
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistics -->
        <div class="row mt-4">
          <div class="col-12">
            <h4 class="mb-3">Štatistiky</h4>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-body">
                <h3 class="text-primary mb-0">{{ stats.total }}</h3>
                <p class="text-muted mb-0">Celkový počet praxí</p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-body">
                <h3 class="text-warning mb-0">{{ stats.inProgress }}</h3>
                <p class="text-muted mb-0">Prebieha</p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-body">
                <h3 class="text-success mb-0">{{ stats.completed }}</h3>
                <p class="text-muted mb-0">Ukončené</p>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card text-center">
              <div class="card-body">
                <h3 class="text-info mb-0">{{ stats.planned }}</h3>
                <p class="text-muted mb-0">Plánované</p>
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
            <h5 class="card-title mb-4">Zoznam praxí</h5>
            
            <div v-if="internships.length === 0" class="text-center py-5">
              <i class="bi bi-inbox fs-1 text-muted"></i>
              <p class="text-muted mt-3">Zatiaľ neboli vytvorené žiadne praxe.</p>
              <button class="btn btn-primary" @click="activeTab = 'create-internship'">
                <i class="bi bi-plus me-2"></i>
                Vytvoriť prvú prax
              </button>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Študent</th>
                    <th>Firma</th>
                    <th>Rok</th>
                    <th>Semester</th>
                    <th>Začiatok</th>
                    <th>Koniec</th>
                    <th>Stav</th>
                    <th>Akcie</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="internship in internships" :key="internship.id">
                    <td>{{ getStudentFullName(internship) }}</td>
                    <td>{{ internship.company?.name || '-' }}</td>
                    <td>{{ getYear(internship.start_date) }}</td>
                    <td><span class="badge bg-secondary">{{ getSemester(internship.start_date) }}</span></td>
                    <td>{{ formatDate(internship.start_date) }}</td>
                    <td>{{ formatDate(internship.end_date) }}</td>
                    <td>
                      <span class="badge" :class="getStatusClass(internship.status)">
                        {{ internship.status }}
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-success me-1" title="Pridať komentár" @click="handleAddComment(internship)">
                        <i class="bi bi-chat-left-text"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-primary me-1" title="Upraviť" @click="handleEditInternship(internship)">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-secondary me-1" title="Stiahnuť PDF">
                        <i class="bi bi-file-pdf"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger" title="Vymazať" @click="handleDeleteInternship(internship.id)">
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
    </div>

    <!-- Comment Modal -->
    <CommentModal
      :is-visible="showCommentModal"
      :internship="selectedInternshipForComment"
      @close="handleCloseCommentModal"
      @submit="handleSubmitComment"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import CreateInternshipForm from '@/components/garant/GarantInternshipForm.vue'
import CommentModal from '@/components/garant/CommentModal.vue'

const authStore = useAuthStore()

const activeTab = ref('overview')

// Editing state
const editingInternship = ref(null)

// Comment modal state
const showCommentModal = ref(false)
const selectedInternshipForComment = ref(null)

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

// Update statistics based on internships data
const updateStatistics = () => {
  stats.value.total = internships.value.length
  stats.value.inProgress = internships.value.filter(i => i.status === 'in_progress').length
  stats.value.completed = internships.value.filter(i => i.status === 'completed').length
  stats.value.planned = internships.value.filter(i => i.status === 'pending').length
}

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
      status: formData.status || 'vytvorená',
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
    
    // Refresh internships list from API
    await fetchInternships()
    
    // Show success message
    alert(editingInternship.value ? 'Prax bola úspešne upravená!' : 'Prax bola úspešne vytvorená!')
    
    // Reset editing state
    editingInternship.value = null
    
    // Switch to internships tab
    activeTab.value = 'internships'
  } catch (error) {
    console.error('Error creating internship:', error)
    alert(error.message || 'Chyba pri vytváraní praxe. Skúste to znova.')
  }
}

const handleDeleteInternship = async (internshipId) => {
  if (!confirm('Naozaj chcete vymazať túto prax?')) {
    return
  }
  
  try {
    const response = await fetch(`/api/internships/${internshipId}`, {
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
    alert('Prax bola úspešne vymazaná!')
  } catch (error) {
    console.error('Error deleting internship:', error)
    alert(error.message || 'Chyba pri mazaní praxe. Skúste to znova.')
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
    'pending': 'bg-secondary',
    'confirmed': 'bg-info',
    'approved': 'bg-success',
    'in_progress': 'bg-warning text-dark',
    'completed': 'bg-success',
    'rejected': 'bg-danger',
    'cancelled': 'bg-danger'
  }
  return statusClasses[status] || 'bg-secondary'
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
    alert('Komentár bol úspešne pridaný!')
    
    // Close modal
    handleCloseCommentModal()
    
    // Optionally refresh internships list to show updated comment count
    await fetchInternships()
  } catch (error) {
    console.error('Error submitting comment:', error)
    throw error // Re-throw to let modal handle the error display
  }
}
</script>

<style scoped>
.card {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.badge {
  font-size: 0.875rem;
  padding: 0.375rem 0.75rem;
}

.nav-tabs .nav-link {
  color: #6c757d;
  border: none;
  border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link.active {
  color: #0d6efd;
  background-color: transparent;
  border-color: transparent;
  border-bottom-color: #0d6efd;
}

.table th {
  font-weight: 600;
  background-color: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}
</style>
