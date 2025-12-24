import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            redirect: '/builder'
        },
        {
            path: '/builder',
            name: 'builder',
            component: () => import('../views/Builder.vue')
        }
    ]
})

export default router
