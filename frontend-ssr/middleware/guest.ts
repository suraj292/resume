// filepath: frontend-ssr/middleware/guest.ts
export default defineNuxtRouteMiddleware(async (to, from) => {
    const { isAuthenticated, fetchUser } = useAuth()

    // Check if user is authenticated
    if (!isAuthenticated.value) {
        await fetchUser()
    }

    // If authenticated, redirect to home
    if (isAuthenticated.value) {
        return navigateTo('/')
    }
})
