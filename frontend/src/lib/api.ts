import axios from 'axios'

const api = axios.create({
  // We use the relative path so the Vite proxy catches it
  baseURL: '/api',
  withCredentials: true, // REQUIRED for session auth
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Add response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Redirect to login on unauthorized
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }
    return Promise.reject(error)
  },
)

export default api
