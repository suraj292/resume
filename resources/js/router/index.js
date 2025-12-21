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
        meta: { requiresAuth: true }
    },
    {
        path: '/ats-checker',
        name: 'AtsChecker',
        component: AtsChecker,
        meta: { requiresAuth: true }
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
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return { top: 0 }
    }
})

// Navigation guard
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore()

    // Check if route requires authentication
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        // Store intended route
        sessionStorage.setItem('intended_route', to.path)
        sessionStorage.setItem('show_auth_modal', 'true')
        next({ name: 'Home' })
    }
    // Redirect authenticated users from auth page to profile
    else if (to.meta.requiresGuest && authStore.isAuthenticated) {
        next({ name: 'Profile' })
    }
    else {
        next()
    }
})

export default router
