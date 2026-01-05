<template>
  <div class="container mt-5">
    <div v-if="loading" class="text-center">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <div v-else-if="error" class="alert alert-danger">
      <h4>Chyba</h4>
      <p>{{ error }}</p>
    </div>
    <div v-else-if="success" class="alert alert-success">
      <h4>Úspech</h4>
      <p>{{ message }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const loading = ref(true)
const error = ref(null)
const success = ref(false)
const message = ref('')

onMounted(async () => {
  const token = route.query.token
  
  if (!token) {
    error.value = 'Token je povinný.'
    loading.value = false
    return
  }

  try {
    const response = await fetch(`/api/internships/company-action?token=${encodeURIComponent(token)}`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
      }
    })

    const data = await response.json()

    if (response.ok) {
      success.value = true
      message.value = data.message || 'Akcia bola úspešne vykonaná.'
      
      // Redirect to login or dashboard after 3 seconds
      setTimeout(() => {
        window.location.href = '/login'
      }, 3000)
    } else {
      error.value = data.message || 'Nastala chyba pri spracovaní požiadavky.'
    }
  } catch (err) {
    error.value = 'Nastala chyba pri komunikácii so serverom.'
    console.error('Error:', err)
  } finally {
    loading.value = false
  }
})
</script>

