import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Builder from '../views/Builder.vue'
import AtsChecker from '../views/AtsChecker.vue'
import Templates from '../views/Templates.vue'
import Pricing from '../views/Pricing.vue'
import Auth from '../views/Auth.vue'
import Contact from '../views/Contact.vue' // Import Contact view
import Blog from '../views/Blog.vue'
import BlogDetail from '../views/BlogDetail.vue'

const routes = [
    { path: '/', name: 'Home', component: Home },
    { path: '/builder', name: 'Builder', component: Builder },
    { path: '/ats-checker', name: 'AtsChecker', component: AtsChecker },
    { path: '/templates', name: 'Templates', component: Templates },
    { path: '/pricing', name: 'Pricing', component: Pricing },
    { path: '/auth', name: 'Auth', component: Auth },
    { path: '/contact', name: 'Contact', component: Contact }, // Add Contact route
    { path: '/blog', name: 'Blog', component: Blog },
    { path: '/blog/:id', name: 'BlogDetail', component: BlogDetail },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
