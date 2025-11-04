<template>
  <div class="notification-bell position-relative">
    <button 
      class="btn btn-link text-decoration-none position-relative p-0"
      @click="toggleDropdown"
      type="button">
      <i class="bi bi-bell fs-5" :class="{ 'text-warning': unreadCount > 0 }"></i>
      <span 
        v-if="unreadCount > 0" 
        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown -->
    <div 
      v-if="showDropdown" 
      class="notification-dropdown position-absolute end-0 mt-2 bg-white shadow-lg rounded border"
      style="width: 350px; max-height: 500px; overflow-y: auto; z-index: 1050;">
      
      <!-- Header -->
      <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">Notifikácie</h6>
        <button 
          v-if="unreadCount > 0"
          @click="markAllAsRead" 
          class="btn btn-sm btn-link text-decoration-none p-0">
          Označiť všetko
        </button>
      </div>

      <!-- Notifications List -->
      <div v-if="loading" class="p-4 text-center">
        <div class="spinner-border spinner-border-sm" role="status">
          <span class="visually-hidden">Načítavam...</span>
        </div>
      </div>

      <div v-else-if="notifications.length === 0" class="p-4 text-center text-muted">
        <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
        <small>Žiadne notifikácie</small>
      </div>

      <div v-else>
        <div 
          v-for="notification in notifications" 
          :key="notification.id"
          class="notification-item p-3 border-bottom"
          :class="{ 'bg-light': !notification.is_read }"
          @click="handleNotificationClick(notification)">
          
          <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
              <strong class="d-block mb-1">{{ notification.title }}</strong>
              <small class="text-muted d-block mb-2">{{ notification.message }}</small>
              <small class="text-muted">
                <i class="bi bi-clock me-1"></i>
                {{ formatDate(notification.created_at) }}
              </small>
            </div>
            <div>
              <span v-if="!notification.is_read" class="badge bg-primary">Nové</span>
              <button 
                @click.stop="deleteNotification(notification.id)"
                class="btn btn-sm btn-link text-danger p-0 ms-2"
                title="Odstrániť">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-2 border-top text-center">
        <router-link to="/notifications" class="btn btn-sm btn-link text-decoration-none" @click="showDropdown = false">
          Zobraziť všetky notifikácie
        </router-link>
      </div>
    </div>

    <!-- Backdrop -->
    <div 
      v-if="showDropdown" 
      class="notification-backdrop position-fixed top-0 start-0 w-100 h-100"
      style="z-index: 1040;"
      @click="showDropdown = false"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const showDropdown = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const loading = ref(false);
let pollInterval = null;

const toggleDropdown = async () => {
  showDropdown.value = !showDropdown.value;
  if (showDropdown.value) {
    await loadNotifications();
  }
};

const loadNotifications = async () => {
  loading.value = true;
  try {
    const response = await fetch('/api/notifications?per_page=10', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    });

    if (response.ok) {
      const data = await response.json();
      notifications.value = data.data || [];
    }
  } catch (error) {
    console.error('Error loading notifications:', error);
  } finally {
    loading.value = false;
  }
};

const loadUnreadCount = async () => {
  try {
    const response = await fetch('/api/notifications/unread-count', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    });

    if (response.ok) {
      const data = await response.json();
      unreadCount.value = data.unread_count || 0;
    }
  } catch (error) {
    console.error('Error loading unread count:', error);
  }
};

const markAllAsRead = async () => {
  try {
    const response = await fetch('/api/notifications/mark-all-read', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    });

    if (response.ok) {
      await loadNotifications();
      await loadUnreadCount();
    }
  } catch (error) {
    console.error('Error marking all as read:', error);
  }
};

const handleNotificationClick = async (notification) => {
  // Mark as read
  if (!notification.is_read) {
    try {
      await fetch(`/api/notifications/${notification.id}/read`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json',
        }
      });
      
      await loadUnreadCount();
    } catch (error) {
      console.error('Error marking notification as read:', error);
    }
  }

  // Navigate based on notification type
  showDropdown.value = false;
  
  if (notification.data && notification.data.internship_id) {
    // Navigate to internship documents or details
    if (authStore.isStudent) {
      router.push(`/upload-documents?internshipId=${notification.data.internship_id}`);
    } else {
      router.push(`/dashboard`);
    }
  }
};

const deleteNotification = async (id) => {
  try {
    const response = await fetch(`/api/notifications/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    });

    if (response.ok) {
      notifications.value = notifications.value.filter(n => n.id !== id);
      await loadUnreadCount();
    }
  } catch (error) {
    console.error('Error deleting notification:', error);
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now - date;
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMs / 3600000);
  const diffDays = Math.floor(diffMs / 86400000);

  if (diffMins < 1) return 'Práve teraz';
  if (diffMins < 60) return `Pred ${diffMins} min`;
  if (diffHours < 24) return `Pred ${diffHours} hod`;
  if (diffDays < 7) return `Pred ${diffDays} dňami`;
  
  return date.toLocaleDateString('sk-SK');
};

// Poll for new notifications every 30 seconds
onMounted(() => {
  loadUnreadCount();
  pollInterval = setInterval(loadUnreadCount, 30000);
});

onUnmounted(() => {
  if (pollInterval) {
    clearInterval(pollInterval);
  }
});
</script>

<style scoped>
.notification-bell {
  display: inline-block;
}

.notification-dropdown {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.notification-item {
  cursor: pointer;
  transition: background-color 0.2s;
}

.notification-item:hover {
  background-color: #f8f9fa !important;
}

.notification-backdrop {
  background-color: transparent;
}
</style>

