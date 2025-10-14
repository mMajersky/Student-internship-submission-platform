import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAnnouncementsStore = defineStore('announcements', () => {
  // State
  const announcements = ref([])
  const currentAnnouncement = ref(null)
  const publishedAnnouncement = ref(null)
  const loading = ref(false)
  const error = ref(null)

  // Getters
  const hasPublishedAnnouncement = computed(() => {
    return publishedAnnouncement.value && publishedAnnouncement.value.content_sanitized
  })

  const latestAnnouncement = computed(() => {
    return announcements.value.length > 0 ? announcements.value[0] : null
  })

  // Actions
  const fetchAnnouncements = async () => {
    try {
      loading.value = true
      error.value = null
      
      const token = localStorage.getItem('jwt_token')
      const response = await fetch('/api/announcements', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        },
      })

      if (!response.ok) {
        throw new Error('Failed to fetch announcements')
      }

      announcements.value = await response.json()
    } catch (err) {
      error.value = err.message
      console.error('Error fetching announcements:', err)
    } finally {
      loading.value = false
    }
  }

  const fetchPublishedAnnouncement = async () => {
    try {
      loading.value = true
      error.value = null
      
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

  const createAnnouncement = async (announcementData) => {
    try {
      loading.value = true
      error.value = null
      
      const token = localStorage.getItem('jwt_token')
      const response = await fetch('/api/announcements', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(announcementData),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to create announcement')
      }

      const newAnnouncement = await response.json()
      announcements.value.unshift(newAnnouncement)
      return newAnnouncement
    } catch (err) {
      error.value = err.message
      console.error('Error creating announcement:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateAnnouncement = async (id, announcementData) => {
    try {
      loading.value = true
      error.value = null
      
      const token = localStorage.getItem('jwt_token')
      const response = await fetch(`/api/announcements/${id}`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(announcementData),
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to update announcement')
      }

      const updatedAnnouncement = await response.json()
      const index = announcements.value.findIndex(a => a.id === id)
      if (index !== -1) {
        announcements.value[index] = updatedAnnouncement
      }
      return updatedAnnouncement
    } catch (err) {
      error.value = err.message
      console.error('Error updating announcement:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteAnnouncement = async (id) => {
    try {
      loading.value = true
      error.value = null
      
      const token = localStorage.getItem('jwt_token')
      const response = await fetch(`/api/announcements/${id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json',
        },
      })

      if (!response.ok) {
        const errorData = await response.json()
        throw new Error(errorData.message || 'Failed to delete announcement')
      }

      announcements.value = announcements.value.filter(a => a.id !== id)
    } catch (err) {
      error.value = err.message
      console.error('Error deleting announcement:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  const clearError = () => {
    error.value = null
  }

  return {
    // State
    announcements,
    currentAnnouncement,
    publishedAnnouncement,
    loading,
    error,
    
    // Getters
    hasPublishedAnnouncement,
    latestAnnouncement,
    
    // Actions
    fetchAnnouncements,
    fetchPublishedAnnouncement,
    createAnnouncement,
    updateAnnouncement,
    deleteAnnouncement,
    clearError,
  }
})
