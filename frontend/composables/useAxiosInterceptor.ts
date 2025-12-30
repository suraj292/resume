import { ref } from 'vue'
import type { AxiosInstance, AxiosError, InternalAxiosRequestConfig, AxiosResponse } from 'axios'

/**
 * Global loading state
 */
const isLoading = ref(false)
const activeRequests = ref(0)
const loadingQueue = ref<Set<string>>(new Set())

/**
 * Composable for managing global loading state with Axios interceptors
 */
export const useAxiosInterceptor = () => {
    /**
     * Setup interceptors for an Axios instance
     * @param axiosInstance - Axios instance to attach interceptors to
     */
    const setupInterceptors = (axiosInstance: AxiosInstance) => {
        // Request interceptor
        axiosInstance.interceptors.request.use(
            (config: InternalAxiosRequestConfig) => {
                const requestId = `${config.method}-${config.url}`
                loadingQueue.value.add(requestId)
                activeRequests.value++
                isLoading.value = true

                return config
            },
            (error: AxiosError) => {
                activeRequests.value--
                if (activeRequests.value === 0) {
                    isLoading.value = false
                    loadingQueue.value.clear()
                }
                return Promise.reject(error)
            }
        )

        // Response interceptor
        axiosInstance.interceptors.response.use(
            (response: AxiosResponse) => {
                const requestId = `${response.config.method}-${response.config.url}`
                loadingQueue.value.delete(requestId)
                activeRequests.value--

                if (activeRequests.value === 0) {
                    isLoading.value = false
                    loadingQueue.value.clear()
                }

                return response
            },
            (error: AxiosError) => {
                if (error.config) {
                    const requestId = `${error.config.method}-${error.config.url}`
                    loadingQueue.value.delete(requestId)
                }

                activeRequests.value--

                if (activeRequests.value === 0) {
                    isLoading.value = false
                    loadingQueue.value.clear()
                }

                // Handle common errors
                if (error.response) {
                    switch (error.response.status) {
                        case 401:
                            // Unauthorized - redirect to login
                            if (process.client) {
                                window.location.href = '/login'
                            }
                            break
                        case 429:
                            // Too many requests
                            console.warn('Rate limit exceeded. Please try again later.')
                            break
                        case 500:
                            // Server error
                            console.error('Server error occurred')
                            break
                    }
                }

                return Promise.reject(error)
            }
        )
    }

    /**
     * Reset loading state (useful for error recovery)
     */
    const resetLoadingState = () => {
        isLoading.value = false
        activeRequests.value = 0
        loadingQueue.value.clear()
    }

    return {
        isLoading,
        activeRequests,
        setupInterceptors,
        resetLoadingState
    }
}
