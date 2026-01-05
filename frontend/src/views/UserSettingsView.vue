<template>
  <div class="min-vh-100 d-flex flex-column bg-light">
    <main class="flex-grow-1 container py-4" style="max-width: 800px;">
      <h1 class="fs-2 fw-semibold mb-4 text-dark">{{ $t('settings.title') }}</h1>

      <!-- Email Notifications -->
      <div class="card border shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">
            <i class="bi bi-envelope me-2"></i>
            {{ $t('settings.emailNotifications') }}
          </h5>
          <p class="text-muted small">{{ $t('settings.emailNotificationsDesc') }}</p>

          <div class="form-check form-switch">
            <input
              class="form-check-input"
              type="checkbox"
              id="emailNotifications"
              v-model="settings.email_notifications"
              @change="updateEmailNotifications">
            <label class="form-check-label" for="emailNotifications">
              {{ settings.email_notifications ? $t('settings.enabled') : $t('settings.disabled') }}
            </label>
          </div>

          <div class="alert mt-3 mb-0" style="background-color: #d1e7dd; border-color: #badbcc; color: #0f5132;">
            <small>
              <i class="bi bi-info-circle me-2"></i>
              <strong>{{ $t('common.note') }}:</strong> {{ $t('settings.emailNote') }}
            </small>
          </div>
        </div>
      </div>

      <!-- Profile Information -->
      <div class="card border shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">
            <i class="bi bi-person me-2"></i>
            {{ $t('settings.profileInfo') }}
          </h5>

          <div class="mb-3">
            <label for="name" class="form-label">{{ $t('settings.name') }}</label>
            <input
              type="text"
              class="form-control"
              id="name"
              v-model="profile.name"
              :placeholder="$t('settings.namePlaceholder')">
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">{{ $t('settings.email') }}</label>
            <input
              type="email"
              class="form-control"
              id="email"
              v-model="profile.email"
              :placeholder="$t('settings.emailPlaceholder')">
          </div>

          <div class="mb-3">
            <label class="form-label">{{ $t('settings.role') }}</label>
            <input
              type="text"
              class="form-control"
              :value="settings.role"
              disabled
              readonly>
          </div>

          <button
            @click="updateProfile"
            class="btn btn-primary"
            :disabled="isUpdatingProfile">
            <span v-if="isUpdatingProfile" class="spinner-border spinner-border-sm me-2"></span>
            {{ $t('settings.saveChanges') }}
          </button>
        </div>
      </div>

      <!-- Change Password -->
      <div class="card border shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">
            <i class="bi bi-shield-lock me-2"></i>
            {{ $t('settings.changePassword') }}
          </h5>

          <div class="mb-3">
            <label for="currentPassword" class="form-label">{{ $t('settings.currentPassword') }}</label>
            <input
              type="password"
              class="form-control"
              id="currentPassword"
              v-model="password.current"
              :placeholder="$t('settings.currentPasswordPlaceholder')">
          </div>

          <div class="mb-3">
            <label for="newPassword" class="form-label">{{ $t('settings.newPassword') }}</label>
            <input
              type="password"
              class="form-control"
              id="newPassword"
              v-model="password.new"
              :placeholder="$t('settings.newPasswordPlaceholder')">
          </div>

          <div class="mb-3">
            <label for="confirmPassword" class="form-label">{{ $t('settings.confirmPassword') }}</label>
            <input
              type="password"
              class="form-control"
              id="confirmPassword"
              v-model="password.confirm"
              :placeholder="$t('settings.confirmPasswordPlaceholder')">
          </div>

          <button
            @click="changePassword"
            class="btn btn-warning"
            :disabled="isChangingPassword">
            <span v-if="isChangingPassword" class="spinner-border spinner-border-sm me-2"></span>
            {{ $t('settings.changePasswordButton') }}
          </button>
        </div>
      </div>
    </main>

    <footer class="bg-white border-top py-3 text-center text-muted small">
      {{ $t('footer.copyright') }}
    </footer>

    <!-- Message Modal -->
    <MessageModal
      :is-visible="showMessageModal"
      :title="messageModalTitle"
      :message="messageModalMessage"
      :type="messageModalType"
      @close="closeMessageModal"
      @confirm="closeMessageModal"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useI18n } from 'vue-i18n';
import MessageModal from '@/components/common/MessageModal.vue';

const { t } = useI18n();

const authStore = useAuthStore();

const settings = reactive({
  email_notifications: true,
  email: '',
  name: '',
  role: '',
});

const profile = reactive({
  name: '',
  email: '',
});

const password = reactive({
  current: '',
  new: '',
  confirm: '',
});

const isUpdatingProfile = ref(false);
const isChangingPassword = ref(false);

// Message modal state
const showMessageModal = ref(false);
const messageModalTitle = ref('');
const messageModalMessage = ref('');
const messageModalType = ref('info');

const showMessage = (message, title = null, type = 'info') => {
  messageModalTitle.value = title || t('common.message');
  messageModalMessage.value = message;
  messageModalType.value = type;
  showMessageModal.value = true;
};

const closeMessageModal = () => {
  showMessageModal.value = false;
};

const loadSettings = async () => {
  try {
    const response = await fetch('/api/user/settings', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    });

    if (response.ok) {
      const data = await response.json();
      settings.email_notifications = data.email_notifications ?? true;
      settings.email = data.email;
      settings.name = data.name;
      settings.role = data.role;
      
      profile.name = data.name;
      profile.email = data.email;
    }
  } catch (error) {
    console.error('Error loading settings:', error);
  }
};

const updateEmailNotifications = async () => {
  try {
    const response = await fetch('/api/user/settings/email-notifications', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        email_notifications: settings.email_notifications
      })
    });

    const data = await response.json();
    
    if (response.ok) {
      showMessage(data.message || t('settings.validation.settingUpdated'), null, 'success');
    } else {
      showMessage(data.message || t('settings.validation.settingUpdateError'), null, 'error');
      // Revert on error
      settings.email_notifications = !settings.email_notifications;
    }
  } catch (error) {
    console.error('Error updating email notifications:', error);
    showMessage(t('settings.validation.settingUpdateError'), null, 'error');
    settings.email_notifications = !settings.email_notifications;
  }
};

const updateProfile = async () => {
  isUpdatingProfile.value = true;
  try {
    const response = await fetch('/api/user/profile', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(profile)
    });

    const data = await response.json();
    
    if (response.ok) {
      showMessage(data.message || t('settings.validation.profileUpdated'), null, 'success');
      settings.name = profile.name;
      settings.email = profile.email;
    } else {
      showMessage(data.message || t('settings.validation.profileUpdateError'), null, 'error');
    }
  } catch (error) {
    console.error('Error updating profile:', error);
    showMessage(t('settings.validation.profileUpdateError'), null, 'error');
  } finally {
    isUpdatingProfile.value = false;
  }
};

const changePassword = async () => {
  if (!password.current || !password.new || !password.confirm) {
    showMessage(t('settings.validation.fillAllFields'), null, 'warning');
    return;
  }

  if (password.new !== password.confirm) {
    showMessage(t('settings.validation.passwordsDontMatch'), null, 'warning');
    return;
  }

  if (password.new.length < 8) {
    showMessage(t('settings.validation.passwordTooShort'), null, 'warning');
    return;
  }

  isChangingPassword.value = true;
  try {
    const response = await fetch('/api/user/password', {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        current_password: password.current,
        new_password: password.new,
        new_password_confirmation: password.confirm,
      })
    });

    const data = await response.json();
    
    if (response.ok) {
      showMessage(data.message || t('settings.validation.passwordChanged'), null, 'success');
      password.current = '';
      password.new = '';
      password.confirm = '';
    } else {
      showMessage(data.message || t('settings.validation.passwordChangeError'), null, 'error');
    }
  } catch (error) {
    console.error('Error changing password:', error);
    showMessage(t('settings.validation.passwordChangeError'), null, 'error');
  } finally {
    isChangingPassword.value = false;
  }
};

onMounted(() => {
  loadSettings();
});
</script>
