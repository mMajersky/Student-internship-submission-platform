<template>
  <div class="create-internship-form">
    <h3 class="mb-4">{{ isEditMode ? $t('garantInternshipForm.editInternship') : $t('garantInternshipForm.newInternship') }}</h3>
    
    <form @submit.prevent="handleSubmit">
      <div class="row mb-3">
        <!-- Študent field -->
        <div class="col-md-6">
          <label for="studentId" class="form-label">
            {{ $t('garantInternshipForm.studentRequired') }}
          </label>
          <select
            class="form-select"
            id="studentId"
            v-model.number="formData.student_id"
            :disabled="isLoadingData"
            required
          >
            <option value="" disabled>
              {{ isLoadingData ? $t('garantInternshipForm.loading') : $t('garantInternshipForm.selectStudent') }}
            </option>
            <option v-for="student in students" :key="student.id" :value="student.id">
              {{ getStudentFullName(student) }}
            </option>
          </select>
          <small v-if="students.length === 0 && !isLoadingData" class="text-muted">
            {{ $t('garantInternshipForm.noStudents') }}
          </small>
        </div>

        <!-- Firma field -->
        <div class="col-md-6">
          <label for="companyId" class="form-label">
            {{ $t('garantInternshipForm.companyRequired') }}
          </label>
          <select
            class="form-select"
            id="companyId"
            v-model.number="formData.company_id"
            :disabled="isLoadingData"
            required
          >
            <option value="" disabled>
              {{ isLoadingData ? $t('garantInternshipForm.loading') : $t('garantInternshipForm.selectCompany') }}
            </option>
            <option v-for="company in companies" :key="company.id" :value="company.id">
              {{ company.name }}
            </option>
          </select>
          <small v-if="companies.length === 0 && !isLoadingData" class="text-muted">
            {{ $t('garantInternshipForm.noCompanies') }}
          </small>
        </div>
      </div>

      <div class="row mb-3">
        <!-- Akademický rok field -->
        <div class="col-md-2">
          <label for="academy_year" class="form-label">
            {{ $t('garantInternshipForm.academyYearRequired') }}
          </label>
          <input
            type="text"
            class="form-control"
            id="academy_year"
            v-model="formData.academy_year"
            :placeholder="$t('garantInternshipForm.academyYearPlaceholder')"
            pattern="\d{4}/\d{4}"
            required
          />
          <small class="text-muted">{{ $t('garantInternshipForm.academyYearFormat') }}</small>
        </div>

        <!-- Semester field -->
        <div class="col-md-2">
          <label for="semester" class="form-label">
            {{ $t('garantInternshipForm.semesterRequired') }}
          </label>
          <select
            class="form-select"
            id="semester"
            v-model="formData.semester"
            required
          >
            <option value="" disabled>{{ $t('garantInternshipForm.selectSemester') }}</option>
            <option value="ZS">ZS</option>
            <option value="LS">LS</option>
          </select>
        </div>

        <!-- Status field -->
        <div class="col-md-2">
          <label for="status" class="form-label">
            {{ $t('garantInternshipForm.statusRequired') }}
          </label>
          <select
            class="form-select"
            id="status"
            v-model="formData.status"
            required
          >
            <option value="" disabled>{{ $t('garantInternshipForm.selectStatus') }}</option>
            <option value="vytvorená">{{ $t('garantInternshipForm.statusCreated') }}</option>
            <option value="potvrdená">{{ $t('garantInternshipForm.statusConfirmed') }}</option>
            <option value="schválená">{{ $t('garantInternshipForm.statusApproved') }}</option>
            <option value="zamietnutá">{{ $t('garantInternshipForm.statusRejected') }}</option>
            <option value="obhájená">{{ $t('garantInternshipForm.statusCompleted') }}</option>
            <option value="neobhájená">{{ $t('garantInternshipForm.statusFailed') }}</option>
          </select>
        </div>

        <!-- Dátum začiatku field -->
        <div class="col-md-3">
          <label for="startDate" class="form-label">
            {{ $t('garantInternshipForm.startDateRequired') }}
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
            {{ $t('garantInternshipForm.endDateRequired') }}
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

      <!-- Info message for new internships -->
      <div class="alert alert-info mb-4" v-if="!isEditMode">
        <i class="bi bi-info-circle me-2"></i>
        {{ $t('garantInternshipForm.infoMessage') }}
        <strong>{{ $t('garantInternshipForm.statusCreatedBold') }}</strong>.
      </div>

      <!-- Company approval info box -->
      <div class="company-approval-info mb-4" v-if="isEditMode && props.internship && props.internship.status === 'potvrdená'">
        <div class="info-box p-3 rounded">
          <div class="d-flex align-items-start">
            <i class="bi bi-envelope-check text-primary me-3 fs-4"></i>
            <div class="flex-grow-1">
              <h5 class="mb-2 text-primary">{{ $t('garantInternshipForm.approvalWaiting') }}</h5>
              <p class="mb-3 text-muted">
                {{ $t('garantInternshipForm.approvalWaitingDesc') }}
              </p>
              <div class="d-flex align-items-center">
                <button
                  type="button"
                  class="btn btn-outline-primary me-3"
                  @click="resendApprovalEmail"
                  :disabled="isResendingEmail"
                >
                  <span v-if="isResendingEmail" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  <i class="bi bi-envelope me-1"></i>
                  {{ isResendingEmail ? $t('garantInternshipForm.resending') : $t('garantInternshipForm.resendEmail') }}
                </button>
                <small class="text-muted">
                  <i class="bi bi-question-circle me-1"></i>
                  {{ $t('garantInternshipForm.resendEmailDesc') }}
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="d-flex justify-content-end gap-2">
        <button
          type="button"
          class="btn btn-outline-secondary"
          @click="handleCancel"
        >
          {{ $t('garantInternshipForm.cancel') }}
        </button>
        <button
          type="submit"
          class="btn btn-primary"
          :disabled="isSubmitting"
        >
          <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          {{ isEditMode ? $t('garantInternshipForm.editInternshipBtn') : $t('garantInternshipForm.createInternshipBtn') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

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
const isResendingEmail = ref(false)

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

const resendApprovalEmail = async () => {
  if (!props.internship || !props.internship.id) return

  isResendingEmail.value = true

  try {
    const response = await fetch(`/api/internships/${props.internship.id}/resend-approval-email`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      }
    })

    const data = await response.json()

    if (response.ok) {
      alert(`${t('garantInternshipForm.emailResentSuccess')} ${data.email}`)
    } else {
      alert(`${t('garantInternshipForm.emailResentError')} ${data.message || t('garantInternshipForm.emailResentUnknownError')}`)
    }
  } catch (error) {
    console.error('Error resending email:', error)
    alert(t('garantInternshipForm.emailResentFailed'))
  } finally {
    isResendingEmail.value = false
  }
}
</script>
