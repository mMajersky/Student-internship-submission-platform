<template>
  <div class="comments-section">
    <div class="comments-header">
      <h5 class="comments-title">
        <i class="bi bi-chat-left-text me-2"></i>
        Komentáre garanta
      </h5>
      <span v-if="comments.length > 0" class="badge bg-primary">
        {{ comments.length }}
      </span>
    </div>

    <div v-if="loading" class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Načítavam...</span>
      </div>
      <p class="text-muted mt-2">Načítavam komentáre...</p>
    </div>

    <div v-else-if="error" class="alert alert-danger" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      {{ error }}
    </div>

    <div v-else-if="comments.length === 0" class="no-comments">
      <i class="bi bi-chat-left text-muted"></i>
      <p class="text-muted mb-0">Zatiaľ nie sú žiadne komentáre.</p>
    </div>

    <div v-else class="comments-list">
      <div 
        v-for="comment in sortedComments" 
        :key="comment.id" 
        class="comment-card"
        :class="`comment-type-${comment.comment_type}`"
      >
        <div class="comment-header">
          <div class="comment-author">
            <i class="bi bi-person-circle me-2"></i>
            <strong>{{ comment.author?.name || 'Garant' }}</strong>
          </div>
          <div class="comment-meta">
            <span class="badge" :class="getCommentTypeBadgeClass(comment.comment_type)">
              {{ getCommentTypeLabel(comment.comment_type) }}
            </span>
            <span class="comment-date">
              <i class="bi bi-clock me-1"></i>
              {{ formatDate(comment.created_at) }}
            </span>
          </div>
        </div>
        <div class="comment-content">
          {{ comment.content }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

const props = defineProps({
  internshipId: {
    type: [Number, String],
    required: true
  },
  authToken: {
    type: String,
    required: true
  }
})

const comments = ref([])
const loading = ref(false)
const error = ref('')

const sortedComments = computed(() => {
  return [...comments.value].sort((a, b) => {
    return new Date(b.created_at) - new Date(a.created_at)
  })
})

const fetchComments = async () => {
  if (!props.internshipId) return

  loading.value = true
  error.value = ''

  try {
    const response = await fetch(`/api/student/internships/${props.internshipId}/comments`, {
      headers: {
        'Authorization': `Bearer ${props.authToken}`,
        'Accept': 'application/json',
      }
    })

    if (!response.ok) {
      const data = await response.json()
      throw new Error(data.message || 'Chyba pri načítaní komentárov')
    }

    const data = await response.json()
    comments.value = data.data || data || []
  } catch (err) {
    console.error('Error fetching comments:', err)
    error.value = err.message || 'Nepodarilo sa načítať komentáre.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchComments()
})

// Watch for internship ID changes
watch(() => props.internshipId, () => {
  fetchComments()
})

const getCommentTypeLabel = (type) => {
  const labels = {
    'approval': 'Schválenie',
    'rejection': 'Zamietnutie',
    'correction': 'Požadovaná oprava',
    'general': 'Všeobecný'
  }
  return labels[type] || type
}

const getCommentTypeBadgeClass = (type) => {
  const classes = {
    'approval': 'bg-success',
    'rejection': 'bg-danger',
    'correction': 'bg-warning text-dark',
    'general': 'bg-info'
  }
  return classes[type] || 'bg-secondary'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  
  const date = new Date(dateString)
  const now = new Date()
  const diffInMs = now - date
  const diffInHours = diffInMs / (1000 * 60 * 60)
  
  // If less than 24 hours ago, show relative time
  if (diffInHours < 24) {
    if (diffInHours < 1) {
      const minutes = Math.floor(diffInMs / (1000 * 60))
      return `pred ${minutes} minútami`
    }
    const hours = Math.floor(diffInHours)
    return `pred ${hours} hodinami`
  }
  
  // Otherwise show full date and time
  return date.toLocaleString('sk-SK', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Expose method to refresh comments
defineExpose({
  fetchComments
})
</script>

<style scoped>
.comments-section {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1.5rem;
}

.comments-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e5e7eb;
}

.comments-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
  display: flex;
  align-items: center;
}

.no-comments {
  text-align: center;
  padding: 3rem 1rem;
}

.no-comments i {
  font-size: 3rem;
  color: #d1d5db;
  display: block;
  margin-bottom: 1rem;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.comment-card {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1rem;
  transition: all 0.2s ease;
}

.comment-card:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.comment-type-approval {
  border-left: 4px solid #10b981;
}

.comment-type-rejection {
  border-left: 4px solid #ef4444;
}

.comment-type-correction {
  border-left: 4px solid #f59e0b;
}

.comment-type-general {
  border-left: 4px solid #3b82f6;
}

.comment-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.comment-author {
  display: flex;
  align-items: center;
  color: #374151;
  font-size: 0.9375rem;
}

.comment-author i {
  font-size: 1.25rem;
  color: #6b7280;
}

.comment-meta {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.comment-date {
  font-size: 0.8125rem;
  color: #6b7280;
  display: flex;
  align-items: center;
}

.comment-content {
  color: #1f2937;
  font-size: 0.9375rem;
  line-height: 1.6;
  white-space: pre-wrap;
  word-wrap: break-word;
}

.badge {
  font-size: 0.75rem;
  padding: 0.375rem 0.75rem;
  border-radius: 0.375rem;
  font-weight: 600;
}

.alert {
  padding: 1rem;
  border-radius: 0.5rem;
  margin-bottom: 0;
}

.alert-danger {
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  color: #991b1b;
}

.spinner-border {
  width: 2rem;
  height: 2rem;
}

.visually-hidden {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

@media (max-width: 576px) {
  .comments-section {
    padding: 1rem;
  }

  .comment-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .comment-meta {
    width: 100%;
  }
}
</style>
