import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
    // State
    const user = ref(null)
    const token = ref(localStorage.getItem('auth_token'))

    // Getters
    const isAuthenticated = computed(() => {
        return !!(token.value || user.value || hasSessionCookie())
    })

    const currentUser = computed(() => user.value)

    // Helper to check session cookies
    function hasSessionCookie() {
        return document.cookie.includes('XSRF-TOKEN') ||
            document.cookie.includes('laravel_session')
    }

    // Actions
    function setUser(userData) {
        user.value = userData
        sessionStorage.setItem('user', JSON.stringify(userData))
    }

    function setToken(authToken) {
        token.value = authToken
        localStorage.setItem('auth_token', authToken)
    }

    function setAuth(authToken, userData) {
        setToken(authToken)
        setUser(userData)
    }

    async function fetchUser() {
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

    async function logout() {
        try {
            await fetch('/logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'include'
            })
        } catch (e) {
            console.error('Logout error:', e)
        } finally {
            clearAuth()
            window.location.href = '/'
        }
    }

    function clearAuth() {
        user.value = null
        token.value = null
        sessionStorage.removeItem('user')
        localStorage.removeItem('auth_token')
    }

    function loadUser() {
        const storedUser = sessionStorage.getItem('user')
        if (storedUser) {
            try {
                user.value = JSON.parse(storedUser)
            } catch (e) {
                console.error('Failed to parse stored user:', e)
            }
        }
    }

    function socialLogin(provider) {
        window.location.href = `/api/auth/${provider}/redirect`
    }

    // Initialize user from storage
    loadUser()

    return {
        // State
        user,
        token,
        // Getters
        isAuthenticated,
        currentUser,
        // Actions
        setUser,
        setToken,
        setAuth,
        fetchUser,
        logout,
        clearAuth,
        loadUser,
        socialLogin
    }
})
