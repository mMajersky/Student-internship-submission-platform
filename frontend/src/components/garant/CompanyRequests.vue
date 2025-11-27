<template>
  <div class="company-requests">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="mb-0">{{ t('companyRequests.title') }}</h3>
      
      <!-- Filter Dropdown -->
      <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center">
          <label for="statusFilter" class="me-2 mb-0 text-muted">{{ t('companyRequests.filter.label') }}</label>
          <select
            id="statusFilter"
            class="form-select form-select-sm"
            v-model="activeFilter"
            style="width: auto; min-width: 180px;"
          >
            <option value="pending">{{ t('companyRequests.filter.pending') }} <span v-if="stats.pending > 0">({{ stats.pending }})</span></option>
            <option value="accepted">{{ t('companyRequests.filter.accepted') }} <span v-if="stats.accepted > 0">({{ stats.accepted }})</span></option>
            <option value="declined">{{ t('companyRequests.filter.declined') }} <span v-if="stats.declined > 0">({{ stats.declined }})</span></option>
            <option value="all">{{ t('companyRequests.filter.all') }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">{{ t('companyRequests.loadingAria') }}</span>
      </div>
      <p class="text-muted mt-2">{{ t('companyRequests.loadingText') }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredRequests.length === 0" class="text-center py-5">
      <i class="bi bi-inbox fs-1 text-muted"></i>
      <p class="text-muted mt-3">
        {{ activeFilter === 'pending' ? t('companyRequests.empty.pending') : t('companyRequests.empty.none') }}
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
            {{ t('companyRequests.refresh') }}
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>{{ t('companyRequests.table.company') }}</th>
                <th>{{ t('companyRequests.table.contactPerson') }}</th>
                <th>{{ t('companyRequests.table.source') }}</th>
                <th>{{ t('companyRequests.table.requestedBy') }}</th>
                <th>{{ t('companyRequests.table.date') }}</th>
                <th>{{ t('companyRequests.table.status') }}</th>
                <th>{{ t('companyRequests.table.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="request in filteredRequests" :key="request.id">
                <td>
                  <strong>{{ request.name }}</strong>
                  <br>
                  <small class="text-muted">{{ formatAddress(request) }}</small>
                </td>
                <td>
                  <span v-if="request.contact_person">
                    {{ request.contact_person.name }} {{ request.contact_person.surname }}
                    <br>
                    <small class="text-muted">{{ request.contact_person.email || '-' }}</small>
                  </span>
                  <span v-else class="text-muted">{{ t('companyRequests.noContactPerson') }}</span>
                </td>
                <td>
                  <span
                    class="badge"
                    :class="request.request_source === 'public_registration' ? 'bg-info' : 'bg-primary'"
                  >
                    {{ request.request_source === 'public_registration' ? t('companyRequests.source.public') : t('companyRequests.source.student') }}
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
                    :title="t('companyRequests.actions.view')"
                    @click="viewDetails(request)"
                  >
                    <i class="bi bi-eye"></i>
                  </button>
                  <button
                    v-if="request.status === 'pending'"
                    class="btn btn-sm btn-outline-success me-1"
                    :title="t('companyRequests.actions.approve')"
                    @click="approveRequest(request.id)"
                  >
                    <i class="bi bi-check-circle"></i>
                  </button>
                  <button
                    v-if="request.status === 'pending'"
                    class="btn btn-sm btn-outline-danger"
                    :title="t('companyRequests.actions.reject')"
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
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header bg-light">
            <h5 class="modal-title">
              <i class="bi bi-building me-2"></i>
              {{ t('companyRequests.modal.title') }}
            </h5>
            <button type="button" class="btn-close" @click="selectedRequest = null"></button>
          </div>
          <div class="modal-body">
            <!-- Company Information Card -->
            <div class="card mb-3">
              <div class="card-header bg-primary text-white">
                <i class="bi bi-building me-2"></i>
                {{ t('companyRequests.modal.sections.companyInfo') }}
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-12">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.companyName') }}</label>
                    <p class="mb-0">{{ selectedRequest.name }}</p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.state') }}</label>
                    <p class="mb-0">{{ selectedRequest.state || '-' }}</p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.region') }}</label>
                    <p class="mb-0">{{ selectedRequest.region || '-' }}</p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.city') }}</label>
                    <p class="mb-0">{{ selectedRequest.city || '-' }}</p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.postalCode') }}</label>
                    <p class="mb-0">{{ selectedRequest.postal_code || '-' }}</p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.street') }}</label>
                    <p class="mb-0">{{ selectedRequest.street || '-' }}</p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.houseNumber') }}</label>
                    <p class="mb-0">{{ selectedRequest.house_number || '-' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contact Person Card -->
            <div class="card mb-3">
              <div class="card-header bg-info text-white">
                <i class="bi bi-person me-2"></i>
                {{ t('companyRequests.modal.sections.contact') }}
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-12">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.contactName') }}</label>
                    <p class="mb-0">{{ selectedRequest.contact_person?.name }} {{ selectedRequest.contact_person?.surname }}</p>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.contactEmail') }}</label>
                    <p class="mb-0">
                      <a v-if="selectedRequest.contact_person?.email" :href="`mailto:${selectedRequest.contact_person.email}`">{{ selectedRequest.contact_person.email }}</a>
                      <span v-else>-</span>
                    </p>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.contactPhone') }}</label>
                    <p class="mb-0">{{ selectedRequest.contact_person?.phone || '-' }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Request Information Card -->
            <div class="card mb-3">
              <div class="card-header bg-secondary text-white">
                <i class="bi bi-info-circle me-2"></i>
                {{ t('companyRequests.modal.sections.requestInfo') }}
              </div>
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.requestSource') }}</label>
                    <p class="mb-0">
                      <span
                        class="badge"
                        :class="selectedRequest.request_source === 'public_registration' ? 'bg-info' : 'bg-primary'"
                      >
                        {{ selectedRequest.request_source === 'public_registration' ? t('companyRequests.source.publicRegistration') : t('companyRequests.source.studentRequest') }}
                      </span>
                    </p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.requestedBy') }}</label>
                    <p class="mb-0">{{ selectedRequest.requested_by ? `${selectedRequest.requested_by.name} (${selectedRequest.requested_by.email})` : '-' }}</p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.createdAt') }}</label>
                    <p class="mb-0">{{ formatDate(selectedRequest.created_at) }}</p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.status') }}</label>
                    <p class="mb-0">
                      <span class="badge" :class="getStatusClass(selectedRequest.status)">
                        {{ getStatusText(selectedRequest.status) }}
                      </span>
                    </p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.reviewedBy') }}</label>
                    <p class="mb-0">{{ selectedRequest.reviewed_by ? selectedRequest.reviewed_by.name : '-' }}</p>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.reviewedAt') }}</label>
                    <p class="mb-0">{{ selectedRequest.reviewed_at ? formatDate(selectedRequest.reviewed_at) : '-' }}</p>
                  </div>
                  <div v-if="selectedRequest.rejection_reason" class="col-md-12">
                    <label class="form-label text-muted fw-semibold small">{{ t('companyRequests.modal.fields.rejectionReason') }}</label>
                    <div class="alert alert-danger mb-0">
                      <i class="bi bi-exclamation-triangle me-2"></i>
                      {{ selectedRequest.rejection_reason }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button class="btn btn-secondary" @click="selectedRequest = null">
              <i class="bi bi-x-lg me-2"></i>
              {{ t('companyRequests.actions.close') }}
            </button>
            <button
              v-if="selectedRequest.status === 'pending'"
              class="btn btn-danger"
              @click="rejectRequest(selectedRequest.id)"
            >
              <i class="bi bi-x-circle me-2"></i>
              {{ t('companyRequests.actions.reject') }}
            </button>
            <button
              v-if="selectedRequest.status === 'pending'"
              class="btn btn-success"
              @click="approveRequest(selectedRequest.id)"
            >
              <i class="bi bi-check-circle me-2"></i>
              {{ t('companyRequests.actions.approve') }}
            </button>
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
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()

const requests = ref([])
const isLoading = ref(false)
const activeFilter = ref('pending')
const selectedRequest = ref(null)

// Statistics
const stats = computed(() => ({
  pending: requests.value.filter(r => r.status === 'pending').length,
  accepted: requests.value.filter(r => r.status === 'accepted').length,
  declined: requests.value.filter(r => r.status === 'declined').length
}))

const { t } = useI18n()

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
      console.log('Company requests data:', data)
      console.log('First request:', data.data?.[0])
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
  if (!confirm(t('companyRequests.confirm.approve'))) {
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
      alert(t('companyRequests.alert.accepted', { name: data.data.name }))
      selectedRequest.value = null
      await fetchRequests()
    } else {
      alert(data.message || t('companyRequests.error.approve'))
    }
  } catch (error) {
    console.error('Error approving request:', error)
    alert(t('companyRequests.error.approveUnexpected'))
  }
}

// Reject request
const rejectRequest = async (requestId) => {
  const reason = prompt(t('companyRequests.prompt.rejectionReason'))

  if (!reason || !reason.trim()) {
    alert(t('companyRequests.error.rejectionReasonRequired'))
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
      alert(t('companyRequests.alert.declined'))
      selectedRequest.value = null
      await fetchRequests()
    } else {
      alert(data.message || t('companyRequests.error.reject'))
    }
  } catch (error) {
    console.error('Error rejecting request:', error)
    alert(t('companyRequests.error.rejectUnexpected'))
  }
}

// View details
const viewDetails = (request) => {
  selectedRequest.value = request
}

// Helper functions
const getFilterTitle = () => {
  const titles = {
    pending: t('companyRequests.filter.titles.pending'),
    accepted: t('companyRequests.filter.titles.accepted'),
    declined: t('companyRequests.filter.titles.declined'),
    all: t('companyRequests.filter.titles.all')
  }
  return titles[activeFilter.value] || t('companyRequests.filter.titles.default')
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-warning text-dark',
    accepted: 'bg-success',
    declined: 'bg-danger'
  }
  return classes[status] || 'bg-secondary'
}

const getStatusText = (status) => {
  const texts = {
    pending: t('companyRequests.status.pending'),
    accepted: t('companyRequests.status.accepted'),
    declined: t('companyRequests.status.declined')
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
  return parts.length > 0 ? parts.join(', ') : t('companyRequests.noAddress')
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
