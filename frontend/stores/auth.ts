import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any,
    loading: false,
    error: null as string | null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.user,
    currentUser: (state) => state.user,
  },

  actions: {
    async fetchUser() {
      if (typeof window === 'undefined') return null

      try {
        this.loading = true
        this.error = null

        const config = useRuntimeConfig()
        const token = localStorage.getItem('auth_token')
        console.log('Fetching user with token:', token ? 'Token exists' : 'No token')

        const headers: any = {}
        if (token) {
          headers['Authorization'] = `Bearer ${token}`
        }
        headers['Content-Type'] = 'application/json'
        headers['Accept'] = 'application/json'

        const response: any = await $fetch('/api/v1/user', {
          baseURL: config.public.apiBase,
          credentials: 'include',
          headers
        })
        // The API returns { user, capabilities }
        this.user = response.user || response
        return this.user
      } catch (error: any) {
        console.error('Failed to fetch user:', error)
        this.user = null
        this.error = error.message || 'Failed to fetch user'
        return null
      } finally {
        this.loading = false
      }
    },

    async login(credentials: { email: string; password: string }) {
      if (typeof window === 'undefined') return null

      try {
        this.loading = true
        this.error = null

        const config = useRuntimeConfig()
        const data: any = await $fetch('/api/v1/login', {
          method: 'POST',
          baseURL: config.public.apiBase,
          body: credentials,
          credentials: 'include'
        })

        // Store token in localStorage
        if (data.token) {
          localStorage.setItem('auth_token', data.token)
        }

        this.user = data.user
        return data
      } catch (error: any) {
        console.error('Login failed:', error)
        this.error = error.message || 'Login failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async register(userData: any) {
      if (typeof window === 'undefined') return null

      try {
        this.loading = true
        this.error = null

        const config = useRuntimeConfig()
        const data: any = await $fetch('/api/v1/register', {
          method: 'POST',
          baseURL: config.public.apiBase,
          body: userData,
          credentials: 'include'
        })

        // Store token in localStorage
        if (data.token) {
          localStorage.setItem('auth_token', data.token)
        }

        this.user = data.user
        return data
      } catch (error: any) {
        console.error('Registration failed:', error)
        this.error = error.message || 'Registration failed'
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      if (typeof window === 'undefined') return

      try {
        this.loading = true
        const config = useRuntimeConfig()
        const token = localStorage.getItem('auth_token')
        const headers: any = {}
        if (token) {
          headers['Authorization'] = `Bearer ${token}`
        }

        await $fetch('/api/v1/logout', {
          method: 'POST',
          baseURL: config.public.apiBase,
          credentials: 'include',
          headers
        })

        // Clear token from localStorage
        localStorage.removeItem('auth_token')
        this.user = null
      } catch (error: any) {
        console.error('Logout failed:', error)
        // Clear token anyway
        localStorage.removeItem('auth_token')
        this.user = null
      } finally {
        this.loading = false
      }
    },

    clearError() {
      this.error = null
    }
  }
})
