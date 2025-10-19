<template>
  <div class="create-internship-form">
    <h3 class="mb-4">{{ isEditMode ? 'Upraviť prax' : 'Nová prax' }}</h3>
    
    <form @submit.prevent="handleSubmit">
      <div class="row mb-3">
        <!-- Študent field -->
        <div class="col-md-6">
          <label for="studentId" class="form-label">
            Študent<span class="text-danger">*</span>
          </label>
          <select
            class="form-select"
            id="studentId"
            v-model.number="formData.student_id"
            :disabled="isLoadingData"
            required
          >
            <option value="" disabled>
              {{ isLoadingData ? 'Načítava sa...' : 'Vyberte študenta' }}
            </option>
            <option v-for="student in students" :key="student.id" :value="student.id">
              {{ getStudentFullName(student) }}
            </option>
          </select>
          <small v-if="students.length === 0 && !isLoadingData" class="text-muted">
            Žiadni študenti k dispozícii
          </small>
        </div>

        <!-- Firma field -->
        <div class="col-md-6">
          <label for="companyId" class="form-label">
            Firma<span class="text-danger">*</span>
          </label>
          <select
            class="form-select"
            id="companyId"
            v-model.number="formData.company_id"
            :disabled="isLoadingData"
            required
          >
            <option value="" disabled>
              {{ isLoadingData ? 'Načítava sa...' : 'Vyberte firmu' }}
            </option>
            <option v-for="company in companies" :key="company.id" :value="company.id">
              {{ company.name }}
            </option>
          </select>
          <small v-if="companies.length === 0 && !isLoadingData" class="text-muted">
            Žiadne firmy k dispozícii
          </small>
        </div>
      </div>

      <div class="row mb-3">
        <!-- Akademický rok field -->
        <div class="col-md-2">
          <label for="academy_year" class="form-label">
            Akademický rok<span class="text-danger">*</span>
          </label>
          <input
            type="text"
            class="form-control"
            id="academy_year"
            v-model="formData.academy_year"
            placeholder="2024/2025"
            pattern="\d{4}/\d{4}"
            required
          />
          <small class="text-muted">Formát: YYYY/YYYY</small>
        </div>

        <!-- Semester field -->
        <div class="col-md-2">
          <label for="semester" class="form-label">
            Semester<span class="text-danger">*</span>
          </label>
          <select
            class="form-select"
            id="semester"
            v-model="formData.semester"
            required
          >
            <option value="" disabled>Vyberte semester</option>
            <option value="ZS">ZS</option>
            <option value="LS">LS</option>
          </select>
        </div>

        <!-- Status field -->
        <div class="col-md-2">
          <label for="status" class="form-label">
            Stav<span class="text-danger">*</span>
          </label>
          <select
            class="form-select"
            id="status"
            v-model="formData.status"
            required
          >
            <option value="" disabled>Vyberte stav</option>
            <option value="vytvorená">Vytvorená</option>
            <option value="potvrdená">Potvrdená</option>
            <option value="schválená">Schválená</option>
            <option value="zamietnutá">Zamietnutá</option>
            <option value="obhájená">Obhájená</option>
            <option value="neobhájená">Neobhájená</option>
          </select>
        </div>

        <!-- Dátum začiatku field -->
        <div class="col-md-3">
          <label for="startDate" class="form-label">
            Dátum začiatku<span class="text-danger">*</span>
          </label>
          <input
            type="date"
            class="form-control"
            id="startDate"
            v-model="formData.start_date"
            required
          />
        </div>

        <!-- Dátum konca field -->
        <div class="col-md-3">
          <label for="endDate" class="form-label">
            Dátum konca<span class="text-danger">*</span>
          </label>
          <input
            type="date"
            class="form-control"
            id="endDate"
            v-model="formData.end_date"
            :min="formData.start_date"
            required
          />
        </div>
      </div>

      <!-- Info message -->
      <div class="alert alert-info mb-4" v-if="!isEditMode">
        <i class="bi bi-info-circle me-2"></i>
        Po vytvorení praxe systém vygeneruje PDF „Dohoda o odbornej praxi". Stav bude 
        <strong>Vytvorená</strong>.
      </div>

      <!-- Action buttons -->
      <div class="d-flex justify-content-end gap-2">
        <button
          type="button"
          class="btn btn-outline-secondary"
          @click="handleCancel"
        >
          Zrušiť
        </button>
        <button
          type="submit"
          class="btn btn-primary"
          :disabled="isSubmitting"
        >
          <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          {{ isEditMode ? 'Upraviť prax' : 'Vytvoriť prax' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const props = defineProps({
  initialStudents: {
    type: Array,
    default: () => []
  },
  initialCompanies: {
    type: Array,
    default: () => []
  },
  internship: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['submit', 'cancel'])

const currentYear = new Date().getFullYear()

const isEditMode = computed(() => props.internship !== null)

const formData = ref({
  student_id: '',
  company_id: '',
  status: 'pending',
  academy_year: `${currentYear}/${currentYear + 1}`,
  semester: 'LS',
  start_date: '',
  end_date: ''
})

const isSubmitting = ref(false)
const isLoadingData = ref(false)

// Students data from API
const students = ref(props.initialStudents.length > 0 ? props.initialStudents : [])

// Helper function to get student full name
const getStudentFullName = (student) => {
  if (student.full_name) return student.full_name
  if (student.first_name && student.last_name) {
    return `${student.first_name} ${student.last_name}`
  }
  return student.name || 'Unknown Student'
}

// Companies data from API
const companies = ref(props.initialCompanies.length > 0 ? props.initialCompanies : [])

// Fetch students and companies on component mount
const fetchStudents = async () => {
  if (props.initialStudents.length > 0) return // Use props if provided
  
  try {
    const response = await fetch('/api/students', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    })

    if (response.ok) {
      const data = await response.json()
      students.value = data.data || data
    } else {
      console.error('Failed to fetch students:', response.status)
    }
  } catch (error) {
    console.error('Error fetching students:', error)
  }
}

const fetchCompanies = async () => {
  if (props.initialCompanies.length > 0) return // Use props if provided
  
  try {
    const response = await fetch('/api/companies', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    })

    if (response.ok) {
      const data = await response.json()
      companies.value = data.data || data
    } else {
      console.error('Failed to fetch companies:', response.status)
    }
  } catch (error) {
    console.error('Error fetching companies:', error)
  }
}

// Fetch both in parallel
const fetchDropdownData = async () => {
  isLoadingData.value = true
  
  try {
    await Promise.all([fetchStudents(), fetchCompanies()])
  } catch (error) {
    console.error('Error fetching dropdown data:', error)
  } finally {
    isLoadingData.value = false
  }
}

// Load data when component is mounted
onMounted(() => {
  fetchDropdownData()
  
  // If editing, populate form with internship data
  if (props.internship) {
    formData.value = {
      student_id: props.internship.student_id || props.internship.student?.id || '',
      company_id: props.internship.company_id || props.internship.company?.id || '',
      status: props.internship.status || 'vytvorená',
      academy_year: props.internship.academy_year || `${currentYear}/${currentYear + 1}`,
      semester: props.internship.semester || 'LS',
      start_date: props.internship.start_date || '',
      end_date: props.internship.end_date || ''
    }
  }
})

const handleSubmit = async () => {
  isSubmitting.value = true
  
  try {
    // Emit the form data to parent component
    emit('submit', { ...formData.value })
    
    // Reset form after successful submission
    resetForm()
  } catch (error) {
    console.error('Error submitting form:', error)
  } finally {
    isSubmitting.value = false
  }
}

const handleCancel = () => {
  resetForm()
  emit('cancel')
}

const resetForm = () => {
  formData.value = {
    student_id: '',
    company_id: '',
    status: 'vytvorená',
    academy_year: `${currentYear}/${currentYear + 1}`,
    semester: 'LS',
    start_date: '',
    end_date: ''
  }
}
</script>

<style scoped>
.create-internship-form {
  max-width: 1200px;
}

.form-label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.text-danger {
  color: #dc3545;
}

.alert-info {
  background-color: #e7f3ff;
  border-color: #b3d9ff;
  color: #004085;
}

.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.btn-primary:hover {
  background-color: #0b5ed7;
  border-color: #0a58ca;
}

.btn-outline-secondary {
  color: #6c757d;
  border-color: #6c757d;
}

.btn-outline-secondary:hover {
  background-color: #6c757d;
  color: white;
}

.form-select {
  cursor: pointer;
}

.form-select:focus {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-select option {
  padding: 0.5rem;
}
</style>
