<template>
  <div class="container py-4">
    <h5 class="mb-3">{{ $t('garantDocuments.title') }}</h5>

    <div class="card p-3 mb-4">
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>{{ $t('garantDocuments.tableHeaders.name') }}</th>
              <th>{{ $t('garantDocuments.tableHeaders.type') }}</th>
              <th>{{ $t('garantDocuments.tableHeaders.status') }}</th>
              <th>{{ $t('garantDocuments.tableHeaders.companyStatus') }}</th>
              <th>{{ $t('garantDocuments.tableHeaders.created') }}</th>
              <th class="text-end">{{ $t('garantDocuments.tableHeaders.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="doc in documents" :key="doc.id">
              <td>{{ doc.name || '-' }}</td>
              <td>{{ doc.type }}</td>
              <td>{{ doc.status || '-' }}</td>
              <td>
                <span v-if="doc.company_status" class="badge" :class="getCompanyStatusClass(doc.company_status)">
                  {{ doc.company_status }}
                </span>
                <span v-else class="badge bg-secondary">{{ $t('garantDocuments.notValidated') }}</span>
                <button 
                  v-if="doc.company_status === 'zamietnutý'" 
                  @click="showRejectionReason(doc)" 
                  class="btn btn-sm btn-link text-danger p-0 ms-1"
                  :title="$t('garantDocuments.viewRejectionReason')"
                >
                  <i class="bi bi-info-circle"></i>
                </button>
              </td>
              <td>{{ formatDate(doc.created_at) }}</td>
              <td class="text-end">
                <button @click="download(doc)" class="btn btn-sm btn-outline-primary">
                  {{ $t('garantDocuments.download') }}
                </button>
              </td>
            </tr>
            <tr v-if="!documents.length">
              <td colspan="6" class="text-center text-muted py-4">{{ $t('garantDocuments.noDocuments') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div v-if="showRejectionModal" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true" @click.self="showRejectionModal = false">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">{{ $t('garantDocuments.rejectionReasonTitle') }}</h5>
            <button type="button" class="btn-close btn-close-white" @click="showRejectionModal = false"></button>
          </div>
          <div class="modal-body">
            <p><strong>{{ $t('garantDocuments.documentName') }}:</strong> {{ selectedDocument?.name }}</p>
            <p><strong>{{ $t('garantDocuments.rejectedAt') }}:</strong> {{ formatDate(selectedDocument?.company_validated_at) }}</p>
            <hr>
            <p><strong>{{ $t('garantDocuments.reason') }}:</strong></p>
            <p class="text-muted">{{ selectedDocument?.company_rejection_reason }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showRejectionModal = false">
              {{ $t('garantDocuments.close') }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showRejectionModal" class="modal-backdrop fade show"></div>

    <!-- Výkaz praxe sekcia -->
    <div class="card p-3 mb-3" v-if="internshipInfo && internshipInfo.internship_report && internshipInfo.internship_report.submitted_at">
      <h6 class="fw-semibold mb-3">
        <i class="bi bi-file-text me-2"></i>
        {{ $t('garantDocuments.internshipReport') }}
      </h6>
      
      <div class="alert alert-success py-2 px-3 mb-3">
        <i class="bi bi-check-circle me-2"></i>
        <strong>{{ $t('garantDocuments.reportStatus') }}:</strong> {{ $t('garantDocuments.reportSubmitted') }}
        <br>
        <small class="text-muted">
          {{ $t('garantDocuments.reportSubmittedAt') }} 
          {{ formatDate(internshipInfo.internship_report.submitted_at) }}
        </small>
      </div>
      <button 
        class="btn btn-outline-primary btn-sm" 
        @click="showReportModal = true"
      >
        <i class="bi bi-eye me-1"></i>
        {{ $t('garantDocuments.viewReport') }}
      </button>
    </div>
  </div>

  <!-- Modál pre zobrazenie výkazu praxe -->
  <div v-if="showReportModal" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true" @click.self="showReportModal = false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title d-flex align-items-center">
            <i class="bi bi-file-text me-2"></i>
            {{ $t('garantDocuments.reportContent') }}
          </h5>
          <button type="button" class="btn-close" :aria-label="$t('garantDocuments.close')" @click="showReportModal = false"></button>
        </div>
        <div class="modal-body" v-if="internshipInfo && internshipInfo.internship_report">
          <!-- Základné informácie -->
          <div class="row mb-3">
            <div class="col-md-6">
              <strong>{{ $t('garantDocuments.report.studentName') }}:</strong>
              <p class="mb-1">{{ internshipInfo.internship_report?.student_name || '-' }}</p>
            </div>
            <div class="col-md-6">
              <strong>{{ $t('garantDocuments.report.studentProgram') }}:</strong>
              <p class="mb-1">{{ internshipInfo.internship_report?.student_program || '-' }}</p>
            </div>
            <div class="col-md-6">
              <strong>{{ $t('garantDocuments.report.companyName') }}:</strong>
              <p class="mb-1">{{ internshipInfo.internship_report?.company_name || '-' }}</p>
            </div>
            <div class="col-md-6">
              <strong>{{ $t('garantDocuments.report.tutorName') }}:</strong>
              <p class="mb-1">{{ internshipInfo.internship_report?.tutor_name || '-' }}</p>
            </div>
            <div class="col-md-6">
              <strong>{{ $t('garantDocuments.report.totalHours') }}:</strong>
              <p class="mb-1">{{ internshipInfo.internship_report?.total_hours || '-' }}</p>
            </div>
          </div>
          
          <!-- Pracovné činnosti -->
          <div v-if="internshipInfo.internship_report?.activities && internshipInfo.internship_report.activities.length > 0" class="mb-3">
            <h6 class="fw-semibold mb-2">{{ $t('garantDocuments.report.activities') }}</h6>
            <div class="table-responsive">
              <table class="table table-sm table-bordered">
                <thead class="table-light">
                  <tr>
                    <th>{{ $t('garantDocuments.report.activityDate') }}</th>
                    <th>{{ $t('garantDocuments.report.activityDescription') }}</th>
                    <th>{{ $t('garantDocuments.report.activityHours') }}</th>
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
            <h6 class="fw-semibold mb-2">{{ $t('garantDocuments.report.evaluation') }}</h6>
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
            {{ $t('garantDocuments.close') }}
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-if="showReportModal" class="modal-backdrop fade show"></div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const route = useRoute()
const authStore = useAuthStore()
const internshipId = route.params.id
const documents = ref([])
const internshipInfo = ref(null)
const showReportModal = ref(false)
const showRejectionModal = ref(false)
const selectedDocument = ref(null)

const formatDate = (iso) => {
  if (!iso) return '-'
  try {
    if (typeof iso === 'string' && iso.includes('T')) {
      return new Date(iso).toLocaleDateString('sk-SK')
    }
    return new Date(iso).toLocaleString('sk-SK')
  } catch { return '-' }
}

function getEvaluationCriterionLabel(key) {
  const labels = {
    'organizovanie_a_planovanie_prace': t('garantDocuments.report.evaluation.organizovanie'),
    'schopnost_pracovat_v_time': t('garantDocuments.report.evaluation.teamWork'),
    'schopnost_ucit_sa': t('garantDocuments.report.evaluation.learning'),
    'uroven_digitalnej_gramotnosti': t('garantDocuments.report.evaluation.digital'),
    'kultivovanost_prejavu': t('garantDocuments.report.evaluation.expression'),
    'pouzivanie_zauzivanych_vyrazov': t('garantDocuments.report.evaluation.terms'),
    'prezentovanie': t('garantDocuments.report.evaluation.presentation'),
    'samostatnost': t('garantDocuments.report.evaluation.independence'),
    'adaptabilita': t('garantDocuments.report.evaluation.adaptability'),
    'flexibilita': t('garantDocuments.report.evaluation.flexibility'),
    'schopnost_improvizovat': t('garantDocuments.report.evaluation.improvisation'),
    'schopnost_prijimat_rozhodnutia': t('garantDocuments.report.evaluation.decisions'),
    'schopnost_niest_zodpovednost': t('garantDocuments.report.evaluation.responsibility'),
    'dodrzovanie_etickych_zasad': t('garantDocuments.report.evaluation.ethics'),
    'schopnost_jednania_s_ludmi': t('garantDocuments.report.evaluation.communication')
  };
  return labels[key] || key;
}

const getCompanyStatusClass = (status) => {
  if (status === 'schválený') return 'bg-success'
  if (status === 'zamietnutý') return 'bg-danger'
  return 'bg-secondary'
}

const showRejectionReason = (doc) => {
  selectedDocument.value = doc
  showRejectionModal.value = true
}

const load = async () => {
  const resp = await fetch(`/api/internships/${internshipId}/documents`, {
    headers: { Authorization: `Bearer ${authStore.token}` }
  })
  const data = await resp.json()
  if (!resp.ok) throw new Error(data.message || t('garantDocuments.loadError'))
  documents.value = data.data || []
}

const loadInternshipInfo = async () => {
  try {
    const resp = await fetch(`/api/internships/${internshipId}`, {
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
  const resp = await fetch(`/api/internships/${internshipId}/documents/${doc.id}`, {
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

onMounted(async () => {
  await Promise.all([
    load(),
    loadInternshipInfo()
  ])
})
</script>
