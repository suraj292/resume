// filepath: frontend-ssr/composables/useAuth.ts
export const useAuth = () => {
    const user = useState<any>('user', () => null)
    const config = useRuntimeConfig()

    const isAuthenticated = computed(() => !!user.value)
    const currentUser = computed(() => user.value)

    const fetchUser = async () => {
        try {
            const data = await $fetch('/api/user', {
                baseURL: config.public.apiBase,
                credentials: 'include'
            })
            user.value = data
            return data
        } catch (error) {
            console.error('Failed to fetch user:', error)
            user.value = null
            return null
        }
    }

    const login = async (credentials: { email: string; password: string }) => {
        try {
            const data = await $fetch('/api/login', {
                method: 'POST',
                baseURL: config.public.apiBase,
                body: credentials,
                credentials: 'include'
            })
            user.value = data.user
            return data
        } catch (error) {
            console.error('Login failed:', error)
            throw error
        }
    }

    const register = async (userData: any) => {
        try {
            const data = await $fetch('/api/register', {
                method: 'POST',
                baseURL: config.public.apiBase,
                body: userData,
                credentials: 'include'
            })
            user.value = data.user
            return data
        } catch (error) {
            console.error('Registration failed:', error)
            throw error
        }
    }

    const logout = async () => {
        try {
            await $fetch('/api/logout', {
                method: 'POST',
                baseURL: config.public.apiBase,
                credentials: 'include'
            })
            user.value = null
        } catch (error) {
            console.error('Logout failed:', error)
            user.value = null
        }
    }

    return {
        user,
        isAuthenticated,
        currentUser,
        fetchUser,
        login,
        register,
        logout
    }
}
