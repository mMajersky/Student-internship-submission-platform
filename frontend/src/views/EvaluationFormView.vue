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
    <div v-else>
      <iframe 
        :src="formUrl" 
        style="width: 100%; height: 100vh; border: none;"
        @load="handleIframeLoad"
      ></iframe>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const loading = ref(true)
const error = ref(null)
const formUrl = ref('')

onMounted(() => {
  const token = route.query.token
  
  if (!token) {
    error.value = 'Token je povinný.'
    loading.value = false
    return
  }

  // Redirect to backend form (which renders the HTML form)
  // The backend route handles the form rendering
  const backendUrl = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'
  formUrl.value = `${backendUrl}/internships/evaluation?token=${encodeURIComponent(token)}`
  loading.value = false
})

const handleIframeLoad = () => {
  // Iframe loaded successfully
}
</script>

