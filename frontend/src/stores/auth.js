import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(null)
  const rememberMe = ref(false)
  const isLoading = ref(false)
  const error = ref(null)
  
  // Initialize token from storage based on remember_me preference
  const initializeToken = () => {
    const storedRememberMe = localStorage.getItem('remember_me') === 'true'
    rememberMe.value = storedRememberMe
    
    if (storedRememberMe) {
      // Use localStorage for remember me
      token.value = localStorage.getItem('jwt_token')
    } else {
      // Use sessionStorage for regular sessions
      token.value = sessionStorage.getItem('jwt_token')
    }
  }
  
  // Initialize on store creation
  initializeToken()

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
  const setAuthData = (authData, remember = false) => {
    user.value = authData.user
    token.value = authData.token
    rememberMe.value = remember
    
    // Store remember_me preference
    if (remember) {
      localStorage.setItem('remember_me', 'true')
      localStorage.setItem('jwt_token', authData.token)
      localStorage.setItem('user_data', JSON.stringify(authData.user))
      // Clear sessionStorage for consistency
      sessionStorage.removeItem('jwt_token')
      sessionStorage.removeItem('user_data')
    } else {
      localStorage.setItem('remember_me', 'false')
      sessionStorage.setItem('jwt_token', authData.token)
      sessionStorage.setItem('user_data', JSON.stringify(authData.user))
      // Clear localStorage for consistency
      localStorage.removeItem('jwt_token')
      localStorage.removeItem('user_data')
    }
  }

  const clearAuthData = () => {
    user.value = null
    token.value = null
    rememberMe.value = false
    error.value = null
    
    // Clear both storages
    localStorage.removeItem('jwt_token')
    localStorage.removeItem('user_data')
    localStorage.removeItem('remember_me')
    sessionStorage.removeItem('jwt_token')
    sessionStorage.removeItem('user_data')
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

      // Store remember_me preference from response or request
      const remember = data.remember_me || credentials.remember_me || false
      setAuthData(data, remember)
      return data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      isLoading.value = false
    }
  }
  
  const refreshToken = async () => {
    if (!token.value) return null

    try {
      const response = await fetch('/api/auth/refresh', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token.value}`,
          'Accept': 'application/json',
        }
      })

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.error || 'Token refresh failed')
      }

      // Keep the same remember_me preference
      setAuthData(data, rememberMe.value)
      return data
    } catch (err) {
      console.error('Token refresh failed:', err)
      // If refresh fails, clear auth data (user needs to login again)
      clearAuthData()
      throw err
    }
  }

  const logout = async () => {
    if (token.value) {
      try {
        await fetch('/api/auth/logout', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token.value}`,
            'Accept': 'application/json',
          },
        })
      } catch (err) {
        console.error('Failed to call logout endpoint:', err)
      }
    }

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
        
        // Store in appropriate storage
        if (rememberMe.value) {
          localStorage.setItem('user_data', JSON.stringify(userData))
        } else {
          sessionStorage.setItem('user_data', JSON.stringify(userData))
        }
      } else if (response.status === 401) {
        // Token expired - try to refresh
        try {
          await refreshToken()
          // Retry fetch after refresh
          return await fetchUser()
        } catch (refreshErr) {
          // Refresh failed, clear auth data
          clearAuthData()
        }
      }
    } catch (err) {
      console.error('Failed to fetch user:', err)
      // Try to refresh token if it's an auth error
      if (err.message?.includes('401') || err.message?.includes('Unauthorized')) {
        try {
          await refreshToken()
          return await fetchUser()
        } catch (refreshErr) {
          clearAuthData()
        }
      } else {
        clearAuthData()
      }
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

  // Initialize from storage (localStorage or sessionStorage based on remember_me)
  const initializeAuth = () => {
    initializeToken()
    
    const storage = rememberMe.value ? localStorage : sessionStorage
    const storedUserData = storage.getItem('user_data')
    
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
    refreshToken,
    fetchUser,
    hasPermission,
    hasAnyPermission,
    hasRole,
    hasAnyRole,
    initializeAuth,

    // State
    rememberMe,

    // Constants
    ROLES
  }
})
