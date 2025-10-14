<script setup>
import { onMounted, computed } from 'vue'
import { useAnnouncementsStore } from '@/stores/announcements'

const announcementsStore = useAnnouncementsStore()

const hasAnnouncement = computed(() => announcementsStore.hasPublishedAnnouncement)
const announcementContent = computed(() => announcementsStore.publishedAnnouncement?.content_sanitized || '')
const announcementDate = computed(() => announcementsStore.publishedAnnouncement?.updated_at || '')

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('sk-SK')
}

onMounted(async () => {
  await announcementsStore.fetchPublishedAnnouncement()
})
</script>

<template>
  <div v-if="hasAnnouncement" class="bg-warning bg-opacity-25 border-bottom border-warning">
    <div class="container px-4 py-3">
      <div class="d-flex align-items-start justify-content-between">
        <div class="d-flex align-items-center me-3">
          <span class="badge text-bg-warning d-inline-flex align-items-center">
            <i class="bi bi-megaphone-fill me-1"></i>
            Oznam
          </span>
        </div>
        <div class="flex-grow-1">
          <div class="announcement-content" v-html="announcementContent"></div>
        </div>
        <div class="text-muted small ms-3">
          <i class="bi bi-calendar-event me-1"></i>
          {{ formatDate(announcementDate) }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.announcement-content {
  font-size: 0.9rem;
  line-height: 1.4;
}

.announcement-content :deep(h1),
.announcement-content :deep(h2),
.announcement-content :deep(h3),
.announcement-content :deep(h4),
.announcement-content :deep(h5),
.announcement-content :deep(h6) {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.announcement-content :deep(p) {
  margin-bottom: 0.25rem;
}

.announcement-content :deep(p:last-child) {
  margin-bottom: 0;
}

.announcement-content :deep(strong) {
  font-weight: 600;
}

.announcement-content :deep(em) {
  font-style: italic;
}

.announcement-content :deep(ul),
.announcement-content :deep(ol) {
  margin-bottom: 0.25rem;
  padding-left: 1rem;
}

.announcement-content :deep(li) {
  margin-bottom: 0.1rem;
}
</style>

