import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import Builder from '../pages/Builder.vue'
import AtsChecker from '../pages/AtsChecker.vue'
import Templates from '../pages/Templates.vue'
import Pricing from '../pages/Pricing.vue'
import Auth from '../pages/Auth.vue'
import Contact from '../pages/Contact.vue' // Import Contact view
import Blog from '../pages/Blog.vue'
import BlogDetail from '../pages/BlogDetail.vue'
import Profile from '../pages/Profile.vue'

const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/builder', name: 'Builder', component: Builder },
    { path: '/ats-checker', name: 'AtsChecker', component: AtsChecker },
    { path: '/templates', name: 'Templates', component: Templates },
    { path: '/pricing', name: 'Pricing', component: Pricing },
    { path: '/auth', name: 'Auth', component: Auth },
    { path: '/contact', name: 'Contact', component: Contact }, // Add Contact route
    { path: '/profile', name: 'Profile', component: Profile },
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
