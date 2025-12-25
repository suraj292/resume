// filepath: frontend/composables/useAuth.ts
export const useAuth = () => {
    const authStore = useAuthStore()
    
    return {
        user: computed(() => authStore.user),
        isAuthenticated: computed(() => authStore.isAuthenticated),
        currentUser: computed(() => authStore.currentUser),
        loading: computed(() => authStore.loading),
        error: computed(() => authStore.error),
        fetchUser: authStore.fetchUser,
        login: authStore.login,
        register: authStore.register,
        logout: authStore.logout,
        clearError: authStore.clearError
    }
}
