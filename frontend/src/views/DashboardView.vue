<template>
  <div class="container text-center mt-5">
    <div class="card p-5 shadow-sm">
      <h1 class="display-4">Login Successful!</h1>
      <p class="lead">You have been successfully authenticated.</p>
      <hr class="my-4" />
      <p>
        Your user role is: <strong>{{ userRole }}</strong>
      </p>
      <p>This is where the dashboard will be for logged in users. Enjoy</p>
      <button @click="logout" class="btn btn-danger mt-3" style="max-width: 200px; margin: auto">
        Logout
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const userRole = ref(localStorage.getItem('user_role') || 'Not Found')

const logout = () => {
  localStorage.removeItem('jwt_token')
  localStorage.removeItem('user_role')

  router.push('/login')
}
</script>
