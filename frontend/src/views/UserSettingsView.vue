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

          <div class="alert alert-info mt-3 mb-0">
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
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useI18n } from 'vue-i18n';

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
      alert(data.message || t('settings.validation.settingUpdated'));
    } else {
      alert(data.message || t('settings.validation.settingUpdateError'));
      // Revert on error
      settings.email_notifications = !settings.email_notifications;
    }
  } catch (error) {
    console.error('Error updating email notifications:', error);
    alert(t('settings.validation.settingUpdateError'));
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
      alert(data.message || t('settings.validation.profileUpdated'));
      settings.name = profile.name;
      settings.email = profile.email;
    } else {
      alert(data.message || t('settings.validation.profileUpdateError'));
    }
  } catch (error) {
    console.error('Error updating profile:', error);
    alert(t('settings.validation.profileUpdateError'));
  } finally {
    isUpdatingProfile.value = false;
  }
};

const changePassword = async () => {
  if (!password.current || !password.new || !password.confirm) {
    alert(t('settings.validation.fillAllFields'));
    return;
  }

  if (password.new !== password.confirm) {
    alert(t('settings.validation.passwordsDontMatch'));
    return;
  }

  if (password.new.length < 8) {
    alert(t('settings.validation.passwordTooShort'));
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
      alert(data.message || t('settings.validation.passwordChanged'));
      password.current = '';
      password.new = '';
      password.confirm = '';
    } else {
      alert(data.message || t('settings.validation.passwordChangeError'));
    }
  } catch (error) {
    console.error('Error changing password:', error);
    alert(t('settings.validation.passwordChangeError'));
  } finally {
    isChangingPassword.value = false;
  }
};

onMounted(() => {
  loadSettings();
});
</script>
