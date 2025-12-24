/**
 * Auth Store
 * Handles authentication state and user session
 */

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const isAuthenticated = computed(() => !!user.value)
    const currentUser = computed(() => user.value)

    // Check if session cookie exists
    const hasSessionCookie = () => {
        return document.cookie.includes('laravel_session')
    }

    // Fetch current user from API
    const fetchUser = async () => {
        try {
            const response = await axios.get('/api/user')
            user.value = response.data
            return response.data
        } catch (error) {
            console.error('Failed to fetch user:', error)
            user.value = null
            return null
        }
    }

    // Login
    const login = async (credentials) => {
        try {
            const response = await axios.post('/api/login', credentials)
            user.value = response.data.user
            return response.data
        } catch (error) {
            console.error('Login failed:', error)
            throw error
        }
    }

    // Logout
    const logout = async () => {
        try {
            await axios.post('/api/logout')
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
        hasSessionCookie,
        fetchUser,
        login,
        logout
    }
})
