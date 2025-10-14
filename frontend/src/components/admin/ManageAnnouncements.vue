<template>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Správa oznámení</h2>
      <button class="btn btn-primary" @click="showCreateForm = true" :disabled="loading">
        <i class="bi bi-plus-circle me-2"></i>
        Nové oznámenie
      </button>
    </div>

    <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ error }}
      <button type="button" class="btn-close" @click="announcementsStore.clearError()"></button>
    </div>

    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ successMessage }}
      <button type="button" class="btn-close" @click="successMessage = ''"></button>
    </div>

    <div v-if="showCreateForm || editingAnnouncement" class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">{{ editingAnnouncement ? 'Upraviť oznámenie' : 'Nové oznámenie' }}</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleSubmit">
          <div class="mb-3">
            <label class="form-label">Obsah oznámenia</label>
            <div class="quill-editor-container">
              <QuillEditor
                v-model:content="formData.content"
                contentType="html"
                :options="editorOptions"
              />
            </div>
            <div class="form-text">Základné formátovanie alebo priamo HTML.</div>
          </div>

          <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" id="is_published" v-model="formData.is_published" />
            <label class="form-check-label" for="is_published">Publikovať na hlavnej stránke</label>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm" role="status"></span>
              {{ editingAnnouncement ? 'Uložiť zmeny' : 'Vytvoriť oznámenie' }}
            </button>
            <button type="button" class="btn btn-secondary" @click="resetForm" :disabled="loading">Zrušiť</button>
          </div>
        </form>
      </div>
    </div>

    <h3 class="mb-3">Existujúce oznámenia</h3>
    <div v-if="announcementsStore.loading" class="text-center py-4">
      <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
      <p class="text-muted mt-2">Načítavam oznámenia...</p>
    </div>
    <div v-else-if="announcementsStore.announcements.length === 0" class="text-center py-4 text-muted">
      <p>Žiadne oznámenia zatiaľ neboli vytvorené.</p>
    </div>
    <div v-else class="list-group">
      <div v-for="a in announcementsStore.announcements" :key="a.id" class="list-group-item">
        <div class="d-flex justify-content-between align-items-start">
          <div class="flex-grow-1">
            <span class="badge me-2" :class="a.is_published ? 'bg-success' : 'bg-secondary'">{{ a.is_published ? 'Publikované' : 'Nepublikované' }}</span>
            <div class="text-truncate" style="max-width: 80%" v-html="stripHtmlTags((a.content_sanitized || '').substring(0, 100)) + '...'"></div>
            <div class="small text-muted mt-1">Aktualizované: {{ formatDate(a.updated_at) }}</div>
          </div>
          <div class="btn-group btn-group-sm">
            <button class="btn btn-info" @click="editAnnouncement(a)" :disabled="loading"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-danger" @click="deleteAnnouncement(a.id)" :disabled="loading"><i class="bi bi-trash"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { useAnnouncementsStore } from '@/stores/announcements'

const announcementsStore = useAnnouncementsStore()

const showCreateForm = ref(false)
const editingAnnouncement = ref(null)
const successMessage = ref('')
const error = computed(() => announcementsStore.error)
const loading = computed(() => announcementsStore.loading)

const formData = reactive({
  content: '',
  is_published: false
})

const editorOptions = {
  modules: {
    toolbar: [
      [{ header: [1, 2, 3, false] }],
      ['bold', 'italic', 'underline'],
      ['link'],
      ['clean']
    ]
  },
  placeholder: 'Zadajte obsah oznámenia...',
  theme: 'snow',
}

const formatDate = (d) => (d ? new Date(d).toLocaleString('sk-SK') : '')

const stripHtmlTags = (html) => {
  if (!html) return ''
  const div = document.createElement('div')
  div.innerHTML = html
  return div.textContent || div.innerText || ''
}

const handleSubmit = async () => {
  if (editingAnnouncement.value) {
    await announcementsStore.updateAnnouncement(editingAnnouncement.value.id, formData)
    successMessage.value = 'Oznámenie bolo úspešne aktualizované!'
  } else {
    await announcementsStore.createAnnouncement(formData)
    successMessage.value = 'Nové oznámenie bolo úspešne vytvorené!'
  }
  resetForm()
  await announcementsStore.fetchAnnouncements()
  await announcementsStore.fetchPublishedAnnouncement()
}

const editAnnouncement = (a) => {
  editingAnnouncement.value = { ...a }
  formData.content = a.content
  formData.is_published = a.is_published
  showCreateForm.value = true
}

const deleteAnnouncement = async (id) => {
  await announcementsStore.deleteAnnouncement(id)
  successMessage.value = 'Oznámenie bolo úspešne zmazané!'
}

const resetForm = () => {
  editingAnnouncement.value = null
  formData.content = ''
  formData.is_published = false
  showCreateForm.value = false
  announcementsStore.clearError()
}

onMounted(() => {
  announcementsStore.fetchAnnouncements()
})
</script>

<style scoped>
.quill-editor-container { height: 200px; }
.text-truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>


