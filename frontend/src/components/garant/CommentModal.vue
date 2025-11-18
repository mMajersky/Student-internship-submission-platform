<template>
  <div v-if="isVisible" class="modal fade show" style="display: block;" tabindex="-1" role="dialog" aria-modal="true" @click.self="handleClose">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header px-4 py-3">
          <h5 class="modal-title d-flex align-items-center mb-0">
            <i class="bi bi-chat-left-text me-2"></i>
            {{ $t('commentModal.title') }}
          </h5>
          <button type="button" class="btn-close" @click="handleClose" :aria-label="$t('commentModal.close')"></button>
        </div>

        <div class="modal-body p-4">
          <div v-if="internship" class="alert alert-info mb-4 border-start border-primary border-top-0 border-end-0 border-bottom-0 rounded-2" style="border-width: 4px !important;">
            <p class="mb-1">
              <strong>{{ $t('commentModal.student') }}:</strong>
              {{ internship.student ? `${internship.student.name} ${internship.student.surname}` : '-' }}
            </p>
            <p class="mb-1">
              <strong>{{ $t('commentModal.company') }}:</strong>
              {{ internship.company ? internship.company.name : '-' }}
            </p>
            <p class="mb-0">
              <strong>{{ $t('commentModal.currentStatus') }}:</strong>
              <span class="badge ms-2" :class="getStatusClass(internship.status)">
                {{ internship.status }}
              </span>
            </p>
          </div>

          <!-- Previous Comments Section -->
          <div v-if="internship && authToken" class="mb-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 d-flex align-items-center">
                  <i class="bi bi-chat-dots me-2"></i>
                  {{ $t('commentModal.previousComments') }}
                </h6>
                <span v-if="comments.length > 0" class="badge bg-primary rounded-pill">
                  {{ comments.length }}
                </span>
              </div>

              <div class="card-body">
                <div v-if="loadingComments" class="text-center py-4">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">{{ $t('commentModal.loadingComments') }}</span>
                  </div>
                  <p class="text-muted mt-2 mb-0">{{ $t('commentModal.loadingComments') }}</p>
                </div>

                <div v-else-if="commentsError" class="alert alert-danger" role="alert">
                  <i class="bi bi-exclamation-triangle me-2"></i>
                  {{ commentsError }}
                </div>

                <div v-else-if="comments.length === 0" class="text-center py-5">
                  <i class="bi bi-chat-left text-muted" style="font-size: 3rem;"></i>
                  <p class="text-muted mb-0 mt-3">{{ $t('commentModal.noComments') }}</p>
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
                            {{ formatCommentDate(comment.created_at) }}
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
          </div>

          <!-- Add Comment Form -->
          <div class="card border-primary">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0">
                <i class="bi bi-plus-circle me-2"></i>
                {{ $t('commentModal.newComment') }}
              </h6>
            </div>
            <div class="card-body">
              <form @submit.prevent="handleSubmit">
            <div class="mb-3">
              <label for="commentType" class="form-label">
                <i class="bi bi-tag me-1"></i>
                {{ $t('commentModal.commentTypeRequired') }}
              </label>
              <select
                id="commentType"
                v-model="formData.comment_type"
                class="form-select"
                required
              >
                <option value="" disabled>{{ $t('commentModal.selectCommentType') }}</option>
                <option value="approval">{{ $t('commentModal.approval') }}</option>
                <option value="rejection">{{ $t('commentModal.rejection') }}</option>
                <option value="correction">{{ $t('commentModal.correction') }}</option>
                <option value="general">{{ $t('commentModal.general') }}</option>
              </select>
              <div class="form-text">
                {{ getCommentTypeDescription(formData.comment_type) }}
              </div>
            </div>

            <div class="mb-3">
              <label for="commentContent" class="form-label">
                <i class="bi bi-pencil me-1"></i>
                {{ $t('commentModal.commentRequired') }}
              </label>
              <textarea
                id="commentContent"
                v-model="formData.content"
                class="form-control"
                rows="6"
                :placeholder="$t('commentModal.commentPlaceholder')"
                required
                maxlength="2000"
              ></textarea>
              <div class="form-text text-end">
                {{ formData.content.length }} / 2000 {{ $t('commentModal.characters') }}
              </div>
            </div>

            <div v-if="error" class="alert alert-danger" role="alert">
              <i class="bi bi-exclamation-triangle me-2"></i>
              {{ error }}
            </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                  <button type="button" class="btn btn-secondary" @click="handleClose">
                    <i class="bi bi-x-circle me-2"></i>
                    {{ $t('commentModal.cancel') }}
                  </button>
                  <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                    <span v-if="isSubmitting">
                      <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                      {{ $t('commentModal.saving') }}
                    </span>
                    <span v-else>
                      <i class="bi bi-check-circle me-2"></i>
                      {{ $t('commentModal.saveComment') }}
                    </span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-if="isVisible" class="modal-backdrop fade show"></div>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
  isVisible: {
    type: Boolean,
    required: true
  },
  internship: {
    type: Object,
    default: null
  },
  authToken: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['close', 'submit'])

const formData = reactive({
  comment_type: '',
  content: ''
})

const isSubmitting = ref(false)
const error = ref('')

// Comments list state
const comments = ref([])
const loadingComments = ref(false)
const commentsError = ref('')

const sortedComments = computed(() => {
  return [...comments.value].sort((a, b) => {
    return new Date(b.created_at) - new Date(a.created_at)
  })
})

// Fetch comments function
const fetchComments = async () => {
  if (!props.internship?.id || !props.authToken) return

  loadingComments.value = true
  commentsError.value = ''

  try {
    const response = await fetch(`/api/internships/${props.internship.id}/comments`, {
      headers: {
        'Authorization': `Bearer ${props.authToken}`,
        'Accept': 'application/json',
      }
    })

    if (!response.ok) {
      const data = await response.json()
      throw new Error(data.message || t('commentModal.loadCommentsError'))
    }

    const data = await response.json()
    comments.value = data.data || data || []
  } catch (err) {
    console.error('Error fetching comments:', err)
    commentsError.value = err.message || t('commentModal.loadCommentsFailed')
  } finally {
    loadingComments.value = false
  }
}

// Reset form when modal opens with new internship
watch(() => props.internship, (newInternship) => {
  if (newInternship) {
    formData.comment_type = ''
    formData.content = ''
    error.value = ''
    fetchComments()
  }
})

// Reset form when modal closes
watch(() => props.isVisible, (isVisible) => {
  if (!isVisible) {
    formData.comment_type = ''
    formData.content = ''
    error.value = ''
    comments.value = []
    commentsError.value = ''
  } else if (isVisible && props.internship) {
    fetchComments()
  }
})

const handleClose = () => {
  if (!isSubmitting.value) {
    emit('close')
  }
}

const handleSubmit = async () => {
  if (!formData.comment_type || !formData.content.trim()) {
    error.value = t('commentModal.fillRequiredFields')
    return
  }

  if (!props.internship || !props.internship.id) {
    error.value = t('commentModal.invalidInternship')
    return
  }

  isSubmitting.value = true
  error.value = ''

  try {
    await emit('submit', {
      internship_id: props.internship.id,
      comment_type: formData.comment_type,
      content: formData.content.trim()
    })

    // Reset form after successful submission
    formData.comment_type = ''
    formData.content = ''

    // Refresh comments list to show the new comment
    await fetchComments()
  } catch (err) {
    error.value = err.message || t('commentModal.saveError')
  } finally {
    isSubmitting.value = false
  }
}

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

const formatCommentDate = (dateString) => {
  if (!dateString) return '-'

  // Ensure the date string is treated as UTC if it doesn't have timezone info
  let date
  if (dateString.includes('T') || dateString.includes('Z') || dateString.includes('+')) {
    // Already has timezone info
    date = new Date(dateString)
  } else {
    // No timezone info, treat as UTC
    date = new Date(dateString + 'Z')
  }

  const now = new Date()

  // Calculate difference in milliseconds
  const diffInMs = now - date
  const diffInMinutes = Math.floor(diffInMs / (1000 * 60))
  const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60))

  // If less than 1 minute
  if (diffInMinutes < 1) {
    return t('commentModal.justNow')
  }

  // If less than 1 hour
  if (diffInMinutes < 60) {
    return `${t('common.previous')} ${diffInMinutes} ${t('commentModal.minutesAgo')}`
  }

  // If less than 24 hours
  if (diffInHours < 24) {
    return `${t('common.previous')} ${diffInHours} ${t('commentModal.hoursAgo')}`
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

const getCommentTypeDescription = (type) => {
  const descriptions = {
    'approval': t('commentModal.approvalDesc'),
    'rejection': t('commentModal.rejectionDesc'),
    'correction': t('commentModal.correctionDesc'),
    'general': t('commentModal.generalDesc')
  }
  return descriptions[type] || t('commentModal.selectTypeDesc')
}

const getStatusClass = (status) => {
  const statusClasses = {
    'pending': 'bg-secondary',
    'confirmed': 'bg-info',
    'approved': 'bg-success',
    'in_progress': 'bg-warning text-dark',
    'completed': 'bg-success',
    'rejected': 'bg-danger',
    'cancelled': 'bg-danger'
  }
  return statusClasses[status] || 'bg-secondary'
}
</script>
