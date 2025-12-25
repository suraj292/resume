// filepath: frontend-ssr/middleware/auth.ts
export default defineNuxtRouteMiddleware(async (to, from) => {
    const { isAuthenticated, fetchUser } = useAuth()

    // Try to fetch user if not already authenticated
    if (!isAuthenticated.value) {
        await fetchUser()
    }

    // If still not authenticated, redirect to auth page
    if (!isAuthenticated.value) {
        return navigateTo({
            path: '/auth',
            query: { redirect: to.fullPath }
        })
    }
})
