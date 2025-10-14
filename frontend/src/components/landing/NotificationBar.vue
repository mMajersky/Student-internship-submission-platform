<script setup>
import { onMounted, computed } from 'vue'
import { useAnnouncementsStore } from '@/stores/announcements'

const store = useAnnouncementsStore()

const hasAnnouncement = computed(() => store.hasPublishedAnnouncement)
const content = computed(() => store.publishedAnnouncement?.content_sanitized || '')
const date = computed(() => store.publishedAnnouncement?.updated_at || '')
const fmt = (d) => (d ? new Date(d).toLocaleDateString('sk-SK') : '')

onMounted(async () => {
  await store.fetchPublishedAnnouncement()
})
</script>

<template>
  <div v-if="hasAnnouncement" class="bg-warning bg-opacity-25 border-bottom border-warning">
    <div class="container px-4 py-3 d-flex align-items-start justify-content-between">
      <span class="badge text-bg-warning d-inline-flex align-items-center me-3">
        <i class="bi bi-megaphone-fill me-1"></i>
        Oznam
      </span>
      <div class="flex-grow-1 announcement-content" v-html="content"></div>
      <div class="text-muted small ms-3">
        <i class="bi bi-calendar-event me-1"></i>
        {{ fmt(date) }}
      </div>
    </div>
  </div>
  
</template>

<style scoped>
.announcement-content { font-size: 0.9rem; line-height: 1.4; }
.announcement-content :deep(h1),
.announcement-content :deep(h2),
.announcement-content :deep(h3),
.announcement-content :deep(h4),
.announcement-content :deep(h5),
.announcement-content :deep(h6) { font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; }
.announcement-content :deep(p) { margin-bottom: 0.25rem; }
.announcement-content :deep(p:last-child) { margin-bottom: 0; }
.announcement-content :deep(ul),
.announcement-content :deep(ol) { margin-bottom: 0.25rem; padding-left: 1rem; }
.announcement-content :deep(li) { margin-bottom: 0.1rem; }
</style>


