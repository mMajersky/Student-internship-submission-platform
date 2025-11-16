<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const publishedAnnouncement = ref(null)
const loading = ref(false)
const error = ref('')

const hasAnnouncement = computed(() => {
  return publishedAnnouncement.value && publishedAnnouncement.value.content_sanitized
})

const content = computed(() => publishedAnnouncement.value?.content_sanitized || '')
const date = computed(() => publishedAnnouncement.value?.updated_at || '')
const fmt = (d) => (d ? new Date(d).toLocaleDateString('sk-SK') : '')

const fetchPublishedAnnouncement = async () => {
  try {
    loading.value = true
    error.value = ''

    const response = await fetch('/api/announcements/published')

    if (!response.ok) {
      throw new Error('Failed to fetch published announcement')
    }

    publishedAnnouncement.value = await response.json()
  } catch (err) {
    error.value = err.message
    console.error('Error fetching published announcement:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPublishedAnnouncement()
})
</script>

<template>
  <div v-if="hasAnnouncement" class="bg-warning bg-opacity-25 border-bottom border-warning">
    <div class="container px-4 py-3 d-flex align-items-start justify-content-between">
      <span class="badge text-bg-warning d-inline-flex align-items-center me-3">
        <i class="bi bi-megaphone-fill me-1"></i>
        {{ $t('notificationBar.announcement') }}
      </span>
      <div class="flex-grow-1 announcement-content" v-html="content"></div>
      <div class="text-muted small ms-3">
        <i class="bi bi-calendar-event me-1"></i>
        {{ fmt(date) }}
      </div>
    </div>
  </div>

  <!-- Error state (hidden by default, only shows if there's an error) -->
  <div v-if="error && !hasAnnouncement" class="d-none">
    <!-- Error is logged to console but not displayed to users -->
  </div>
</template>
