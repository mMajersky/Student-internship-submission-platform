<template>
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
      <h5 class="mb-0 d-flex align-items-center">
        <i class="bi bi-chat-left-text me-2"></i>
        {{ $t('studentComments.title') }}
      </h5>
      <span v-if="comments.length > 0" class="badge bg-primary rounded-pill">
        {{ comments.length }}
      </span>
    </div>

    <div class="card-body">
      <div v-if="loading" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">{{ $t('studentComments.loading') }}</span>
        </div>
        <p class="text-muted mt-2 mb-0">{{ $t('studentComments.loadingComments') }}</p>
      </div>

      <div v-else-if="error" class="alert alert-danger" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ error }}
      </div>

      <div v-else-if="comments.length === 0" class="text-center py-5">
        <i class="bi bi-chat-left text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mb-0 mt-3">{{ $t('studentComments.noComments') }}</p>
      </div>

      <div v-else class="d-flex flex-column gap-3">
        <div 
          v-for="comment in sortedComments" 
          :key="comment.id" 
          class="card comment-card"
          :class="`border-start border-${getCommentTypeColor(comment.comment_type)} border-3`"
        >
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
              <div class="d-flex align-items-center">
                <i class="bi bi-person-circle me-2 text-secondary" style="font-size: 1.25rem;"></i>
                <strong class="text-dark">{{ comment.author?.name || 'Garant' }}</strong>
              </div>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge" :class="getCommentTypeBadgeClass(comment.comment_type)">
                  {{ getCommentTypeLabel(comment.comment_type) }}
                </span>
                <small class="text-muted d-flex align-items-center">
                  <i class="bi bi-clock me-1"></i>
                  {{ formatDate(comment.created_at) }}
                </small>
              </div>
            </div>
            <p class="mb-0" style="white-space: pre-wrap; word-wrap: break-word;">
              {{ comment.content }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

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
      throw new Error(data.message || t('studentComments.loadError'))
    }

    const data = await response.json()
    comments.value = data.data || data || []
  } catch (err) {
    console.error('Error fetching comments:', err)
    error.value = err.message || t('studentComments.loadErrorGeneric')
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

const getCommentTypeColor = (type) => {
  const colors = {
    'approval': 'success',
    'rejection': 'danger',
    'correction': 'warning',
    'general': 'info'
  }
  return colors[type] || 'secondary'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  
  // Ensure the date string is treated as UTC if it doesn't have timezone info
  let date
  if (dateString.includes('T') || dateString.includes('Z') || dateString.includes('+')) {
    // Already has timezone info
    date = new Date(dateString)
  } else {
    // No timezone info, treat as UTC by appending 'Z'
    date = new Date(dateString.replace(' ', 'T') + 'Z')
  }
  
  const now = new Date()
  
  // Calculate difference in milliseconds
  const diffInMs = now - date
  const diffInMinutes = Math.floor(diffInMs / (1000 * 60))
  const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60))
  
  // If less than 1 minute
  if (diffInMinutes < 1) {
    return 'práve teraz'
  }
  
  // If less than 1 hour
  if (diffInMinutes < 60) {
    return `pred ${diffInMinutes} minútami`
  }
  
  // If less than 24 hours
  if (diffInHours < 24) {
    return `pred ${diffInHours} hodinami`
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
