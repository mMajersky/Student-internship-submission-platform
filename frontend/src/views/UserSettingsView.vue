<template>
  <div class="min-vh-100 d-flex flex-column bg-light">
    <main class="flex-grow-1 container py-4" style="max-width: 800px;">
      <h1 class="fs-2 fw-semibold mb-4 text-dark">Nastavenia</h1>

      <!-- Email Notifications -->
      <div class="card border shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">
            <i class="bi bi-envelope me-2"></i>
            Email notifikácie
          </h5>
          <p class="text-muted small">Povoľte alebo zakážte prijímanie emailov od systému.</p>
          
          <div class="form-check form-switch">
            <input 
              class="form-check-input" 
              type="checkbox" 
              id="emailNotifications"
              v-model="settings.email_notifications"
              @change="updateEmailNotifications">
            <label class="form-check-label" for="emailNotifications">
              {{ settings.email_notifications ? 'Zapnuté' : 'Vypnuté' }}
            </label>
          </div>

          <div class="alert alert-info mt-3 mb-0">
            <small>
              <i class="bi bi-info-circle me-2"></i>
              <strong>Poznámka:</strong> Aj keď sú emailové notifikácie vypnuté, stále budete vidieť notifikácie 
              v systéme (zvonček v hlavičke).
            </small>
          </div>
        </div>
      </div>

      <!-- Profile Information -->
      <div class="card border shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">
            <i class="bi bi-person me-2"></i>
            Informácie o profile
          </h5>
          
          <div class="mb-3">
            <label for="name" class="form-label">Meno</label>
            <input 
              type="text" 
              class="form-control" 
              id="name"
              v-model="profile.name"
              placeholder="Vaše meno">
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input 
              type="email" 
              class="form-control" 
              id="email"
              v-model="profile.email"
              placeholder="vasemail@example.com">
          </div>

          <div class="mb-3">
            <label class="form-label">Rola</label>
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
            Uložiť zmeny
          </button>
        </div>
      </div>

      <!-- Change Password -->
      <div class="card border shadow-sm mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">
            <i class="bi bi-shield-lock me-2"></i>
            Zmena hesla
          </h5>
          
          <div class="mb-3">
            <label for="currentPassword" class="form-label">Aktuálne heslo</label>
            <input 
              type="password" 
              class="form-control" 
              id="currentPassword"
              v-model="password.current"
              placeholder="Zadajte aktuálne heslo">
          </div>

          <div class="mb-3">
            <label for="newPassword" class="form-label">Nové heslo</label>
            <input 
              type="password" 
              class="form-control" 
              id="newPassword"
              v-model="password.new"
              placeholder="Zadajte nové heslo (min. 8 znakov)">
          </div>

          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Potvrdiť nové heslo</label>
            <input 
              type="password" 
              class="form-control" 
              id="confirmPassword"
              v-model="password.confirm"
              placeholder="Potvrďte nové heslo">
          </div>

          <button 
            @click="changePassword" 
            class="btn btn-warning"
            :disabled="isChangingPassword">
            <span v-if="isChangingPassword" class="spinner-border spinner-border-sm me-2"></span>
            Zmeniť heslo
          </button>
        </div>
      </div>
    </main>

    <footer class="bg-white border-top py-3 text-center text-muted small">
      © 2025 Odborná prax CRM
    </footer>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';

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
      alert(data.message || 'Nastavenie bolo aktualizované.');
    } else {
      alert(data.message || 'Chyba pri aktualizácii nastavenia.');
      // Revert on error
      settings.email_notifications = !settings.email_notifications;
    }
  } catch (error) {
    console.error('Error updating email notifications:', error);
    alert('Chyba pri aktualizácii nastavenia.');
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
      alert(data.message || 'Profil bol aktualizovaný.');
      settings.name = profile.name;
      settings.email = profile.email;
    } else {
      alert(data.message || 'Chyba pri aktualizácii profilu.');
    }
  } catch (error) {
    console.error('Error updating profile:', error);
    alert('Chyba pri aktualizácii profilu.');
  } finally {
    isUpdatingProfile.value = false;
  }
};

const changePassword = async () => {
  if (!password.current || !password.new || !password.confirm) {
    alert('Prosím vyplňte všetky polia.');
    return;
  }

  if (password.new !== password.confirm) {
    alert('Nové heslo a potvrdenie hesla sa nezhodujú.');
    return;
  }

  if (password.new.length < 8) {
    alert('Nové heslo musí mať aspoň 8 znakov.');
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
      alert(data.message || 'Heslo bolo zmenené.');
      password.current = '';
      password.new = '';
      password.confirm = '';
    } else {
      alert(data.message || 'Chyba pri zmene hesla.');
    }
  } catch (error) {
    console.error('Error changing password:', error);
    alert('Chyba pri zmene hesla.');
  } finally {
    isChangingPassword.value = false;
  }
};

onMounted(() => {
  loadSettings();
});
</script>

