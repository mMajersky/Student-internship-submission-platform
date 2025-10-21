<template>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Úprava oznámenia</h2>
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
            <h5 class="mb-0">Obsah oznámenia</h5>
            <small class="text-muted">Použite editor nižšie na formátovanie textu</small>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleSubmit">
              <div class="mb-3">
                <label class="form-label">Text oznámenia</label>
                <div class="quill-editor-container">
                  <QuillEditor
                    v-model:content="formData.content"
                    contentType="html"
                    :options="editorOptions"
                  />
                </div>
                <div class="form-text">Použite tlačidlá v editore na formátovanie textu.</div>
              </div>

              <div class="mb-3 form-check">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  id="is_published" 
                  v-model="formData.is_published" 
                />
                <label class="form-check-label" for="is_published">
                  Zobraziť oznámenie na hlavnej stránke
                </label>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm" role="status"></span>
                  <i v-else class="bi bi-save me-2"></i>
                  Uložiť zmeny
                </button>
                <button type="button" class="btn btn-secondary" @click="loadCurrentAnnouncement" :disabled="loading">
                  <i class="bi bi-arrow-clockwise me-2"></i>
                  Obnoviť
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
            <h6 class="mb-0">Aktuálne oznámenie</h6>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <div class="mb-2">
                  <span class="badge" :class="currentAnnouncement.is_published ? 'bg-success' : 'bg-secondary'">
                    {{ currentAnnouncement.is_published ? 'Zobrazené' : 'Skryté' }}
                  </span>
                </div>
                <div class="text-muted small">
                  <i class="bi bi-calendar-event me-1"></i>
                  Posledná úprava: {{ formatDate(currentAnnouncement.updated_at) }}
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
import { ref, reactive, onMounted } from 'vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const loading = ref(false)
const error = ref('')
const successMessage = ref('')
const currentAnnouncement = ref(null)

const formData = reactive({
  content: '',
  is_published: true
})

const editorOptions = {
  modules: {
    toolbar: [
      [{ header: [1, 2, 3, false] }],
      ['bold', 'italic', 'underline'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['link'],
      ['clean']
    ]
  },
  placeholder: 'Zadajte obsah oznámenia...',
  theme: 'snow',
}

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
      throw new Error('Nepodarilo sa načítať aktuálne oznámenie')
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
    error.value = 'Obsah oznámenia je povinný'
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
      throw new Error(errorData.message || 'Nepodarilo sa uložiť oznámenie')
    }

    const updatedAnnouncement = await response.json()
    currentAnnouncement.value = updatedAnnouncement
    successMessage.value = 'Oznámenie bolo úspešne uložené!'
    
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

<style scoped>
.quill-editor-container {
  height: 300px;
}
</style>
