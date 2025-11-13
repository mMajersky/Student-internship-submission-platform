<template>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>{{ $t('editAnnouncement.title') }}</h2>
    </div>

    <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ error }}
      <button type="button" class="btn-close" @click="clearError"></button>
    </div>

    <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
      {{ successMessage }}
      <button type="button" class="btn-close" @click="successMessage = ''"></button>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">{{ $t('editAnnouncement.content') }}</h5>
            <small class="text-muted">{{ $t('editAnnouncement.contentDesc') }}</small>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleSubmit">
              <div class="mb-3">
                <label class="form-label">{{ $t('editAnnouncement.text') }}</label>
                <div class="quill-editor-container">
                  <QuillEditor
                    :key="locale"
                    v-model:content="formData.content"
                    contentType="html"
                    :options="editorOptions"
                  />
                </div>
                <div class="form-text">{{ $t('editAnnouncement.editorHelp') }}</div>
              </div>

              <div class="mb-3 form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="is_published"
                  v-model="formData.is_published"
                />
                <label class="form-check-label" for="is_published">
                  {{ $t('editAnnouncement.showOnMainPage') }}
                </label>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm" role="status"></span>
                  <i v-else class="bi bi-save me-2"></i>
                  {{ $t('editAnnouncement.saveChanges') }}
                </button>
                <button type="button" class="btn btn-secondary" @click="loadCurrentAnnouncement" :disabled="loading">
                  <i class="bi bi-arrow-clockwise me-2"></i>
                  {{ $t('editAnnouncement.refresh') }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Current announcement info -->
    <div v-if="currentAnnouncement && currentAnnouncement.updated_at" class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">{{ $t('editAnnouncement.currentAnnouncement') }}</h6>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <div class="mb-2">
                  <span class="badge" :class="currentAnnouncement.is_published ? 'bg-success' : 'bg-secondary'">
                    {{ currentAnnouncement.is_published ? $t('editAnnouncement.displayed') : $t('editAnnouncement.hidden') }}
                  </span>
                </div>
                <div class="text-muted small">
                  <i class="bi bi-calendar-event me-1"></i>
                  {{ $t('editAnnouncement.lastModified') }}: {{ formatDate(currentAnnouncement.updated_at) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const authStore = useAuthStore()
const loading = ref(false)
const error = ref('')
const successMessage = ref('')
const currentAnnouncement = ref(null)

const formData = reactive({
  content: '',
  is_published: true
})

const editorOptions = computed(() => ({
  modules: {
    toolbar: [
      [{ header: [1, 2, 3, false] }],
      ['bold', 'italic', 'underline'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['link'],
      ['clean']
    ]
  },
  placeholder: t('editAnnouncement.placeholder'),
  theme: 'snow',
}))

const formatDate = (date) => {
  return new Date(date).toLocaleString('sk-SK')
}

const loadCurrentAnnouncement = async () => {
  try {
    loading.value = true
    error.value = ''
    
    const response = await fetch('/api/announcement', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
      },
    })

    if (!response.ok) {
      throw new Error(t('editAnnouncement.loadError'))
    }

    const data = await response.json()
    currentAnnouncement.value = data
    formData.content = data.content || ''
    formData.is_published = data.is_published ?? true
    
  } catch (err) {
    error.value = err.message
    console.error('Error loading announcement:', err)
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  if (!formData.content.trim()) {
    error.value = t('editAnnouncement.contentRequired')
    return
  }

  try {
    loading.value = true
    error.value = ''

    const response = await fetch('/api/announcement', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(formData),
    })

    if (!response.ok) {
      const errorData = await response.json()
      throw new Error(errorData.message || t('editAnnouncement.saveError'))
    }

    const updatedAnnouncement = await response.json()
    currentAnnouncement.value = updatedAnnouncement
    successMessage.value = t('editAnnouncement.saveSuccess')

    // Clear success message after 3 seconds
    setTimeout(() => {
      successMessage.value = ''
    }, 3000)

  } catch (err) {
    error.value = err.message
    console.error('Error saving announcement:', err)
  } finally {
    loading.value = false
  }
}

const clearError = () => {
  error.value = ''
}

onMounted(() => {
  loadCurrentAnnouncement()
})
</script>
