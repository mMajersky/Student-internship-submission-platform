import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('jwt_token'))
  const isLoading = ref(false)
  const error = ref(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const userRole = computed(() => user.value?.role?.toLowerCase?.() || null) // normalize role
  const userPermissions = computed(() => user.value?.permissions || [])
  const userDisplayName = computed(() => user.value?.role_display_name || user.value?.name || '')
  const userEmail = computed(() => user.value?.email || '')

  // Role-based computed properties
  const isAdmin = computed(() => userRole.value === 'admin')
  const isGarant = computed(() => userRole.value === 'garant')
  const isCompany = computed(() => userRole.value === 'company')
  const isStudent = computed(() => userRole.value === 'student')
  const isAnonymous = computed(() => !isAuthenticated.value || userRole.value === 'anonymous')

  // Permission-based computed properties
  const canManageAnnouncements = computed(() => 
    isAdmin.value || isGarant.value
  )

  // Actions
  const setAuthData = (authData) => {
    user.value = authData.user
    token.value = authData.token
    
    // Store in localStorage
    localStorage.setItem('jwt_token', authData.token)
    localStorage.setItem('user_data', JSON.stringify(authData.user))
  }

  const clearAuthData = () => {
    user.value = null
    token.value = null
    error.value = null
    
    // Clear localStorage
    localStorage.removeItem('jwt_token')
    localStorage.removeItem('user_data')
  }

  const login = async (credentials) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(credentials)
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.error || 'Login failed')
      }

      setAuthData(data)
      return data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      isLoading.value = false
    }
  }

  const logout = () => {
    clearAuthData()
  }

  const fetchUser = async () => {
    if (!token.value) return

    try {
      const response = await fetch('/api/user', {
        headers: {
          'Authorization': `Bearer ${token.value}`,
          'Accept': 'application/json',
        }
      })

      if (response.ok) {
        const userData = await response.json()
        user.value = userData
        localStorage.setItem('user_data', JSON.stringify(userData))
      } else if (response.status === 401) {
        // Token expired or invalid
        clearAuthData()
      }
    } catch (err) {
      console.error('Failed to fetch user:', err)
      clearAuthData()
    }
  }

  const hasPermission = (permission) => {
    return userPermissions.value.includes(permission)
  }

  const hasAnyPermission = (permissions) => {
    return permissions.some(permission => hasPermission(permission))
  }

  const hasRole = (role) => {
    return userRole.value === role
  }

  const hasAnyRole = (roles) => {
    return roles.includes(userRole.value)
  }

  // Initialize from localStorage
  const initializeAuth = () => {
    const storedUserData = localStorage.getItem('user_data')
    if (storedUserData && token.value) {
      try {
        user.value = JSON.parse(storedUserData)
      } catch (err) {
        console.error('Failed to parse stored user data:', err)
        clearAuthData()
      }
    }
  }

  // Role constants
  const ROLES = {
    ADMIN: 'ADMIN',
    GARANT: 'GARANT',
    COMPANY: 'COMPANY',
    STUDENT: 'STUDENT',
    ANONYMOUS: 'ANONYMOUS'
  }

  return {
    // State
    user,
    token,
    isLoading,
    error,

    // Getters
    isAuthenticated,
    userRole,
    userPermissions,
    userDisplayName,
    userEmail,
    isAdmin,
    isGarant,
    isCompany,
    isStudent,
    isAnonymous,
    canManageAnnouncements,

    // Actions
    setAuthData,
    clearAuthData,
    login,
    logout,
    fetchUser,
    hasPermission,
    hasAnyPermission,
    hasRole,
    hasAnyRole,
    initializeAuth,

    // Constants
    ROLES
  }
})
