<template>
  <div class="notification-bell position-relative">
    <button 
      class="btn btn-link text-decoration-none position-relative p-0"
      @click="toggleDropdown"
      type="button">
      <i class="bi bi-bell fs-5" :class="{ 'text-warning': unreadCount > 0, 'text-success': unreadCount === 0 }"></i>
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
        <h6 class="mb-0 fw-semibold">{{ $t('notifications.title') }}</h6>
        <button
          v-if="notifications.length > 0"
          @click="deleteAllNotifications"
          class="btn btn-sm btn-link text-danger text-decoration-none p-0">
          <i class="bi bi-trash me-1"></i>
          {{ $t('notifications.deleteAll') }}
        </button>
      </div>

      <!-- Notifications List -->
      <div v-if="loading" class="p-4 text-center">
        <div class="spinner-border spinner-border-sm" role="status">
          <span class="visually-hidden">{{ $t('notifications.loading') }}</span>
        </div>
      </div>

      <div v-else-if="notifications.length === 0" class="p-4 text-center text-muted">
        <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
        <small>{{ $t('notifications.noNotifications') }}</small>
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
              <strong class="d-block mb-1">{{ getTranslatedTitle(notification) }}</strong>
              <small class="text-muted d-block mb-2">{{ getTranslatedMessage(notification) }}</small>
              <small class="text-muted">
                <i class="bi bi-clock me-1"></i>
                {{ formatDate(notification.created_at) }}
              </small>
            </div>
            <div>
              <span v-if="!notification.is_read" class="badge" style="background-color: #198754;">{{ $t('notifications.new') }}</span>
              <button
                @click.stop="deleteNotification(notification.id)"
                class="btn btn-sm btn-link text-danger p-0 ms-2"
                :title="$t('notifications.delete')">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Backdrop -->
    <div 
      v-if="showDropdown" 
      class="notification-backdrop position-fixed top-0 start-0 w-100 h-100"
      style="z-index: 1040;"
      @click="showDropdown = false"></div>

    <!-- Delete All Confirmation Dialog -->
    <ConfirmationDialog
      :is-visible="showDeleteAllConfirm"
      :title="$t('notifications.deleteAllTitle')"
      :message="$t('notifications.deleteAllConfirm')"
      :confirm-text="$t('notifications.deleteAll')"
      :cancel-text="$t('common.cancel')"
      type="danger"
      @confirm="confirmDeleteAll"
      @cancel="cancelDeleteAll"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import ConfirmationDialog from '@/components/common/ConfirmationDialog.vue';

const { t } = useI18n();

const authStore = useAuthStore();
const router = useRouter();

const showDropdown = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const loading = ref(false);
const showDeleteAllConfirm = ref(false);
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

const deleteAllNotifications = () => {
  showDeleteAllConfirm.value = true;
};

const confirmDeleteAll = async () => {
  showDeleteAllConfirm.value = false;
  
  try {
    // Delete all notifications one by one
    const deletePromises = notifications.value.map(notification =>
      fetch(`/api/notifications/${notification.id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json',
        }
      })
    );

    await Promise.all(deletePromises);
    
    // Reload notifications and count
    await loadNotifications();
    await loadUnreadCount();
  } catch (error) {
    console.error('Error deleting all notifications:', error);
  }
};

const cancelDeleteAll = () => {
  showDeleteAllConfirm.value = false;
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

  // Navigate based on notification type and user role
  showDropdown.value = false;
  
  // Extract internship_id from notification data
  const internshipId = notification.data?.internship_id || null;
  
  if (authStore.isStudent) {
    // For students, always navigate to upload documents if internship_id exists
    if (internshipId) {
      router.push(`/upload-documents?internshipId=${internshipId}`);
    } else {
      // Fallback to upload documents page without specific internship
      router.push('/upload-documents');
    }
  } else if (authStore.isCompany) {
    // For companies, navigate to company dashboard or specific internship documents
    if (internshipId) {
      router.push(`/company/internships/${internshipId}/documents`);
    } else {
      router.push('/company-dashboard');
    }
  } else if (authStore.isGarant) {
    // For garants, navigate based on notification type
    if (notification.type === 'company_request_created') {
      router.push('/garant-dashboard?tab=companyRequests');
    } else if (internshipId) {
      router.push(`/garant/internships/${internshipId}/documents`);
    } else {
      router.push('/garant-dashboard');
    }
  } else if (authStore.isAdmin) {
    // For admins, navigate to dashboard
    router.push('/dashboard');
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

const getTranslatedTitle = (notification) => {
  switch (notification.type) {
    case 'comment_added':
      return t('notifications.commentAdded.title');
    case 'approval_request':
      return t('notifications.approvalRequest.title');
    case 'internship_status_changed':
      return t('notifications.internshipStatusChanged.title');
    case 'internship_created':
      return t('notifications.internshipCreated.title');
    case 'document_uploaded':
      return t('notifications.documentUploaded.title');
    case 'company_request_created':
      return t('notifications.companyRequestCreated.title');
    case 'company_request_approved':
      return t('notifications.companyRequestApproved.title');
    case 'company_request_rejected':
      return t('notifications.companyRequestRejected.title');
    default:
      return notification.title; // Fallback to original title
  }
};

const getTranslatedMessage = (notification) => {
  switch (notification.type) {
    case 'comment_added':
      // Extract garant name from the original message
      const garantNameMatch = notification.message.match(/Garant (.+?) pridal/);
      const garantName = garantNameMatch ? garantNameMatch[1] : 'Unknown';
      return t('notifications.commentAdded.message', { garantName });

    case 'approval_request':
      // Extract student name from the original message
      const studentNameMatch = notification.message.match(/Študent (.+?) žiada/);
      const studentName = studentNameMatch ? studentNameMatch[1] : 'Unknown';
      return t('notifications.approvalRequest.message', { studentName });

    case 'internship_status_changed':
      // Use the status from notification data
      const status = notification.data?.new_status || 'Unknown';
      return t('notifications.internshipStatusChanged.message', { status });

    case 'internship_created':
      // Extract student name and company name from the original message
      const createdMatch = notification.message.match(/Študent (.+?) vytvoril novú prax(?: vo firme (.+?))?\./);
      const createdStudentName = createdMatch ? createdMatch[1] : 'Unknown';
      const companyName = createdMatch && createdMatch[2] ? createdMatch[2] : '';

      if (companyName) {
        return t('notifications.internshipCreated.message', { studentName: createdStudentName, companyName });
      } else {
        return t('notifications.internshipCreatedUnassigned.message', { studentName: createdStudentName });
      }

    case 'document_uploaded':
      // Extract student name from the original message
      const docStudentMatch = notification.message.match(/Študent (.+?) nahral/);
      const docStudentName = docStudentMatch ? docStudentMatch[1] : 'Unknown';
      return t('notifications.documentUploaded.message', { studentName: docStudentName });

    case 'company_request_created':
      // Extract company name from message: "A new company registration request for 'Company Name' has been submitted via student."
      const reqMatch = notification.message.match(/for '(.+?)' has been submitted/);
      const reqCompanyName = reqMatch ? reqMatch[1] : 'Unknown';
      return t('notifications.companyRequestCreated.message', { companyName: reqCompanyName });

    case 'company_request_approved':
      // Extract company name from message: "Your company request for 'Company Name' has been approved..."
      const approvedMatch = notification.message.match(/for '(.+?)' has been approved/);
      const approvedCompanyName = approvedMatch ? approvedMatch[1] : 'Unknown';
      return t('notifications.companyRequestApproved.message', { companyName: approvedCompanyName });

    case 'company_request_rejected':
      // Extract company name and reason from message: "Your company request for 'Company Name' has been rejected. Reason: ..."
      const rejectedMatch = notification.message.match(/for '(.+?)' has been rejected\. Reason: (.+)/);
      const rejectedCompanyName = rejectedMatch ? rejectedMatch[1] : 'Unknown';
      const reason = rejectedMatch ? rejectedMatch[2] : 'No reason provided';
      return t('notifications.companyRequestRejected.message', { companyName: rejectedCompanyName, reason });

    default:
      return notification.message; // Fallback to original message
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now - date;
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMs / 3600000);
  const diffDays = Math.floor(diffMs / 86400000);

  if (diffMins < 1) return t('notifications.justNow');
  if (diffMins < 60) return `${t('common.previous')} ${diffMins} ${t('notifications.minutesAgo')}`;
  if (diffHours < 24) return `${t('common.previous')} ${diffHours} ${t('notifications.hoursAgo')}`;
  if (diffDays < 7) return `${t('common.previous')} ${diffDays} ${t('notifications.daysAgo')}`;

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
