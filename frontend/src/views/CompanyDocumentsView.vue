<template>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">{{ $t('companyDocuments.title') }}</h5>
      <button class="btn btn-outline-secondary btn-sm" @click="goBack">
        <i class="bi bi-arrow-left me-1"></i>
        {{ $t('common.back') }}
      </button>
    </div>

    <!-- Internship info card -->
    <div class="card p-3 mb-4" v-if="internshipInfo">
      <div class="row">
        <div class="col-md-6">
          <p class="mb-1"><strong>{{ $t('companyDocuments.student') }}:</strong> {{ internshipInfo.student?.name }} {{ internshipInfo.student?.surname }}</p>
          <p class="mb-1"><strong>{{ $t('companyDocuments.studentEmail') }}:</strong> {{ internshipInfo.student?.student_email || '-' }}</p>
        </div>
        <div class="col-md-6">
          <p class="mb-1"><strong>{{ $t('companyDocuments.academyYear') }}:</strong> {{ internshipInfo.academy_year || '-' }}</p>
          <p class="mb-1"><strong>{{ $t('companyDocuments.status') }}:</strong> 
            <span class="badge" :class="getStatusClass(internshipInfo.status)">{{ getTranslatedStatus(internshipInfo.status) }}</span>
          </p>
        </div>
      </div>
    </div>

    <div class="card p-3 mb-4">
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>{{ $t('companyDocuments.tableHeaders.name') }}</th>
              <th>{{ $t('companyDocuments.tableHeaders.type') }}</th>
              <th>{{ $t('companyDocuments.tableHeaders.status') }}</th>
              <th>{{ $t('companyDocuments.tableHeaders.companyStatus') }}</th>
              <th>{{ $t('companyDocuments.tableHeaders.created') }}</th>
              <th class="text-end">{{ $t('companyDocuments.tableHeaders.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="doc in documents" :key="doc.id">
              <td>{{ doc.name || '-' }}</td>
              <td>{{ getDocumentTypeLabel(doc.type) }}</td>
              <td>{{ doc.status || '-' }}</td>
              <td>
                <span v-if="doc.company_status" class="badge" :class="getCompanyStatusClass(doc.company_status)">
                  {{ doc.company_status }}
                </span>
                <span v-else class="badge bg-secondary">{{ $t('companyDocuments.notValidated') }}</span>
              </td>
              <td>{{ formatDate(doc.created_at) }}</td>
              <td class="text-end">
                <button @click="download(doc)" class="btn btn-sm btn-outline-primary me-1" :title="$t('companyDocuments.download')">
                  <i class="bi bi-download"></i>
                </button>
                <button 
                  v-if="!doc.company_status" 
                  @click="openValidateModal(doc)" 
                  class="btn btn-sm btn-outline-success"
                  :title="$t('companyDocuments.validate')"
                >
                  <i class="bi bi-check-circle"></i>
                </button>
                <button 
                  v-if="doc.company_status === 'zamietnutý'" 
                  @click="showRejectionReason(doc)" 
                  class="btn btn-sm btn-outline-danger"
                  :title="$t('companyDocuments.viewRejectionReason')"
                >
                  <i class="bi bi-info-circle"></i>
                </button>
              </td>
            </tr>
            <tr v-if="!documents.length">
              <td colspan="6" class="text-center text-muted py-4">{{ $t('companyDocuments.noDocuments') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Výkaz praxe sekcia -->
    <div class="card p-3 mb-3" v-if="internshipInfo && internshipInfo.internship_report && internshipInfo.internship_report.submitted_at">
      <h6 class="fw-semibold mb-3">
        <i class="bi bi-file-text me-2"></i>
        {{ $t('companyDocuments.internshipReport') }}
      </h6>
      
      <div class="alert alert-success py-2 px-3 mb-3">
        <i class="bi bi-check-circle me-2"></i>
        <strong>{{ $t('companyDocuments.reportStatus') }}:</strong> {{ $t('companyDocuments.reportSubmitted') }}
        <br>
        <small class="text-muted">
          {{ $t('companyDocuments.reportSubmittedAt') }} 
          {{ formatDate(internshipInfo.internship_report.submitted_at) }}
        </small>
      </div>
      <button 
        class="btn btn-outline-primary btn-sm" 
        @click="showReportModal = true"
      >
        <i class="bi bi-eye me-1"></i>
        {{ $t('companyDocuments.viewReport') }}
      </button>
    </div>
  </div>

  <!-- Validation Modal -->
  <div v-if="showValidateModal" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true" @click.self="closeValidateModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ $t('companyDocuments.validateDocument') }}</h5>
          <button type="button" class="btn-close" @click="closeValidateModal"></button>
        </div>
        <div class="modal-body">
          <p><strong>{{ $t('companyDocuments.documentName') }}:</strong> {{ selectedDocument?.name }}</p>
          <p><strong>{{ $t('companyDocuments.documentType') }}:</strong> {{ getDocumentTypeLabel(selectedDocument?.type) }}</p>
          
          <div class="mt-3">
            <label class="form-label">{{ $t('companyDocuments.selectAction') }}:</label>
            <div class="btn-group w-100" role="group">
              <input type="radio" class="btn-check" name="validateAction" id="approveAction" value="approve" v-model="validateAction">
              <label class="btn btn-outline-success" for="approveAction">
                <i class="bi bi-check-circle me-1"></i>
                {{ $t('companyDocuments.approve') }}
              </label>
              <input type="radio" class="btn-check" name="validateAction" id="rejectAction" value="reject" v-model="validateAction">
              <label class="btn btn-outline-danger" for="rejectAction">
                <i class="bi bi-x-circle me-1"></i>
                {{ $t('companyDocuments.reject') }}
              </label>
            </div>
          </div>

          <div v-if="validateAction === 'reject'" class="mt-3">
            <label class="form-label">{{ $t('companyDocuments.rejectionReason') }}:</label>
            <textarea 
              class="form-control" 
              v-model="rejectionReason" 
              rows="3" 
              :placeholder="$t('companyDocuments.rejectionReasonPlaceholder')"
            ></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeValidateModal">
            {{ $t('common.cancel') }}
          </button>
          <button 
            type="button" 
            class="btn" 
            :class="validateAction === 'approve' ? 'btn-success' : 'btn-danger'"
            @click="submitValidation"
            :disabled="!validateAction || (validateAction === 'reject' && !rejectionReason.trim()) || validating"
          >
            <span v-if="validating" class="spinner-border spinner-border-sm me-1"></span>
            {{ validateAction === 'approve' ? $t('companyDocuments.confirmApprove') : $t('companyDocuments.confirmReject') }}
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showValidateModal" class="modal-backdrop fade show"></div>

  <!-- Rejection Reason Modal -->
  <div v-if="showRejectionModal" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true" @click.self="showRejectionModal = false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">{{ $t('companyDocuments.rejectionReasonTitle') }}</h5>
          <button type="button" class="btn-close btn-close-white" @click="showRejectionModal = false"></button>
        </div>
        <div class="modal-body">
          <p><strong>{{ $t('companyDocuments.documentName') }}:</strong> {{ selectedDocument?.name }}</p>
          <p><strong>{{ $t('companyDocuments.rejectedAt') }}:</strong> {{ formatDate(selectedDocument?.company_validated_at) }}</p>
          <hr>
          <p><strong>{{ $t('companyDocuments.reason') }}:</strong></p>
          <p class="text-muted">{{ selectedDocument?.company_rejection_reason }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="showRejectionModal = false">
            {{ $t('common.cancel') }}
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showRejectionModal" class="modal-backdrop fade show"></div>

  <!-- Modál pre zobrazenie výkazu praxe -->
  <div v-if="showReportModal" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true" @click.self="showReportModal = false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-file-text me-2"></i>
            {{ $t('companyDocuments.reportContent') }}
          </h5>
          <button type="button" class="btn-close" @click="showReportModal = false"></button>
        </div>
        <div class="modal-body" v-if="internshipInfo && internshipInfo.internship_report">
          <!-- Základné informácie -->
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ $t('companyDocuments.report.studentName') }}:</strong>
              <p class="mb-1">{{ internshipInfo.internship_report?.student_name || '-' }}</p>
            </div>
            <div class="col-md-6">
              <strong>{{ $t('companyDocuments.report.studentProgram') }}:</strong>
              <p class="mb-1">{{ internshipInfo.internship_report?.student_program || '-' }}</p>
            </div>
            <div class="col-md-6">
              <strong>{{ $t('companyDocuments.report.companyName') }}:</strong>
              <p class="mb-1">{{ internshipInfo.internship_report?.company_name || '-' }}</p>
            </div>
            <div class="col-md-6">
              <strong>{{ $t('companyDocuments.report.tutorName') }}:</strong>
              <p class="mb-1">{{ internshipInfo.internship_report?.tutor_name || '-' }}</p>
            </div>
            <div class="col-md-6">
              <strong>{{ $t('companyDocuments.report.totalHours') }}:</strong>
              <p class="mb-1">{{ internshipInfo.internship_report?.total_hours || '-' }}</p>
            </div>
          </div>
          
          <!-- Pracovné činnosti -->
          <div v-if="internshipInfo.internship_report?.activities && internshipInfo.internship_report.activities.length > 0" class="mb-3">
            <h6 class="fw-semibold mb-2">{{ $t('companyDocuments.report.activities') }}</h6>
            <div class="table-responsive">
              <table class="table table-sm table-bordered">
                <thead class="table-light">
                  <tr>
                    <th>{{ $t('companyDocuments.report.activityDate') }}</th>
                    <th>{{ $t('companyDocuments.report.activityDescription') }}</th>
                    <th>{{ $t('companyDocuments.report.activityHours') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(activity, index) in internshipInfo.internship_report.activities" :key="index">
                    <td>{{ formatDate(activity.date) }}</td>
                    <td>{{ activity.description }}</td>
                    <td>{{ activity.hours }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <!-- Hodnotenie od firmy -->
          <div v-if="internshipInfo.internship_report?.evaluation" class="mt-3">
            <h6 class="fw-semibold mb-2">{{ $t('companyDocuments.report.evaluation') }}</h6>
            <div class="row g-2">
              <div class="col-md-6" v-for="(value, key) in internshipInfo.internship_report.evaluation" :key="key">
                <div class="d-flex justify-content-between align-items-center border rounded p-2">
                  <span class="small">{{ getEvaluationCriterionLabel(key) }}:</span>
                  <span class="badge bg-primary">{{ value }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="showReportModal = false">
            {{ $t('common.cancel') }}
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showReportModal" class="modal-backdrop fade show"></div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const internshipId = route.params.id
const documents = ref([])
const internshipInfo = ref(null)
const showReportModal = ref(false)
const showValidateModal = ref(false)
const showRejectionModal = ref(false)
const selectedDocument = ref(null)
const validateAction = ref('')
const rejectionReason = ref('')
const validating = ref(false)

const formatDate = (iso) => {
  if (!iso) return '-'
  try {
    if (typeof iso === 'string' && iso.includes('T')) {
      return new Date(iso).toLocaleDateString('sk-SK')
    }
    return new Date(iso).toLocaleString('sk-SK')
  } catch { return '-' }
}

const getDocumentTypeLabel = (type) => {
  const types = {
    'dohoda_o_praxi': t('companyDocuments.types.agreement'),
    'agreement_signed': t('companyDocuments.types.agreementSigned'),
    'vykaz_praxe_scan': t('companyDocuments.types.reportScan'),
    'potvrdenie': t('companyDocuments.types.confirmation'),
    'dochadzka': t('companyDocuments.types.attendance'),
  }
  return types[type] || type
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

const getCompanyStatusClass = (status) => {
  if (status === 'schválený') return 'bg-success'
  if (status === 'zamietnutý') return 'bg-danger'
  return 'bg-secondary'
}

function getEvaluationCriterionLabel(key) {
  const labels = {
    'organizovanie_a_planovanie_prace': t('companyDocuments.report.evaluation.organizovanie'),
    'schopnost_pracovat_v_time': t('companyDocuments.report.evaluation.teamWork'),
    'schopnost_ucit_sa': t('companyDocuments.report.evaluation.learning'),
    'uroven_digitalnej_gramotnosti': t('companyDocuments.report.evaluation.digital'),
    'kultivovanost_prejavu': t('companyDocuments.report.evaluation.expression'),
    'pouzivanie_zauzivanych_vyrazov': t('companyDocuments.report.evaluation.terms'),
    'prezentovanie': t('companyDocuments.report.evaluation.presentation'),
    'samostatnost': t('companyDocuments.report.evaluation.independence'),
    'adaptabilita': t('companyDocuments.report.evaluation.adaptability'),
    'flexibilita': t('companyDocuments.report.evaluation.flexibility'),
    'schopnost_improvizovat': t('companyDocuments.report.evaluation.improvisation'),
    'schopnost_prijimat_rozhodnutia': t('companyDocuments.report.evaluation.decisions'),
    'schopnost_niest_zodpovednost': t('companyDocuments.report.evaluation.responsibility'),
    'dodrzovanie_etickych_zasad': t('companyDocuments.report.evaluation.ethics'),
    'schopnost_jednania_s_ludmi': t('companyDocuments.report.evaluation.communication')
  }
  return labels[key] || key
}

const load = async () => {
  const resp = await fetch(`/api/company/internships/${internshipId}/documents`, {
    headers: { Authorization: `Bearer ${authStore.token}` }
  })
  const data = await resp.json()
  if (!resp.ok) throw new Error(data.message || t('companyDocuments.loadError'))
  documents.value = data.data || []
}

const loadInternshipInfo = async () => {
  try {
    const resp = await fetch(`/api/company/internships/${internshipId}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    if (!resp.ok) {
      console.error(`Chyba ${resp.status} pri načítaní informácií o praxi`)
      internshipInfo.value = null
      return
    }
    const data = await resp.json()
    internshipInfo.value = data.data
  } catch (e) {
    console.error('Chyba pri načítaní informácií o praxi:', e)
    internshipInfo.value = null
  }
}

const download = async (doc) => {
  const resp = await fetch(`/api/company/internships/${internshipId}/documents/${doc.id}`, {
    headers: { Authorization: `Bearer ${authStore.token}` }
  })
  if (!resp.ok) {
    const data = await resp.json().catch(() => ({}))
    alert(data.message || `Chyba: ${resp.status}`)
    return
  }
  const blob = await resp.blob()
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = doc.name || 'document.pdf'
  a.click()
  window.URL.revokeObjectURL(url)
}

const openValidateModal = (doc) => {
  selectedDocument.value = doc
  validateAction.value = ''
  rejectionReason.value = ''
  showValidateModal.value = true
}

const closeValidateModal = () => {
  showValidateModal.value = false
  selectedDocument.value = null
  validateAction.value = ''
  rejectionReason.value = ''
}

const showRejectionReason = (doc) => {
  selectedDocument.value = doc
  showRejectionModal.value = true
}

const submitValidation = async () => {
  if (!selectedDocument.value || !validateAction.value) return
  if (validateAction.value === 'reject' && !rejectionReason.value.trim()) {
    alert(t('companyDocuments.rejectionReasonRequired'))
    return
  }

  validating.value = true
  try {
    const resp = await fetch(`/api/company/internships/${internshipId}/documents/${selectedDocument.value.id}/validate`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        action: validateAction.value,
        rejection_reason: validateAction.value === 'reject' ? rejectionReason.value : null,
      })
    })

    const data = await resp.json()
    if (!resp.ok) {
      throw new Error(data.message || t('companyDocuments.validateError'))
    }

    // Update local document
    const docIndex = documents.value.findIndex(d => d.id === selectedDocument.value.id)
    if (docIndex !== -1) {
      documents.value[docIndex].company_status = data.data.company_status
      documents.value[docIndex].company_rejection_reason = data.data.company_rejection_reason
      documents.value[docIndex].company_validated_at = data.data.company_validated_at
    }

    alert(validateAction.value === 'approve' 
      ? t('companyDocuments.documentApproved') 
      : t('companyDocuments.documentRejected'))
    closeValidateModal()
  } catch (e) {
    alert(e.message || t('companyDocuments.validateError'))
  } finally {
    validating.value = false
  }
}

const goBack = () => {
  router.push({ name: 'company-dashboard' })
}

onMounted(async () => {
  await Promise.all([
    load(),
    loadInternshipInfo()
  ])
})
</script>

