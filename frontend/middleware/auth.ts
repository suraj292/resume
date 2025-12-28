// filepath: frontend-ssr/middleware/auth.ts
export default defineNuxtRouteMiddleware(async (to, from) => {
    const { isAuthenticated, fetchUser } = useAuth()

    // Try to fetch user if not already authenticated
    if (!isAuthenticated.value) {
        try {
            await fetchUser()
        } catch (error) {
            // If fetchUser fails (e.g., backend offline), still redirect to auth
            return navigateTo({
                path: '/auth',
                query: { redirect: to.fullPath }
            })
        }
    }

    // If still not authenticated after fetching, redirect to auth page
    if (!isAuthenticated.value) {
        return navigateTo({
            path: '/auth',
            query: { redirect: to.fullPath }
        })
    }
})
