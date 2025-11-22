<template>
  <div id="app-wrapper" class="p-4">
    <div class="container">
      <h5 class="mb-4">
        <i class="bi bi-file-earmark-text me-2"></i> {{ $t('studentDocuments.title') }}
      </h5>

      <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
          <a class="nav-link active" href="#">{{ $t('studentDocuments.tabs.documents') }}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-muted" href="#">{{ $t('studentDocuments.tabs.overview') }}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-muted" href="#">{{ $t('studentDocuments.tabs.myInternships') }}</a>
        </li>
      </ul>

      <div class="card p-4">
        <h5 class="mb-4">{{ $t('studentDocuments.documentsTitle') }}</h5>

        <div class="row g-4">
          <!-- Ľavý stĺpec -->
          <div class="col-md-6">
            <div class="border rounded p-4 bg-white h-100">
              <h6 class="fw-semibold mb-3">{{ $t('studentDocuments.generatedAgreement') }}</h6>
              <p class="text-muted small mb-3">
                {{ $t('studentDocuments.generatedAgreementDesc') }}
              </p>
              <button class="btn btn-primary btn-sm" @click="downloadGeneratedAgreement">
                <i class="bi bi-download me-1"></i> {{ $t('studentDocuments.downloadAgreement') }}
              </button>

              <div class="alert alert-info mt-4" role="alert">
                <h6 class="alert-heading"><i class="bi bi-shield-lock me-2"></i>{{ $t('studentDocuments.securityTitle') }}</h6>
                <p class="mb-0 small">
                  <strong>{{ $t('studentDocuments.securityWhere') }}</strong><br>
                  {{ $t('studentDocuments.securityDesc') }}
                </p>
              </div>
            </div>
          </div>

          <!-- Pravý stĺpec -->
          <div class="col-md-6">
            <div class="border rounded p-4 bg-white mb-4">
              <h6 class="fw-semibold mb-3">
                {{ $t('studentDocuments.signedAgreement') }}
                <small class="text-muted d-block">
                  {{ $t('studentDocuments.signedAgreementDesc') }}
                </small>
              </h6>

              <div class="mb-3">
                <label for="uploadZmluva" class="form-label">{{ $t('studentDocuments.uploadDocument') }}</label>
                <input
                  type="file"
                  id="uploadZmluva"
                  class="form-control form-control-sm"
                  accept="application/pdf"
                  @change="handleFileUpload($event, 'zmluva')"
                />
              </div>
              <button class="btn btn-primary btn-sm" @click="uploadFile('zmluva')">
                {{ $t('studentDocuments.upload') }}
              </button>

              <div class="mt-3" v-if="signedAgreement">
                <div class="alert alert-success py-2 px-3 mb-2">
                  <i class="bi bi-check-circle me-2"></i>
                  {{ $t('studentDocuments.uploaded') }}: <strong>{{ signedAgreement.name }}</strong>
                  <small class="text-muted ms-2">({{ new Date(signedAgreement.created_at).toLocaleString() }})</small>
                </div>
                <div class="d-flex gap-2">
                  <button class="btn btn-success btn-sm" @click="downloadSignedAgreement">
                    <i class="bi bi-download me-1"></i> {{ $t('studentDocuments.download') }}
                  </button>
                  <button class="btn btn-danger btn-sm" @click="deleteSignedAgreement">
                    <i class="bi bi-trash me-1"></i> {{ $t('studentDocuments.delete') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Výkaz praxe sekcia -->
            <div class="border rounded p-4 bg-white mb-4">
              <h6 class="fw-semibold mb-3">{{ $t('studentDocuments.internshipReport') }}</h6>
              <p class="text-muted small mb-3">
                {{ $t('studentDocuments.internshipReportDesc') }}
              </p>

              <!-- Loading state -->
              <div v-if="!internshipInfo" class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                  <span class="visually-hidden">Načítava sa...</span>
                </div>
                <small class="d-block text-muted mt-2">Načítavajú sa informácie o praxi...</small>
              </div>

              <!-- Status výkazu -->
              <template v-else>
              <div class="mb-3" v-if="reportSubmitted">
                <div class="alert alert-success py-2 px-3 mb-2">
                  <i class="bi bi-check-circle me-2"></i>
                  <strong>{{ $t('studentDocuments.reportStatus') }}:</strong> {{ $t('studentDocuments.reportSubmitted') }}
                  <br>
                  <small class="text-muted">
                    {{ $t('studentDocuments.reportSubmittedAt') }} 
                    {{ new Date(internshipInfo.internship_report.submitted_at).toLocaleString() }}
                  </small>
                </div>
                <button 
                  class="btn btn-outline-primary btn-sm" 
                  @click="showReportModal = true"
                >
                  <i class="bi bi-eye me-1"></i>
                  {{ $t('studentDocuments.viewReport') }}
                </button>
              </div>
              <div class="mb-3" v-else-if="reportEmailSent">
                <div class="alert alert-info py-2 px-3 mb-2">
                  <i class="bi bi-envelope-check me-2"></i>
                  {{ $t('studentDocuments.reportEmailSent') }}
                  <br>
                  <small class="text-muted">
                    {{ $t('studentDocuments.reportWaitingForCompany') }}
                  </small>
                </div>
              </div>
              <div class="mb-3" v-else-if="reportUploaded">
                <div class="alert alert-info py-2 px-3 mb-2">
                  <i class="bi bi-file-check me-2"></i>
                  {{ $t('studentDocuments.reportUploaded') }}
                  <br>
                  <small class="text-muted">
                    {{ $t('studentDocuments.reportUploadedInfo') }}
                  </small>
                </div>
              </div>
              <div class="mb-3" v-else>
                <div class="alert alert-warning py-2 px-3 mb-2">
                  <i class="bi bi-clock me-2"></i>
                  {{ $t('studentDocuments.reportPending') }}
                </div>
              </div>

              <!-- Dve možnosti: Nahrať PDF ALEBO Odoslať elektronický formulár -->
              <div v-if="!reportSubmitted && !reportEmailSent && !reportUploaded" class="mb-3">
                <p class="text-muted small mb-3">
                  {{ $t('studentDocuments.reportOptionsDescription') }}
                </p>
                
                <!-- Možnosť 1: Nahrať PDF -->
                <div class="border rounded p-3 mb-3" style="background-color: #f8f9fa;">
                  <h6 class="fw-semibold mb-2">
                    <i class="bi bi-file-pdf me-2"></i>
                    {{ $t('studentDocuments.optionUploadPdf') }}
                  </h6>
                  <div v-if="!showUploadForm">
                    <button 
                      class="btn btn-outline-primary btn-sm" 
                      @click="showUploadForm = true"
                      :disabled="reportEmailSent"
                    >
                      <i class="bi bi-upload me-1"></i>
                      {{ $t('studentDocuments.uploadReportDocument') }}
                    </button>
                  </div>
                  <div v-else>
                    <label for="uploadVykaz" class="form-label small">{{ $t('studentDocuments.uploadReportDocument') }}</label>
                    <input
                      type="file"
                      id="uploadVykaz"
                      class="form-control form-control-sm mb-2"
                      accept="application/pdf,image/jpeg,image/jpg,image/png"
                      @change="handleFileUpload($event, 'vykaz')"
                      :disabled="reportEmailSent"
                    />
                    <div class="d-flex gap-2">
                      <button 
                        class="btn btn-primary btn-sm" 
                        @click="uploadFile('vykaz')"
                        :disabled="!vykazFile || isUploadingVykaz || reportEmailSent"
                      >
                        <i class="bi bi-upload me-1"></i>
                        {{ $t('studentDocuments.upload') }}
                      </button>
                      <button 
                        class="btn btn-outline-secondary btn-sm" 
                        @click="showUploadForm = false"
                      >
                        {{ $t('studentDocuments.cancel') }}
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Možnosť 2: Odoslať elektronický formulár -->
                <div class="border rounded p-3" style="background-color: #f8f9fa;">
                  <h6 class="fw-semibold mb-2">
                    <i class="bi bi-envelope-check me-2"></i>
                    {{ $t('studentDocuments.optionSendForm') }}
                  </h6>
                  <button 
                    class="btn btn-outline-info btn-sm" 
                    @click="sendReportToCompany"
                    :disabled="isSendingReport || reportEmailSent || showUploadForm"
                  >
                    <i class="bi bi-envelope-check me-1"></i> 
                    {{ $t('studentDocuments.sendReportToCompany') }}
                  </button>
                  <small class="d-block text-muted mt-2">
                    {{ $t('studentDocuments.sendFormDescription') }}
                  </small>
                </div>
              </div>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modál pre zobrazenie výkazu praxe -->
    <div v-if="showReportModal" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true" @click.self="showReportModal = false">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center">
              <i class="bi bi-file-text me-2"></i>
              {{ $t('studentDocuments.reportContent') }}
            </h5>
            <button type="button" class="btn-close" :aria-label="$t('studentDocuments.close')" @click="showReportModal = false"></button>
          </div>
          <div class="modal-body" v-if="internshipInfo && internshipInfo.internship_report">
            <!-- Základné informácie -->
            <div class="row mb-3">
              <div class="col-md-6">
                <strong>{{ $t('studentDocuments.report.studentName') }}:</strong>
                <p class="mb-1">{{ internshipInfo.internship_report?.student_name || '-' }}</p>
              </div>
              <div class="col-md-6">
                <strong>{{ $t('studentDocuments.report.studentProgram') }}:</strong>
                <p class="mb-1">{{ internshipInfo.internship_report?.student_program || '-' }}</p>
              </div>
              <div class="col-md-6">
                <strong>{{ $t('studentDocuments.report.companyName') }}:</strong>
                <p class="mb-1">{{ internshipInfo.internship_report?.company_name || '-' }}</p>
              </div>
              <div class="col-md-6">
                <strong>{{ $t('studentDocuments.report.tutorName') }}:</strong>
                <p class="mb-1">{{ internshipInfo.internship_report?.tutor_name || '-' }}</p>
              </div>
              <div class="col-md-6">
                <strong>{{ $t('studentDocuments.report.totalHours') }}:</strong>
                <p class="mb-1">{{ internshipInfo.internship_report?.total_hours || '-' }}</p>
              </div>
            </div>
            
            <!-- Pracovné činnosti -->
            <div v-if="internshipInfo.internship_report?.activities && internshipInfo.internship_report.activities.length > 0" class="mb-3">
              <h6 class="fw-semibold mb-2">{{ $t('studentDocuments.report.activities') }}</h6>
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>{{ $t('studentDocuments.report.activityDate') }}</th>
                      <th>{{ $t('studentDocuments.report.activityDescription') }}</th>
                      <th>{{ $t('studentDocuments.report.activityHours') }}</th>
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
              <h6 class="fw-semibold mb-2">{{ $t('studentDocuments.report.evaluation') }}</h6>
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
              {{ $t('studentDocuments.close') }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="showReportModal" class="modal-backdrop fade show"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const zmluvaFile = ref(null);
const vykazFile = ref(null);
const route = useRoute();
const authStore = useAuthStore();
const internshipId = ref(route.params.id || route.query.internshipId || null);
const signedAgreement = ref(null);
const reportScan = ref(null);
const internshipInfo = ref(null);
const isSendingReport = ref(false);
const isUploadingVykaz = ref(false);
const showUploadForm = ref(false);
const showReportModal = ref(false);

const loadSignedMeta = async () => {
  if (!internshipId.value || !authStore.token) return;
  
  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/agreement-signed/meta`, {
      headers: { 'Authorization': `Bearer ${authStore.token}` }
    });
    
    if (!resp.ok) {
      console.error(`Chyba ${resp.status} pri načítaní metadát dokumentu`);
      signedAgreement.value = null;
      return;
    }
    
    const data = await resp.json();
    // Backend teraz vracia 200 aj keď dokument neexistuje (document: null)
    signedAgreement.value = data.document;
  } catch (e) { 
    console.error('Chyba pri načítaní metadát dokumentu:', e);
    signedAgreement.value = null;
  }
};

const loadReportScanMeta = async () => {
  if (!internshipId.value || !authStore.token) return;
  
  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/report-scan/meta`, {
      headers: { 'Authorization': `Bearer ${authStore.token}` }
    });
    
    if (!resp.ok) {
      console.error(`Chyba ${resp.status} pri načítaní metadát výkazu`);
      reportScan.value = null;
      return;
    }
    
    const data = await resp.json();
    reportScan.value = data.document;
  } catch (e) { 
    console.error('Chyba pri načítaní metadát výkazu:', e);
    reportScan.value = null;
  }
};

const loadInternshipInfo = async () => {
  if (!internshipId.value || !authStore.token) return;
  
  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}`, {
      headers: { 'Authorization': `Bearer ${authStore.token}` }
    });
    
    if (!resp.ok) {
      console.error(`Chyba ${resp.status} pri načítaní informácií o praxi`);
      internshipInfo.value = null;
      return;
    }
    
    const data = await resp.json();
    internshipInfo.value = data.data;
  } catch (e) { 
    console.error('Chyba pri načítaní informácií o praxi:', e);
    internshipInfo.value = null;
  }
};

const internshipEnded = computed(() => {
  if (!internshipInfo.value || !internshipInfo.value.end_date) return false;
  const endDate = new Date(internshipInfo.value.end_date);
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  return endDate <= today;
});

const evaluationSubmitted = computed(() => {
  return internshipInfo.value?.evaluation?.submitted_at ? true : false;
});

const evaluationEmailSent = computed(() => {
  return internshipInfo.value?.evaluation?.email_sent_at ? true : false;
});

const reportSubmitted = computed(() => {
  return internshipInfo.value?.internship_report?.submitted_at ? true : false;
});

const reportEmailSent = computed(() => {
  return internshipInfo.value?.internship_report?.email_sent_at ? true : false;
});

const reportUploaded = computed(() => {
  // Report is uploaded if report scan document exists (PDF nahraný študentom)
  return reportScan.value !== null;
});

const sendReportToCompany = async () => {
  if (!internshipId.value || !authStore.token) {
    alert(t('studentDocuments.downloadError'));
    return;
  }

  if (!internshipEnded.value) {
    alert(t('studentDocuments.internshipNotEnded'));
    return;
  }

  // TODO: Skontrolovať, či je výkaz nahraný
  // if (!reportExists) {
  //   alert(t('studentDocuments.reportNotUploaded'));
  //   return;
  // }

  isSendingReport.value = true;

  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}/send-report-to-company`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    });

    const data = await resp.json();

    if (!resp.ok) {
      throw new Error(data.message || data.error || t('studentDocuments.sendReportError'));
    }

    alert(t('studentDocuments.sendReportSuccess'));
    await loadInternshipInfo();
    await loadReportScanMeta();
    showUploadForm.value = false;
  } catch (e) {
    alert(e.message || t('studentDocuments.sendReportError'));
  } finally {
    isSendingReport.value = false;
  }
};

onMounted(async () => {
  // Načítaj všetky dáta paralelne pre rýchlejší loading
  await Promise.all([
    loadSignedMeta(),
    loadReportScanMeta(),
    loadInternshipInfo()
  ]);
});

function handleFileUpload(event, type) {
  const file = event.target.files[0];
  if (type === "zmluva") zmluvaFile.value = file;
  else vykazFile.value = file;
}

async function uploadFile(type) {
  const file = type === "zmluva" ? zmluvaFile.value : vykazFile.value;

  if (!internshipId.value) {
    alert(t('studentDocuments.internshipIdMissing'));
    return;
  }

  if (!file) {
    alert(t('studentDocuments.selectFile'));
    return;
  }

  if (!authStore.token) {
    alert(t('studentDocuments.notLoggedIn'));
    return;
  }

  // Handle report scan upload
  if (type === 'vykaz') {
    isUploadingVykaz.value = true;
    
    try {
      const formData = new FormData();
      formData.append('file', file);

      const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/report-scan`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await resp.json();

      if (!resp.ok) {
        throw new Error(data.message || data.error || t('studentDocuments.uploadError'));
      }

      alert(t('studentDocuments.reportUploadSuccess'));
      await loadReportScanMeta();
      vykazFile.value = null;
      showUploadForm.value = false;
      
      // Reset file input
      const fileInput = document.getElementById('uploadVykaz');
      if (fileInput) fileInput.value = '';
    } catch (e) {
      alert(e.message || t('studentDocuments.uploadError'));
    } finally {
      isUploadingVykaz.value = false;
    }
    return;
  }

  try {
    const formData = new FormData();
    formData.append('file', file);

    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/agreement-signed`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`
      },
      body: formData,
    });

    const data = await resp.json();
    if (!resp.ok) {
      throw new Error(data.message || 'Nepodarilo sa nahrať súbor.');
    }

    alert(t('studentDocuments.uploadSuccess'));
    await loadSignedMeta();
  } catch (e) {
    alert(e.message);
  }
}

async function downloadGeneratedAgreement() {
  if (!internshipId.value || !authStore.token) {
    alert(t('studentDocuments.downloadError'));
    return;
  }
  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/agreement-generated`, {
      method: 'GET',
      headers: {
        'Accept': 'application/pdf',
        'Authorization': `Bearer ${authStore.token}`
      }
    });
    if (!resp.ok) {
      const data = await resp.json().catch(() => ({}));
      throw new Error(data.message || `${t('studentDocuments.serverError')} ${resp.status}`);
    }
    const blob = await resp.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Dohoda_o_odbornej_praxi_${internshipId.value}.pdf`;
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (e) {
    alert(e.message);
  }
}

async function downloadSignedAgreement() {
  if (!internshipId.value || !authStore.token) {
    alert(t('studentDocuments.downloadError'));
    return;
  }
  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/agreement-signed`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${authStore.token}`
      }
    });
    if (!resp.ok) {
      const data = await resp.json().catch(() => ({}));
      throw new Error(data.message || `${t('studentDocuments.serverError')} ${resp.status}`);
    }
    const blob = await resp.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = signedAgreement.value?.name || 'Podpisana_dohoda.pdf';
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (e) {
    alert(e.message);
  }
}

async function deleteSignedAgreement() {
  if (!confirm(t('studentDocuments.deleteConfirm'))) {
    return;
  }

  if (!internshipId.value || !authStore.token) {
    alert(t('studentDocuments.downloadError'));
    return;
  }

  try {
    const resp = await fetch(`/api/student/internships/${internshipId.value}/documents/agreement-signed`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    });

    const data = await resp.json();

    if (!resp.ok) {
      throw new Error(data.message || `${t('studentDocuments.serverError')} ${resp.status}`);
    }

    alert(t('studentDocuments.deleteSuccess'));
    signedAgreement.value = null;
  } catch (e) {
    alert(e.message);
  }
}

function formatDate(dateString) {
  if (!dateString) return '-';
  try {
    return new Date(dateString).toLocaleDateString('sk-SK');
  } catch (e) {
    return dateString;
  }
}

function getEvaluationCriterionLabel(key) {
  const labels = {
    'organizovanie_a_planovanie_prace': t('studentDocuments.report.evaluation.organizovanie'),
    'schopnost_pracovat_v_time': t('studentDocuments.report.evaluation.teamWork'),
    'schopnost_ucit_sa': t('studentDocuments.report.evaluation.learning'),
    'uroven_digitalnej_gramotnosti': t('studentDocuments.report.evaluation.digital'),
    'kultivovanost_prejavu': t('studentDocuments.report.evaluation.expression'),
    'pouzivanie_zauzivanych_vyrazov': t('studentDocuments.report.evaluation.terms'),
    'prezentovanie': t('studentDocuments.report.evaluation.presentation'),
    'samostatnost': t('studentDocuments.report.evaluation.independence'),
    'adaptabilita': t('studentDocuments.report.evaluation.adaptability'),
    'flexibilita': t('studentDocuments.report.evaluation.flexibility'),
    'schopnost_improvizovat': t('studentDocuments.report.evaluation.improvisation'),
    'schopnost_prijimat_rozhodnutia': t('studentDocuments.report.evaluation.decisions'),
    'schopnost_niest_zodpovednost': t('studentDocuments.report.evaluation.responsibility'),
    'dodrzovanie_etickych_zasad': t('studentDocuments.report.evaluation.ethics'),
    'schopnost_jednania_s_ludmi': t('studentDocuments.report.evaluation.communication')
  };
  return labels[key] || key;
}
</script>
