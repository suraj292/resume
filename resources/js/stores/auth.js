import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
    // State
    const user = ref(null)
    const token = ref(localStorage.getItem('auth_token'))

    // Getters
    const isAuthenticated = computed(() => {
        // Only consider authenticated if we have user data or a token
        // Don't rely on session cookies alone as they might be expired
        return !!(token.value || user.value)
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
                const data = await response.json()
                // API returns { user: {...}, capabilities: {...} }
                const userData = data.user || data
                setUser(userData)
                return userData
            } else {
                console.error('Failed to fetch user:', response.status, response.statusText)
                if (response.status === 401) {
                    clearAuth()
                }
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

    // Check if user just completed social login
    async function checkSocialLogin() {
        // If we have a session cookie but no user data, try to fetch
        if (hasSessionCookie() && !user.value) {
            console.log('Detected session cookie, fetching user data...')
            await fetchUser()
        }
    }

    // Initialize user from storage
    loadUser()
    
    // Check for social login on initialization
    checkSocialLogin()

    console.log('loggedInInfo', {
        // User: User,
        isAuthenticated: isAuthenticated.value,
        currentUser: currentUser.value
    })

    return {
        // State
        user,
        token,
        // Getters
        isAuthenticated,
        currentUser,
        // Helpers
        hasSessionCookie,
        checkSocialLogin,
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
