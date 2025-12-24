import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('../views/Home.vue')
        },
        {
            path: '/builder',
            name: 'builder',
            component: () => import('../views/Builder.vue')
        },
        {
            path: '/templates',
            name: 'templates',
            component: () => import('../views/Templates.vue')
        },
        {
            path: '/ats-checker',
            name: 'ats-checker',
            component: () => import('../views/ATSChecker.vue')
        },
        {
            path: '/pricing',
            name: 'pricing',
            component: () => import('../views/Pricing.vue')
        },
        {
            path: '/auth',
            name: 'auth',
            component: () => import('../views/Auth.vue')
        },
        {
            path: '/contact',
            name: 'contact',
            component: () => import('../views/Contact.vue')
        },
        {
            path: '/blog',
            name: 'blog',
            component: () => import('../views/Blog.vue')
        },
        {
            path: '/blog/:id',
            name: 'blog-detail',
            component: () => import('../views/BlogDetail.vue')
        },
        {
            path: '/profile',
            name: 'profile',
            component: () => import('../views/Profile.vue')
        },
        {
            path: '/checkout',
            name: 'checkout',
            component: () => import('../views/Checkout.vue')
        }
    ]
})

export default router
