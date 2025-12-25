export const useApi = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const apiFetch = async (endpoint, options = {}) => {
    const url = `${apiBase}${endpoint}`
    
    try {
      const response = await $fetch(url, {
        ...options,
        credentials: 'include',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          ...options.headers,
        },
      })
      return response
    } catch (error) {
      console.error('API Error:', error)
      throw error
    }
  }

  return {
    apiFetch,
    
    // Resume API
    extractText: (formData) => apiFetch('/api/extract-text', {
      method: 'POST',
      body: formData,
      headers: {}, // Let browser set Content-Type for FormData
    }),
    
    generateTemplates: (data) => apiFetch('/api/generate-templates', {
      method: 'POST',
      body: JSON.stringify(data),
    }),
    
    optimizeResume: (data) => apiFetch('/api/optimize-resume', {
      method: 'POST',
      body: JSON.stringify(data),
    }),
    
    // ATS Checker API
    checkATS: (formData) => apiFetch('/api/ats-check', {
      method: 'POST',
      body: formData,
      headers: {},
    }),
    
    // Blog API
    getBlogs: (params) => apiFetch('/api/blogs', {
      method: 'GET',
      params,
    }),
    
    getBlogBySlug: (slug) => apiFetch(`/api/blogs/${slug}`),
    
    getFeaturedBlogs: () => apiFetch('/api/blogs/featured'),
    
    getTrendingBlogs: () => apiFetch('/api/blogs/trending'),
    
    getCategories: () => apiFetch('/api/categories'),
    
    // Contact API
    submitContact: (data) => apiFetch('/api/contact', {
      method: 'POST',
      body: JSON.stringify(data),
    }),
    
    // Auth API
    login: (credentials) => apiFetch('/api/login', {
      method: 'POST',
      body: JSON.stringify(credentials),
    }),
    
    register: (userData) => apiFetch('/api/register', {
      method: 'POST',
      body: JSON.stringify(userData),
    }),
    
    logout: () => apiFetch('/api/logout', {
      method: 'POST',
    }),
    
    getUser: () => apiFetch('/api/user'),
    
    // Profile API
    updateProfile: (data) => apiFetch('/api/profile', {
      method: 'PUT',
      body: JSON.stringify(data),
    }),
    
    // Pricing/Checkout API
    createCheckoutSession: (planId) => apiFetch('/api/checkout/create-session', {
      method: 'POST',
      body: JSON.stringify({ plan_id: planId }),
    }),
  }
}
