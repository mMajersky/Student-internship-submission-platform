<template>
  <div class="manage-garants">
    <h3 class="mb-4">Správa garantov</h3>
    
    <!-- Create Garant Form -->
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title mb-3">{{ editingGarant ? 'Upraviť garanta' : 'Vytvoriť nového garanta' }}</h5>
        
        <form @submit.prevent="handleSubmit">
          <div class="row mb-3">
            <!-- Name field -->
            <div class="col-md-3">
              <label for="name" class="form-label">
                Meno<span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.name }"
                id="name"
                v-model="formData.name"
                placeholder="Zadajte meno"
                required
              />
              <div v-if="errors.name" class="invalid-feedback">
                {{ errors.name }}
              </div>
            </div>

            <!-- Surname field -->
            <div class="col-md-3">
              <label for="surname" class="form-label">
                Priezvisko<span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                :class="{ 'is-invalid': errors.surname }"
                id="surname"
                v-model="formData.surname"
                placeholder="Zadajte priezvisko"
                required
              />
              <div v-if="errors.surname" class="invalid-feedback">
                {{ errors.surname }}
              </div>
            </div>

            <!-- Email field -->
            <div class="col-md-3">
              <label for="email" class="form-label">
                Email<span class="text-danger">*</span>
              </label>
              <input
                type="email"
                class="form-control"
                :class="{ 'is-invalid': errors.email }"
                id="email"
                v-model="formData.email"
                placeholder="garant@example.com"
                required
              />
              <div v-if="errors.email" class="invalid-feedback">
                {{ errors.email }}
              </div>
              <small class="text-muted">Email musí byť unikátny</small>
            </div>

            <!-- Password field -->
            <div class="col-md-3">
              <label for="password" class="form-label">
                Heslo<span v-if="!editingGarant" class="text-danger">*</span>
              </label>
              <input
                type="password"
                class="form-control"
                :class="{ 'is-invalid': errors.password }"
                id="password"
                v-model="formData.password"
                :placeholder="editingGarant ? 'Nechajte prázdne pre zachovanie' : 'Zadajte heslo'"
                minlength="6"
                :required="!editingGarant"
              />
              <div v-if="errors.password" class="invalid-feedback">
                {{ errors.password }}
              </div>
              <small class="text-muted">{{ editingGarant ? 'Nechajte prázdne ak nechcete zmeniť' : 'Minimálne 6 znakov' }}</small>
            </div>
          </div>

          <!-- Info message -->
          <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle me-2"></i>
            {{ editingGarant ? 'Upravte údaje garanta. Heslo zmeňte len ak je to potrebné.' : 'Po vytvorení bude garant môcť používať zadaný email a heslo na prihlásenie do systému.' }}
          </div>

          <!-- Error message -->
          <div v-if="submitError" class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ submitError }}
          </div>

          <!-- Success message -->
          <div v-if="submitSuccess" class="alert alert-success mb-3">
            <i class="bi bi-check-circle me-2"></i>
            {{ submitSuccess }}
          </div>

          <!-- Action buttons -->
          <div class="d-flex justify-content-end gap-2">
            <button
              type="button"
              class="btn btn-secondary"
              @click="resetForm"
              :disabled="isSubmitting"
            >
              <i class="bi bi-x-circle me-2"></i>
              {{ editingGarant ? 'Zrušiť úpravu' : 'Zrušiť' }}
            </button>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="isSubmitting"
            >
              <span v-if="isSubmitting">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                {{ editingGarant ? 'Aktualizuje sa...' : 'Vytvára sa...' }}
              </span>
              <span v-else>
                <i :class="editingGarant ? 'bi bi-check-circle me-2' : 'bi bi-plus-circle me-2'"></i>
                {{ editingGarant ? 'Aktualizovať garanta' : 'Vytvoriť garanta' }}
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Garants List Table (Optional) -->
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title mb-0">Zoznam garantov</h5>
          <button 
            class="btn btn-sm btn-outline-primary"
            @click="fetchGarants"
            :disabled="isLoadingGarants"
          >
            <i class="bi bi-arrow-clockwise me-1"></i>
            Obnoviť
          </button>
        </div>
        
        <!-- Loading state -->
        <div v-if="isLoadingGarants" class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Načítava sa...</span>
          </div>
          <p class="text-muted mt-2">Načítavajú sa garanti...</p>
        </div>

        <!-- Empty state -->
        <div v-else-if="garants.length === 0" class="text-center py-5">
          <i class="bi bi-people fs-1 text-muted"></i>
          <p class="text-muted mt-3">Zatiaľ neboli vytvorení žiadni garanti.</p>
        </div>

        <!-- Garants table -->
        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Meno</th>
                <th>Priezvisko</th>
                <th>Email</th>
                <th>Vytvorené</th>
                <th>Akcie</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="garant in garants" :key="garant.id">
                <td>{{ garant.id }}</td>
                <td>{{ garant.name }}</td>
                <td>{{ garant.surname }}</td>
                <td>{{ garant.email }}</td>
                <td>{{ formatDate(garant.created_at) }}</td>
                <td>
                  <button 
                    class="btn btn-sm btn-outline-primary me-1" 
                    title="Upraviť"
                    @click="handleEditGarant(garant)"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button 
                    class="btn btn-sm btn-outline-danger" 
                    title="Vymazať"
                    @click="handleDeleteGarant(garant.id)"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()

// Form data
const formData = ref({
  name: '',
  surname: '',
  email: '',
  password: ''
})

// Form state
const errors = ref({})
const isSubmitting = ref(false)
const submitError = ref(null)
const submitSuccess = ref(null)

// Garants list state
const garants = ref([])
const isLoadingGarants = ref(false)

// Editing state
const editingGarant = ref(null)

// Validate form
const validateForm = () => {
  errors.value = {}
  let isValid = true

  // Validate name
  if (!formData.value.name || formData.value.name.trim().length === 0) {
    errors.value.name = 'Meno je povinné'
    isValid = false
  }

  // Validate surname
  if (!formData.value.surname || formData.value.surname.trim().length === 0) {
    errors.value.surname = 'Priezvisko je povinné'
    isValid = false
  }

  // Validate email
  if (!formData.value.email || formData.value.email.trim().length === 0) {
    errors.value.email = 'Email je povinný'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
    errors.value.email = 'Neplatný email formát'
    isValid = false
  }

  // Validate password
  if (!formData.value.password || formData.value.password.length < 6) {
    // Only require password for new garants, not when editing
    if (!editingGarant.value) {
      errors.value.password = 'Heslo musí mať minimálne 6 znakov'
      isValid = false
    } else if (formData.value.password && formData.value.password.length < 6) {
      // If editing and password is provided, it must be at least 6 characters
      errors.value.password = 'Heslo musí mať minimálne 6 znakov'
      isValid = false
    }
  }

  return isValid
}

// Reset form
const resetForm = () => {
  formData.value = {
    name: '',
    surname: '',
    email: '',
    password: ''
  }
  errors.value = {}
  submitError.value = null
  submitSuccess.value = null
  editingGarant.value = null
}

// Handle form submission
const handleSubmit = async () => {
  // Clear previous messages
  submitError.value = null
  submitSuccess.value = null

  // Validate form
  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    const isEditing = editingGarant.value !== null
    
    // Prepare payload
    const payload = {
      name: formData.value.name.trim(),
      surname: formData.value.surname.trim(),
      email: formData.value.email.trim()
    }
    
    // Only include password if provided (for editing) or always (for creating)
    if (!isEditing || formData.value.password) {
      payload.password = formData.value.password
    }

    // Determine API endpoint and method
    const url = isEditing 
      ? `/api/garants/${editingGarant.value.id}`
      : '/api/garants'
    const method = isEditing ? 'PUT' : 'POST'

    // Send POST/PUT request
    const response = await fetch(url, {
      method: method,
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload)
    })

    const data = await response.json()

    if (!response.ok) {
      // Handle specific error cases
      if (response.status === 409 || data.error?.includes('email')) {
        errors.value.email = 'Email už existuje v systéme'
        throw new Error('Email už existuje v systéme')
      }
      throw new Error(data.error || data.message || `Nepodarilo sa ${isEditing ? 'aktualizovať' : 'vytvoriť'} garanta`)
    }

    // Show success message
    submitSuccess.value = isEditing ? 'Garant bol úspešne aktualizovaný!' : 'Garant bol úspešne vytvorený!'

    // Reset form
    formData.value = {
      name: '',
      surname: '',
      email: '',
      password: ''
    }
    errors.value = {}
    editingGarant.value = null

    // Refresh garants list
    await fetchGarants()

    // Clear success message after 5 seconds
    setTimeout(() => {
      submitSuccess.value = null
    }, 5000)

  } catch (error) {
    console.error('Error creating/updating garant:', error)
    submitError.value = error.message || `Chyba pri ${editingGarant.value ? 'aktualizácii' : 'vytváraní'} garanta. Skúste to znova.`
  } finally {
    isSubmitting.value = false
  }
}

// Fetch garants list
const fetchGarants = async () => {
  isLoadingGarants.value = true

  try {
    const response = await fetch('/api/garants', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    })

    if (response.ok) {
      const data = await response.json()
      garants.value = data.data || data
    } else {
      console.error('Failed to fetch garants:', response.status)
    }
  } catch (error) {
    console.error('Error fetching garants:', error)
  } finally {
    isLoadingGarants.value = false
  }
}

// Handle edit garant
const handleEditGarant = (garant) => {
  editingGarant.value = garant
  
  // Populate form with garant data
  formData.value = {
    name: garant.name,
    surname: garant.surname,
    email: garant.email,
    password: '' // Don't populate password for security
  }
  
  // Clear any previous errors
  errors.value = {}
  submitError.value = null
  submitSuccess.value = null
  
  // Scroll to top of form
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Handle delete garant
const handleDeleteGarant = async (garantId) => {
  if (!confirm('Naozaj chcete vymazať tohto garanta?')) {
    return
  }

  try {
    const response = await fetch(`/api/garants/${garantId}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
      }
    })

    if (!response.ok) {
      const data = await response.json()
      throw new Error(data.error || data.message || 'Failed to delete garant')
    }

    // Refresh garants list
    await fetchGarants()

    // Show success message
    alert('Garant bol úspešne vymazaný!')
  } catch (error) {
    console.error('Error deleting garant:', error)
    alert(error.message || 'Chyba pri mazaní garanta. Skúste to znova.')
  }
}

// Format date
const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('sk-SK', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Load garants when component is mounted
onMounted(() => {
  fetchGarants()
})
</script>

