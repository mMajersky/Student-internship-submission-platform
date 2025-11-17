<template>
  <div class="company-requests">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="mb-0">Správa žiadostí o firmy</h3>
      
      <!-- Filter Dropdown -->
      <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center">
          <label for="statusFilter" class="me-2 mb-0 text-muted">Filter:</label>
          <select
            id="statusFilter"
            class="form-select form-select-sm"
            v-model="activeFilter"
            style="width: auto; min-width: 180px;"
          >
            <option value="pending">
              <i class="bi bi-clock-history"></i>
              Čakajúce
              <span v-if="stats.pending > 0">({{ stats.pending }})</span>
            </option>
            <option value="approved">
              Schválené
              <span v-if="stats.approved > 0">({{ stats.approved }})</span>
            </option>
            <option value="rejected">
              Zamietnuté
              <span v-if="stats.rejected > 0">({{ stats.rejected }})</span>
            </option>
            <option value="all">
              Všetky
            </option>
          </select>
        </div>
        
        <!-- Stats badges -->
        <div class="d-flex gap-2">
          <span class="badge bg-warning text-dark" v-if="stats.pending > 0">
            <i class="bi bi-clock-history me-1"></i>{{ stats.pending }}
          </span>
          <span class="badge bg-success" v-if="stats.approved > 0">
            <i class="bi bi-check-circle me-1"></i>{{ stats.approved }}
          </span>
          <span class="badge bg-danger" v-if="stats.rejected > 0">
            <i class="bi bi-x-circle me-1"></i>{{ stats.rejected }}
          </span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Načítava sa...</span>
      </div>
      <p class="text-muted mt-2">Načítavajú sa žiadosti...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredRequests.length === 0" class="text-center py-5">
      <i class="bi bi-inbox fs-1 text-muted"></i>
      <p class="text-muted mt-3">
        {{ activeFilter === 'pending' ? 'Žiadne čakajúce žiadosti.' : 'Žiadne žiadosti.' }}
      </p>
    </div>

    <!-- Requests Table -->
    <div v-else class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title mb-0">
            {{ getFilterTitle() }}
          </h5>
          <button
            class="btn btn-sm btn-outline-primary"
            @click="fetchRequests"
            :disabled="isLoading"
          >
            <i class="bi bi-arrow-clockwise me-1"></i>
            Obnoviť
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Firma</th>
                <th>Kontaktná osoba</th>
                <th>Zdroj</th>
                <th>Požiadal</th>
                <th>Dátum</th>
                <th>Stav</th>
                <th>Akcie</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="request in filteredRequests" :key="request.id">
                <td>
                  <strong>{{ request.company_name }}</strong>
                  <br>
                  <small class="text-muted">{{ formatAddress(request) }}</small>
                </td>
                <td>
                  {{ request.contact_person_name }} {{ request.contact_person_surname }}
                  <br>
                  <small class="text-muted">{{ request.contact_person_email }}</small>
                </td>
                <td>
                  <span
                    class="badge"
                    :class="request.request_source === 'public_registration' ? 'bg-info' : 'bg-primary'"
                  >
                    {{ request.request_source === 'public_registration' ? 'Verejná' : 'Študent' }}
                  </span>
                </td>
                <td>
                  <span v-if="request.requested_by">
                    {{ request.requested_by.name }}
                    <br>
                    <small class="text-muted">{{ request.requested_by.email }}</small>
                  </span>
                  <span v-else class="text-muted">-</span>
                </td>
                <td>
                  <small>{{ formatDate(request.created_at) }}</small>
                </td>
                <td>
                  <span class="badge" :class="getStatusClass(request.status)">
                    {{ getStatusText(request.status) }}
                  </span>
                </td>
                <td>
                  <button
                    class="btn btn-sm btn-outline-info me-1"
                    title="Zobraziť detaily"
                    @click="viewDetails(request)"
                  >
                    <i class="bi bi-eye"></i>
                  </button>
                  <button
                    v-if="request.status === 'pending'"
                    class="btn btn-sm btn-outline-success me-1"
                    title="Schváliť"
                    @click="approveRequest(request.id)"
                  >
                    <i class="bi bi-check-circle"></i>
                  </button>
                  <button
                    v-if="request.status === 'pending'"
                    class="btn btn-sm btn-outline-danger"
                    title="Zamietnuť"
                    @click="rejectRequest(request.id)"
                  >
                    <i class="bi bi-x-circle"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div
      v-if="selectedRequest"
      class="modal fade show"
      style="display: block;"
      tabindex="-1"
      @click.self="selectedRequest = null"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail žiadosti o firmu</h5>
            <button type="button" class="btn-close" @click="selectedRequest = null"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-12">
                <h6 class="text-muted">Informácie o firme</h6>
                <table class="table table-sm">
                  <tr>
                    <th style="width: 30%;">Názov firmy:</th>
                    <td>{{ selectedRequest.company_name }}</td>
                  </tr>
                  <tr>
                    <th>Štát:</th>
                    <td>{{ selectedRequest.state || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Región:</th>
                    <td>{{ selectedRequest.region || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Mesto:</th>
                    <td>{{ selectedRequest.city || '-' }}</td>
                  </tr>
                  <tr>
                    <th>PSČ:</th>
                    <td>{{ selectedRequest.postal_code || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Ulica:</th>
                    <td>{{ selectedRequest.street || '-' }}</td>
                  </tr>
                  <tr>
                    <th>Číslo domu:</th>
                    <td>{{ selectedRequest.house_number || '-' }}</td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-12">
                <h6 class="text-muted">Kontaktná osoba</h6>
                <table class="table table-sm">
                  <tr>
                    <th style="width: 30%;">Meno:</th>
                    <td>{{ selectedRequest.contact_person_name }} {{ selectedRequest.contact_person_surname }}</td>
                  </tr>
                  <tr>
                    <th>Email:</th>
                    <td>{{ selectedRequest.contact_person_email }}</td>
                  </tr>
                  <tr>
                    <th>Telefón:</th>
                    <td>{{ selectedRequest.contact_person_phone || '-' }}</td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-12">
                <h6 class="text-muted">Informácie o žiadosti</h6>
                <table class="table table-sm">
                  <tr>
                    <th style="width: 30%;">Zdroj žiadosti:</th>
                    <td>
                      <span
                        class="badge"
                        :class="selectedRequest.request_source === 'public_registration' ? 'bg-info' : 'bg-primary'"
                      >
                        {{ selectedRequest.request_source === 'public_registration' ? 'Verejná registrácia' : 'Žiadosť študenta' }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="selectedRequest.requested_by">
                    <th>Požiadal:</th>
                    <td>{{ selectedRequest.requested_by.name }} ({{ selectedRequest.requested_by.email }})</td>
                  </tr>
                  <tr>
                    <th>Dátum vytvorenia:</th>
                    <td>{{ formatDate(selectedRequest.created_at) }}</td>
                  </tr>
                  <tr>
                    <th>Stav:</th>
                    <td>
                      <span class="badge" :class="getStatusClass(selectedRequest.status)">
                        {{ getStatusText(selectedRequest.status) }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="selectedRequest.reviewed_by">
                    <th>Posúdil:</th>
                    <td>{{ selectedRequest.reviewed_by.name }}</td>
                  </tr>
                  <tr v-if="selectedRequest.reviewed_at">
                    <th>Dátum posúdenia:</th>
                    <td>{{ formatDate(selectedRequest.reviewed_at) }}</td>
                  </tr>
                  <tr v-if="selectedRequest.rejection_reason">
                    <th>Dôvod zamietnutia:</th>
                    <td class="text-danger">{{ selectedRequest.rejection_reason }}</td>
                  </tr>
                  <tr v-if="selectedRequest.company_id">
                    <th>ID firmy:</th>
                    <td>{{ selectedRequest.company_id }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              v-if="selectedRequest.status === 'pending'"
              class="btn btn-success"
              @click="approveRequest(selectedRequest.id)"
            >
              <i class="bi bi-check-circle me-2"></i>
              Schváliť
            </button>
            <button
              v-if="selectedRequest.status === 'pending'"
              class="btn btn-danger"
              @click="rejectRequest(selectedRequest.id)"
            >
              <i class="bi bi-x-circle me-2"></i>
              Zamietnuť
            </button>
            <button class="btn btn-secondary" @click="selectedRequest = null">Zavrieť</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Backdrop -->
    <div v-if="selectedRequest" class="modal-backdrop fade show"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()

const requests = ref([])
const isLoading = ref(false)
const activeFilter = ref('pending')
const selectedRequest = ref(null)

// Statistics
const stats = computed(() => ({
  pending: requests.value.filter(r => r.status === 'pending').length,
  approved: requests.value.filter(r => r.status === 'approved').length,
  rejected: requests.value.filter(r => r.status === 'rejected').length
}))

// Filtered requests
const filteredRequests = computed(() => {
  if (activeFilter.value === 'all') {
    return requests.value
  }
  return requests.value.filter(r => r.status === activeFilter.value)
})

// Fetch requests
const fetchRequests = async () => {
  isLoading.value = true

  try {
    const response = await fetch(`/api/company-requests?status=${activeFilter.value}`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })

    if (response.ok) {
      const data = await response.json()
      requests.value = data.data || []
    } else {
      console.error('Failed to fetch company requests:', response.status)
    }
  } catch (error) {
    console.error('Error fetching company requests:', error)
  } finally {
    isLoading.value = false
  }
}

// Approve request
const approveRequest = async (requestId) => {
  if (!confirm('Naozaj chcete schváliť túto žiadosť? Firma bude pridaná do systému.')) {
    return
  }

  try {
    const response = await fetch(`/api/company-requests/${requestId}/approve`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })

    const data = await response.json()

    if (response.ok) {
      alert(`Žiadosť bola schválená! Firma "${data.data.company_name}" bola pridaná do systému.`)
      selectedRequest.value = null
      await fetchRequests()
    } else {
      alert(data.message || 'Nepodarilo sa schváliť žiadosť.')
    }
  } catch (error) {
    console.error('Error approving request:', error)
    alert('Vyskytla sa chyba pri schvaľovaní žiadosti.')
  }
}

// Reject request
const rejectRequest = async (requestId) => {
  const reason = prompt('Zadajte dôvod zamietnutia žiadosti:')

  if (!reason || !reason.trim()) {
    alert('Dôvod zamietnutia je povinný.')
    return
  }

  try {
    const response = await fetch(`/api/company-requests/${requestId}/reject`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        rejection_reason: reason.trim()
      })
    })

    const data = await response.json()

    if (response.ok) {
      alert('Žiadosť bola zamietnutá.')
      selectedRequest.value = null
      await fetchRequests()
    } else {
      alert(data.message || 'Nepodarilo sa zamietnuť žiadosť.')
    }
  } catch (error) {
    console.error('Error rejecting request:', error)
    alert('Vyskytla sa chyba pri zamietaní žiadosti.')
  }
}

// View details
const viewDetails = (request) => {
  selectedRequest.value = request
}

// Helper functions
const getFilterTitle = () => {
  const titles = {
    pending: 'Čakajúce žiadosti',
    approved: 'Schválené žiadosti',
    rejected: 'Zamietnuté žiadosti',
    all: 'Všetky žiadosti'
  }
  return titles[activeFilter.value] || 'Žiadosti'
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-warning text-dark',
    approved: 'bg-success',
    rejected: 'bg-danger'
  }
  return classes[status] || 'bg-secondary'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'Čaká',
    approved: 'Schválené',
    rejected: 'Zamietnuté'
  }
  return texts[status] || status
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('sk-SK', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatAddress = (request) => {
  const parts = [
    request.street,
    request.house_number,
    request.city,
    request.postal_code
  ].filter(Boolean)
  return parts.length > 0 ? parts.join(', ') : 'Adresa neuvedená'
}

// Watch filter changes
watch(activeFilter, () => {
  fetchRequests()
})

// Load requests on mount
onMounted(() => {
  fetchRequests()
})
</script>
