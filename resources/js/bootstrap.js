import axios from 'axios'
import { useAuthStore } from './stores/auth'

// Set up axios defaults
axios.defaults.baseURL = import.meta.env.VITE_API_URL || '/api'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'

// Request interceptor to add auth token
axios.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }
    
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor to handle auth errors
axios.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    const authStore = useAuthStore()
    
    if (error.response?.status === 401) {
      // Token expired or invalid
      authStore.logout()
      window.location.href = '/auth/login'
    }
    
    return Promise.reject(error)
  }
)

// Export axios instance
export default axios
