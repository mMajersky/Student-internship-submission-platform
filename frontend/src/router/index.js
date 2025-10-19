import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
const LandingView = () => import('../views/LandingView.vue')

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'landing',
      component: LandingView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { guest: true }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
      meta: { 
        requiresAuth: true,
        roles: ['admin', 'garant']
      }
    },
    // Future routes with role-based access
    {
      path: '/student-dashboard',
      name: 'student-dashboard',
      component: () => import('../views/StudentDashboardView.vue'),
      meta: { 
        requiresAuth: true,
        roles: ['student']
      }
    },
    {
      path: '/company-dashboard',
      name: 'company-dashboard',
      component: () => import('../views/CompanyDashboardView.vue'),
      meta: { 
        requiresAuth: true,
        roles: ['company']
      }
    },
    {
      path: '/garant-dashboard',
      name: 'garant-dashboard',
      component: () => import('../views/GarantDashboardView.vue'),
      meta: { 
        requiresAuth: true,
        roles: ['GARANT']
      }
    },
    {
      path: '/admin-panel',
      name: 'admin-panel',
      component: () => import('../views/AdminPanelView.vue'),
      meta: { 
        requiresAuth: true,
        roles: ['admin']
      }
    },
    {
      path: '/unauthorized',
      name: 'unauthorized',
      component: () => import('../views/UnauthorizedView.vue')
    }
  ]
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Initialize auth store if not already done
  if (!authStore.isAuthenticated && authStore.token) {
    await authStore.fetchUser()
  }

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const allowedRoles = to.meta.roles || []
  const isGuestRoute = to.matched.some(record => record.meta.guest)

  // Check if user is authenticated
  if (requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' })
    return
  }

  // Check if authenticated user is trying to access guest routes
  if (isGuestRoute && authStore.isAuthenticated) {
    // Redirect to appropriate dashboard based on role
    if (authStore.isAdmin || authStore.isGarant) {
      next({ name: 'dashboard' })
    } else if (authStore.isStudent) {
      next({ name: 'student-dashboard' })
    } else if (authStore.isCompany) {
      next({ name: 'company-dashboard' })
    } else {
      next({ name: 'landing' })
    }
    return
  }

  // Check role-based access
if (requiresAuth && allowedRoles.length > 0) {
  const normalizedRoles = allowedRoles.map(r => r.toLowerCase())
  const userRole = authStore.userRole?.toLowerCase()

  if (!normalizedRoles.includes(userRole)) {
    next({ name: 'unauthorized' })
    return
  }
}


  next()
})

export default router