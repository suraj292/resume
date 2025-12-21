import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import Builder from '../pages/Builder.vue'
import AtsChecker from '../pages/AtsChecker.vue' // Original import
import Templates from '../pages/Templates.vue'
import Pricing from '../pages/Pricing.vue'
import Auth from '../pages/Auth.vue'
import Contact from '../pages/Contact.vue' // Import Contact view
import Blog from '../pages/Blog.vue'
import BlogDetail from '../pages/BlogDetail.vue'
import Profile from '../pages/Profile.vue'

// Check if user is authenticated
const isAuthenticated = () => {
    // Check for Laravel session or token
    const token = localStorage.getItem('auth_token')
    const sessionUser = document.querySelector('meta[name="user-id"]')
    return !!(token || (sessionUser && sessionUser.content))
}

const requireAuth = (to, from, next) => {
    if (isAuthenticated()) {
        next()
    } else {
        // Store intended route and trigger modal
        sessionStorage.setItem('intended_route', to.path)
        sessionStorage.setItem('show_auth_modal', 'true')
        next({ name: 'Home' })
    }
}

const routes = [
    { path: '/', name: 'Home', component: Home },
    {
        path: '/builder',
        name: 'Builder',
        component: Builder,
        beforeEnter: requireAuth // Added authentication guard
    },
    {
        path: '/ats-checker',
        name: 'AtsChecker', // Kept original name, but component is AtsChecker
        component: AtsChecker,
        beforeEnter: requireAuth // Added authentication guard
    },
    { path: '/templates', name: 'Templates', component: Templates },
    { path: '/pricing', name: 'Pricing', component: Pricing },
    { path: '/auth', name: 'Auth', component: Auth },
    { path: '/contact', name: 'Contact', component: Contact }, // Add Contact route
    {
        path: '/profile',
        name: 'Profile',
        component: Profile,
        beforeEnter: requireAuth // Added authentication guard
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

export default router
