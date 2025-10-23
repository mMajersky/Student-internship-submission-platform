<template>
  <div class="container">
    <main class="main">
      <h1 class="title">Prehľad študenta</h1>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-label">Aktívne praxe</div>
          <div class="stat-value">{{ stats.aktivne }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Schválené</div>
          <div class="stat-value">{{ stats.schvalene }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Obhájené</div>
          <div class="stat-value">{{ stats.obhajene }}</div>
        </div>
      </div>

      <div class="section-header">
        <h2 class="section-title">Praxe</h2>
        <button @click="handleNovaPrax" class="btn btn-primary">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 3V13M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Nová prax
        </button>
      </div>

      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Firma</th>
              <th>Akademický rok</th>
              <th>Termín</th>
              <th>Stav</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="prax in praxe" :key="prax.id">
              <td class="td-firma">{{ prax.company ? prax.company.name : 'Neznáma firma' }}</td>
              <td>{{ prax.academy_year }}</td>
              <td>{{ prax.start_date }} - {{ prax.end_date }}</td>
              <td>
                <span v-if="prax.status" 
                      class="status-badge" 
                      :class="`status-${normalize(prax.status)}`">
                  {{ formatStatus(prax.status) }}
                </span>
              </td>
              <td class="td-actions">
                <button @click="handleZobrazitKomentare(prax)" class="btn btn-outline me-2" title="Zobraziť komentáre">
                  <i class="bi bi-chat-left-text"></i>
                  <span v-if="prax.comments_count" class="ms-1">({{ prax.comments_count }})</span>
                </button>
                <button @click="handleDokumenty(prax)" class="btn btn-outline">Dokumenty</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- Comments Modal -->
    <div v-if="showCommentsModal" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true" @click.self="closeCommentsModal">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
          <div class="modal-header px-4 py-3">
            <h5 class="modal-title d-flex align-items-center mb-0">
              <i class="bi bi-chat-left-text me-2"></i>
              Komentáre k praxi
            </h5>
            <button type="button" class="btn-close" aria-label="Zavrieť" @click="closeCommentsModal"></button>
          </div>
          <div class="modal-body p-4">
            <div v-if="selectedPraxForComments" class="alert alert-info mb-4 border-start border-primary border-top-0 border-end-0 border-bottom-0 rounded-2" style="border-width: 4px !important;">
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

    <footer class="footer">
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

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  color: #1f2937;
  background-color: #f9fafb;
}

.main {
  flex: 1;
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  padding: 2rem;
}

.title {
  font-size: 1.875rem;
  font-weight: 600;
  margin-bottom: 2rem;
  color: #1f2937;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-bottom: 2.5rem;
}

.stat-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1.5rem;
}

.stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 2.25rem;
  font-weight: 600;
  color: #1f2937;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: all 0.2s;
}

.btn-primary {
  background: #2563eb;
  color: white;
}

.btn-primary:hover {
  background: #1d4ed8;
}

.btn-outline {
  background: white;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-outline:hover {
  background: #f9fafb;
}

.table-container {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  overflow: hidden;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table thead {
  background: #f9fafb;
}

.table th {
  text-align: left;
  padding: 0.875rem 1rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
}

.table td {
  padding: 1rem;
  font-size: 0.875rem;
  color: #1f2937;
  border-bottom: 1px solid #e5e7eb;
}

.td-firma {
  font-weight: 500;
}

.td-actions {
  text-align: right;
}

.status-badge {
  display: inline-block;
  padding: 0.375rem 0.75rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.status-vytvorena {
  background: #6b7280;
  color: white;
}

.status-schvalena {
  background: #10b981;
  color: white;
}

.status-obhajena,
.status-ukoncena {
  background: #3b82f6;
  color: white;
}

.status-prebieha {
  background: #f59e0b;
  color: white;
}

.footer {
  padding: 1.5rem 2rem;
  text-align: center;
  color: #6b7280;
  font-size: 0.875rem;
  border-top: 1px solid #e5e7eb;
  background: white;
}

/* Minimal custom styles for modal - Bootstrap handles most of it */
.modal {
  padding: 1rem;
}

.modal-dialog {
  margin: 1.75rem auto;
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  .table-container {
    overflow-x: auto;
  }
  .modal {
    padding: 0.5rem;
  }
}
</style>
