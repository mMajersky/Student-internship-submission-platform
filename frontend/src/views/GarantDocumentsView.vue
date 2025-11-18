<template>
  <div class="container py-4">
    <h5 class="mb-3">{{ $t('garantDocuments.title') }}</h5>

    <div class="card p-3">
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>{{ $t('garantDocuments.tableHeaders.name') }}</th>
              <th>{{ $t('garantDocuments.tableHeaders.type') }}</th>
              <th>{{ $t('garantDocuments.tableHeaders.status') }}</th>
              <th>{{ $t('garantDocuments.tableHeaders.created') }}</th>
              <th class="text-end">{{ $t('garantDocuments.tableHeaders.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="doc in documents" :key="doc.id">
              <td>{{ doc.name || '-' }}</td>
              <td>{{ doc.type }}</td>
              <td>{{ doc.status || '-' }}</td>
              <td>{{ formatDate(doc.created_at) }}</td>
              <td class="text-end">
                <button @click="download(doc)" class="btn btn-sm btn-outline-primary">
                  {{ $t('garantDocuments.download') }}
                </button>
              </td>
            </tr>
            <tr v-if="!documents.length">
              <td colspan="5" class="text-center text-muted py-4">{{ $t('garantDocuments.noDocuments') }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
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

const formatDate = (iso) => {
  if (!iso) return '-'
  try {
    return new Date(iso).toLocaleString()
  } catch { return '-' }
}

const load = async () => {
  const resp = await fetch(`/api/internships/${internshipId}/documents`, {
    headers: { Authorization: `Bearer ${authStore.token}` }
  })
  const data = await resp.json()
  if (!resp.ok) throw new Error(data.message || t('garantDocuments.loadError'))
  documents.value = data.data || []
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

onMounted(load)
</script>
