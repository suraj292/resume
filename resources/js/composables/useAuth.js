import { ref } from 'vue'

// Global user state
const user = ref(null)
const isAuthenticated = ref(false)

export function useAuth() {
    // Load user from session storage on init
    const loadUser = () => {
        const storedUser = sessionStorage.getItem('user')
        if (storedUser) {
            try {
                user.value = JSON.parse(storedUser)
                isAuthenticated.value = true
            } catch (e) {
                console.error('Failed to parse stored user:', e)
            }
        }
    }

    // Set user data
    const setUser = (userData) => {
        user.value = userData
        isAuthenticated.value = true
        sessionStorage.setItem('user', JSON.stringify(userData))
    }

    // Logout user
    const logout = async () => {
        try {
            // Call logout API
            await fetch('/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
        } catch (e) {
            console.error('Logout error:', e)
        } finally {
            // Clear local state
            user.value = null
            isAuthenticated.value = false
            sessionStorage.removeItem('user')
            localStorage.removeItem('auth_token')

            // Redirect to home
            window.location.href = '/'
        }
    }

    // Fetch current user
    const fetchUser = async () => {
        try {
            const response = await fetch('/api/user', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'include'
            })

            if (response.ok) {
                const userData = await response.json()
                setUser(userData)
                return userData
            }
        } catch (e) {
            console.error('Failed to fetch user:', e)
        }
        return null
    }

    // Social login - redirect to OAuth provider
    const socialLogin = (provider) => {
        window.location.href = `/api/auth/${provider}/redirect`
    }

    return {
        user,
        isAuthenticated,
        loadUser,
        setUser,
        logout,
        fetchUser,
        socialLogin
    }
}
