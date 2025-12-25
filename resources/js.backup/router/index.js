import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import Builder from '../pages/Builder.vue'
import AtsChecker from '../pages/AtsChecker.vue'
import Templates from '../pages/Templates.vue'
import Pricing from '../pages/Pricing.vue'
import Auth from '../pages/Auth.vue'
import Contact from '../pages/Contact.vue'
import Blog from '../pages/Blog.vue'
import BlogDetail from '../pages/BlogDetail.vue'
import Profile from '../pages/Profile.vue'
import { useAuthStore } from '../stores/auth'

const routes = [
    { path: '/', name: 'Home', component: Home },
    {
        path: '/builder',
        name: 'Builder',
        component: Builder,
        meta: { requiresAuth: false }
    },
    {
        path: '/ats-checker',
        name: 'AtsChecker',
        component: AtsChecker,
        meta: { requiresAuth: false }
    },
    { path: '/templates', name: 'Templates', component: Templates },
    { path: '/pricing', name: 'Pricing', component: Pricing },
    {
        path: '/auth',
        name: 'Auth',
        component: Auth,
        meta: { requiresGuest: true }
    },
    { path: '/contact', name: 'Contact', component: Contact },
    {
        path: '/profile',
        name: 'Profile',
        component: Profile,
        meta: { requiresAuth: true }
    },
    { path: '/blog', name: 'Blog', component: Blog },
    { path: '/blog/:id', name: 'BlogDetail', component: BlogDetail },
    {
        path: '/checkout',
        name: 'Checkout',
        component: () => import('../pages/Checkout.vue'),
        meta: { requiresAuth: true }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return { top: 0 }
    }
})

// Navigation guard
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()

    // Check if route requires authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        // If there's a session cookie, try to fetch user first (for social login redirects)
        if (authStore.hasSessionCookie() && !authStore.currentUser) {
            const userData = await authStore.fetchUser()
            if (userData) {
                // User fetched successfully, proceed to route
                return next()
            }
        }
        
        // No valid session, store intended route and redirect to auth
        sessionStorage.setItem('intended_route', to.path)
        next({ name: 'Auth' })
    }
    // Redirect authenticated users from auth page to builder or intended route
    else if (to.meta.requiresGuest && authStore.isAuthenticated) {
        const intendedRoute = sessionStorage.getItem('intended_route')
        if (intendedRoute) {
            sessionStorage.removeItem('intended_route')
            next(intendedRoute)
        } else {
            next({ name: 'Builder' })
        }
    }
    else {
        next()
    }
})

export default router
