import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
// Importy sú v poriadku, môžeme ich nechať
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
        roles: ['ADMIN', 'GARANT']
      }
    },
    {
      path: '/student-dashboard',
      name: 'student-dashboard',
      component: () => import('../views/StudentDashboardView.vue'),
      meta: { 
        requiresAuth: true,
        roles: ['STUDENT']
      }
    },
    {
      // --- OPRAVENÉ TU ---
      path: '/create-internship', // Pridané písmeno 'n'
      name: 'create-internship',
      component: () => import('../views/StudentCreateIntershipView.vue'),
      meta: { 
        requiresAuth: true,
        roles: ['STUDENT']
      }
    },
    {
      // --- OPRAVENÉ AJ TU ---
      path: '/internships', // Pridané písmeno 'n'
      name: 'internships',
      component: () => import('../views/StudentIntershipView.vue'),
      meta: { 
        requiresAuth: true,
        roles: ['STUDENT']
      }
    },
    {
      path: '/company-dashboard',
      name: 'company-dashboard',
      component: () => import('../views/CompanyDashboardView.vue'),
      meta: { 
        requiresAuth: true,
        roles: ['COMPANY']
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
        roles: ['ADMIN']
      }
    },
    {
      path: '/unauthorized',
      name: 'unauthorized',
      component: () => import('../views/UnauthorizedView.vue')
    }
  ]
})

// Váš beforeEach guard je v poriadku, nie je potrebné ho meniť
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  if (!authStore.isAuthenticated && authStore.token) {
    await authStore.fetchUser()
  }

  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const allowedRoles = to.meta.roles || []
  const isGuestRoute = to.matched.some(record => record.meta.guest)

  if (requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' })
    return
  }

  if (isGuestRoute && authStore.isAuthenticated) {
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