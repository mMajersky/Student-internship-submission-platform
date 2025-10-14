<template>
  <div class="manage-announcements">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Správa oznámení</h2>
      <button 
        class="btn btn-primary" 
        @click="showCreateForm = true"
        :disabled="loading"
      >
        <i class="bi bi-plus-circle me-2"></i>
        Nové oznámenie
      </button>
    </div>

    <!-- Error Alert -->
    <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ error }}
      <button type="button" class="btn-close" @click="announcementsStore.clearError()"></button>
    </div>

    <!-- Success Alert -->
    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ successMessage }}
      <button type="button" class="btn-close" @click="successMessage = ''"></button>
    </div>

    <!-- Create/Edit Form -->
    <div v-if="showCreateForm || editingAnnouncement" class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">
          {{ editingAnnouncement ? 'Upraviť oznámenie' : 'Nové oznámenie' }}
        </h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleSubmit">
          <div class="mb-3">
            <label for="content" class="form-label">Obsah oznámenia</label>
            <div class="quill-editor-container">
              <QuillEditor
                v-model:content="formData.content"
                contentType="html"
                :options="editorOptions"
                @ready="onEditorReady"
              />
            </div>
            <div class="form-text">
              Môžete používať základné formátovanie (tučné, kurzíva, nadpisy) alebo priamo HTML kód.
            </div>
          </div>

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              class="form-check-input" 
              id="is_published"
              v-model="formData.is_published"
            >
            <label class="form-check-label" for="is_published">
              Zverejniť oznámenie
            </label>
          </div>

          <div class="d-flex gap-2">
            <button 
              type="submit" 
              class="btn btn-primary"
              :disabled="loading || !formData.content.trim()"
            >
              <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ editingAnnouncement ? 'Uložiť zmeny' : 'Vytvoriť oznámenie' }}
            </button>
            <button 
              type="button" 
              class="btn btn-secondary"
              @click="cancelForm"
              :disabled="loading"
            >
              Zrušiť
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Announcements List -->
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Existujúce oznámenia</h5>
      </div>
      <div class="card-body">
        <div v-if="announcementsStore.loading" class="text-center py-4">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Načítavam...</span>
          </div>
        </div>

        <div v-else-if="announcementsStore.announcements.length === 0" class="text-center py-4 text-muted">
          <i class="bi bi-megaphone fs-1"></i>
          <p class="mt-2">Zatiaľ neboli vytvorené žiadne oznámenia.</p>
        </div>

        <div v-else class="list-group">
          <div 
            v-for="announcement in announcementsStore.announcements" 
            :key="announcement.id"
            class="list-group-item"
          >
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-2">
                  <h6 class="mb-0 me-2">{{ formatDate(announcement.updated_at) }}</h6>
                  <span 
                    class="badge"
                    :class="announcement.is_published ? 'bg-success' : 'bg-secondary'"
                  >
                    {{ announcement.is_published ? 'Zverejnené' : 'Koncept' }}
                  </span>
                </div>
                
                <div class="announcement-preview" v-html="announcement.content_sanitized"></div>
                
                <small class="text-muted">
                  Vytvoril: {{ announcement.creator?.email || 'Neznámy' }}
                  <span v-if="announcement.updater && announcement.updater.id !== announcement.creator?.id">
                    | Upravil: {{ announcement.updater.email }}
                  </span>
                </small>
              </div>
              
              <div class="btn-group-vertical btn-group-sm">
                <button 
                  class="btn btn-outline-primary"
                  @click="editAnnouncement(announcement)"
                  :disabled="loading"
                >
                  <i class="bi bi-pencil"></i>
                </button>
                <button 
                  class="btn btn-outline-danger"
                  @click="deleteAnnouncement(announcement.id)"
                  :disabled="loading"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
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

// Reactive data
const showCreateForm = ref(false)
const editingAnnouncement = ref(null)
const successMessage = ref('')

const formData = reactive({
  content: '',
  is_published: false
})

// Editor options
const editorOptions = {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ 'header': [1, 2, 3, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'list': 'ordered'}, { 'list': 'bullet' }],
      ['link'],
      ['clean']
    ]
  },
  placeholder: 'Zadajte obsah oznámenia...'
}

// Computed
const loading = computed(() => announcementsStore.loading)
const error = computed(() => announcementsStore.error)

// Methods
const onEditorReady = (quill) => {
  // Editor is ready
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('sk-SK')
}

const editAnnouncement = (announcement) => {
  editingAnnouncement.value = announcement
  formData.content = announcement.content
  formData.is_published = announcement.is_published
  showCreateForm.value = false
}

const cancelForm = () => {
  showCreateForm.value = false
  editingAnnouncement.value = null
  formData.content = ''
  formData.is_published = false
  successMessage.value = ''
}

const handleSubmit = async () => {
  try {
    if (editingAnnouncement.value) {
      await announcementsStore.updateAnnouncement(editingAnnouncement.value.id, formData)
      successMessage.value = 'Oznámenie bolo úspešne aktualizované.'
    } else {
      await announcementsStore.createAnnouncement(formData)
      successMessage.value = 'Oznámenie bolo úspešne vytvorené.'
    }
    
    cancelForm()
    
    // Refresh published announcement if this one was published
    if (formData.is_published) {
      await announcementsStore.fetchPublishedAnnouncement()
    }
  } catch (error) {
    console.error('Error saving announcement:', error)
  }
}

const deleteAnnouncement = async (id) => {
  if (confirm('Naozaj chcete odstrániť toto oznámenie?')) {
    try {
      await announcementsStore.deleteAnnouncement(id)
      successMessage.value = 'Oznámenie bolo úspešne odstránené.'
      
      // Refresh published announcement if this was the published one
      await announcementsStore.fetchPublishedAnnouncement()
    } catch (error) {
      console.error('Error deleting announcement:', error)
    }
  }
}

// Lifecycle
onMounted(async () => {
  await announcementsStore.fetchAnnouncements()
})
</script>

<style scoped>
.quill-editor-container {
  min-height: 200px;
}

.announcement-preview {
  max-height: 100px;
  overflow: hidden;
  text-overflow: ellipsis;
}

.announcement-preview :deep(p) {
  margin-bottom: 0.5rem;
}

.announcement-preview :deep(p:last-child) {
  margin-bottom: 0;
}

.list-group-item {
  border-left: none;
  border-right: none;
}

.list-group-item:first-child {
  border-top: none;
}

.list-group-item:last-child {
  border-bottom: none;
}
</style>
