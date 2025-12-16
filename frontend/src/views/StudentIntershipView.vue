<template>
  <div class="min-vh-100 d-flex flex-column bg-light">
    <main class="flex-grow-1 container py-4" style="max-width: 1200px;">
      <h1 class="fs-2 fw-semibold mb-4 text-dark">{{ $t('studentInternship.title') }}</h1>

      <!-- Stats Cards -->
      <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
          <div class="card border shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted small mb-2">{{ $t('studentInternship.stats.active') }}</p>
              <h2 class="display-4 fw-semibold mb-0">{{ stats.aktivne }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card border shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted small mb-2">{{ $t('studentInternship.stats.approved') }}</p>
              <h2 class="display-4 fw-semibold mb-0">{{ stats.schvalene }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card border shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted small mb-2">{{ $t('studentInternship.stats.completed') }}</p>
              <h2 class="display-4 fw-semibold mb-0">{{ stats.obhajene }}</h2>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
        <h2 class="fs-4 fw-semibold mb-0">{{ $t('studentInternship.internships') }}</h2>
        <button @click="handleNovaPrax" class="btn btn-primary d-inline-flex align-items-center gap-2">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          {{ $t('studentInternship.newInternship') }}
        </button>
      </div>

      <!-- Table -->
      <div class="card border shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th class="fw-semibold">{{ $t('studentInternship.tableHeaders.company') }}</th>
                <th class="fw-semibold">{{ $t('studentInternship.tableHeaders.academicYear') }}</th>
                <th class="fw-semibold">{{ $t('studentInternship.tableHeaders.period') }}</th>
                <th class="fw-semibold">{{ $t('studentInternship.tableHeaders.status') }}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="prax in praxe" :key="prax.id">
                <td class="fw-medium">{{ prax.company ? prax.company.name : $t('studentInternship.unknownCompany') }}</td>
                <td>{{ prax.academy_year }}</td>
                <td>{{ prax.start_date }} - {{ prax.end_date }}</td>
                <td>
                  <span v-if="prax.status"
                        class="badge text-uppercase"
                        :class="getStatusBadgeClass(prax.status)">
                    {{ formatStatus(prax.status) }}
                  </span>
                </td>
                <td class="text-end">
                  <button @click="handleZobrazitKomentare(prax)" class="btn btn-sm btn-outline-secondary me-2" :title="$t('studentInternship.actions.viewComments')">
                    <i class="bi bi-chat-left-text"></i>
                    <span v-if="prax.comments_count" class="ms-1">({{ prax.comments_count }})</span>
                  </button>
                  <button @click="handleDokumenty(prax)" class="btn btn-sm btn-outline-secondary">{{ $t('studentInternship.actions.documents') }}</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- Comments Modal -->
    <div v-if="showCommentsModal" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true" @click.self="closeCommentsModal">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title d-flex align-items-center">
              <i class="bi bi-chat-left-text me-2"></i>
              {{ $t('studentInternship.modal.commentsTitle') }}
            </h5>
            <button type="button" class="btn-close" :aria-label="$t('studentInternship.modal.close')" @click="closeCommentsModal"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedPraxForComments" class="alert alert-info border-start border-primary border-4 border-top-0 border-end-0 border-bottom-0 mb-4">
              <p class="mb-1">
                <strong>{{ $t('studentInternship.modal.company') }}:</strong>
                {{ selectedPraxForComments.company ? selectedPraxForComments.company.name : '-' }}
              </p>
              <p class="mb-0">
                <strong>{{ $t('studentInternship.modal.academicYear') }}:</strong>
                {{ selectedPraxForComments.academy_year }}
              </p>
            </div>
            <CommentsSection
              v-if="selectedPraxForComments"
              :internship-id="selectedPraxForComments.id"
              :auth-token="authStore.token"
            />
          </div>
        </div>
      </div>
    </div>
    <div v-if="showCommentsModal" class="modal-backdrop fade show"></div>

    <footer class="bg-white border-top py-3 text-center text-muted small">
      {{ $t('studentInternship.footer') }}
    </footer>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useI18n } from 'vue-i18n';
import CommentsSection from '@/components/student/CommentsSection.vue';

const { t } = useI18n();

const router = useRouter();
const authStore = useAuthStore();

const stats = reactive({
  aktivne: 0,
  schvalene: 0,
  obhajene: 0
});

const praxe = ref([]);

// Comments modal state
const showCommentsModal = ref(false);
const selectedPraxForComments = ref(null);

// Pomocná funkcia na normalizáciu textu (odstráni diakritiku, medzery a prevedie na malé písmená)
const normalize = (text) =>
  text
    ? text.toString().trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
    : '';

const statusTranslationMap = {
  'created': 'studentInternship.status.created',
  'approved by garant': 'studentInternship.status.approvedByGarant',
  'rejected by garant': 'studentInternship.status.rejectedByGarant',
  'confirmed by company': 'studentInternship.status.confirmedByCompany',
  'not confirmed by company': 'studentInternship.status.notConfirmedByCompany',
  'defended by student': 'studentInternship.status.defendedByStudent',
  'not defended by student': 'studentInternship.status.notDefendedByStudent'
};

// Pekné zobrazenie statusu
const formatStatus = (text) => {
  if (!text) return '';
  const translationKey = statusTranslationMap[text] || 'studentInternship.status.created';
  return t(translationKey, text);
};

const getStatusBadgeClass = (status) => {
  const classes = {
    'created': 'bg-secondary',
    'approved by garant': 'bg-info',
    'rejected by garant': 'bg-danger',
    'confirmed by company': 'bg-success',
    'not confirmed by company': 'bg-danger',
    'defended by student': 'bg-primary',
    'not defended by student': 'bg-danger'
  };
  const normalized = normalize(status);
  return classes[normalized] || classes[status] || 'bg-secondary';
};

const loadPraxe = async () => {
  try {
    const token = authStore.token;

    if (!token) {
      console.error('Chýba autentifikačný token.');
      return;
    }

    const response = await fetch('api/student/internships', {
      method: 'GET',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || `Chyba servera: ${response.status}`);
    }

    const responseData = await response.json();

    if (Array.isArray(responseData.data)) {
      const internships = responseData.data;
      praxe.value = internships;

      // VÝPOČET ŠTATISTÍK – nové hodnoty
      stats.aktivne = internships.filter((p) => {
        const status = normalize(p.status);
        return status === 'created' || status === 'approved by garant' || status === 'confirmed by company';
      }).length;

      stats.schvalene = internships.filter((p) => normalize(p.status) === 'confirmed by company').length;

      stats.obhajene = internships.filter((p) => normalize(p.status) === 'defended by student').length;
    }
  } catch (error) {
    console.error('Chyba pri načítaní praxí:', error.message);
  }
};

onMounted(() => {
  loadPraxe();
});

const handleNovaPrax = () => {
  router.push('/create-internship');
};

const handleDokumenty = (prax) => {
  console.log('Otvoriť dokumenty pre prax s ID:', prax.id);
  router.push({
    name: 'upload-documents',
    query: { internshipId: prax.id }
  });
};

const handleZobrazitKomentare = (prax) => {
  selectedPraxForComments.value = prax;
  showCommentsModal.value = true;
};

const closeCommentsModal = () => {
  showCommentsModal.value = false;
  selectedPraxForComments.value = null;
};
</script>
