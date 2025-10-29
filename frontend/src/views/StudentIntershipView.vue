<template>
  <div class="min-vh-100 d-flex flex-column bg-light">
    <main class="flex-grow-1 container py-4" style="max-width: 1200px;">
      <h1 class="fs-2 fw-semibold mb-4 text-dark">Prehľad študenta</h1>

      <!-- Stats Cards -->
      <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
          <div class="card border shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted small mb-2">Aktívne praxe</p>
              <h2 class="display-4 fw-semibold mb-0">{{ stats.aktivne }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card border shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted small mb-2">Schválené</p>
              <h2 class="display-4 fw-semibold mb-0">{{ stats.schvalene }}</h2>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card border shadow-sm h-100">
            <div class="card-body">
              <p class="text-muted small mb-2">Obhájené</p>
              <h2 class="display-4 fw-semibold mb-0">{{ stats.obhajene }}</h2>
            </div>
          </div>
        </div>
      </div>

      <!-- Section Header -->
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
        <h2 class="fs-4 fw-semibold mb-0">Praxe</h2>
        <button @click="handleNovaPrax" class="btn btn-primary d-inline-flex align-items-center gap-2">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Nová prax
        </button>
      </div>

      <!-- Table -->
      <div class="card border shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th class="fw-semibold">Firma</th>
                <th class="fw-semibold">Akademický rok</th>
                <th class="fw-semibold">Termín</th>
                <th class="fw-semibold">Stav</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="prax in praxe" :key="prax.id">
                <td class="fw-medium">{{ prax.company ? prax.company.name : 'Neznáma firma' }}</td>
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
                  <button @click="handleZobrazitKomentare(prax)" class="btn btn-sm btn-outline-secondary me-2" title="Zobraziť komentáre">
                    <i class="bi bi-chat-left-text"></i>
                    <span v-if="prax.comments_count" class="ms-1">({{ prax.comments_count }})</span>
                  </button>
                  <button @click="handleDokumenty(prax)" class="btn btn-sm btn-outline-secondary">Dokumenty</button>
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
              Komentáre k praxi
            </h5>
            <button type="button" class="btn-close" aria-label="Zavrieť" @click="closeCommentsModal"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedPraxForComments" class="alert alert-info border-start border-primary border-4 border-top-0 border-end-0 border-bottom-0 mb-4">
              <p class="mb-1">
                <strong>Firma:</strong> 
                {{ selectedPraxForComments.company ? selectedPraxForComments.company.name : '-' }}
              </p>
              <p class="mb-0">
                <strong>Akademický rok:</strong> 
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
      © 2025 Odborná prax CRM
    </footer>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import CommentsSection from '@/components/student/CommentsSection.vue';

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

// Pekné zobrazenie statusu
const formatStatus = (text) => {
  if (!text) return '';
  const t = text.toString().trim().toLowerCase();
  return t.charAt(0).toUpperCase() + t.slice(1);
};

const getStatusBadgeClass = (status) => {
  const normalized = normalize(status);
  const classes = {
    'vytvorena': 'bg-secondary',
    'schvalena': 'bg-success',
    'obhajena': 'bg-primary',
    'ukoncena': 'bg-primary',
    'prebieha': 'bg-warning text-dark',
    'zamietnuta': 'bg-danger',
    'zrusena': 'bg-danger'
  };
  return classes[normalized] || 'bg-secondary';
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

      // VÝPOČET ŠTATISTÍK – normalizované porovnanie
      stats.aktivne = internships.filter((p) => {
        const status = normalize(p.status);
        return status === 'vytvorena' || status === 'prebieha';
      }).length;

      stats.schvalene = internships.filter((p) => normalize(p.status) === 'schvalena').length;

      stats.obhajene = internships.filter((p) => {
        const status = normalize(p.status);
        return status === 'obhajena' || status === 'ukoncena';
      }).length;
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

