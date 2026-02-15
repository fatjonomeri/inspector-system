import axios from 'axios'

const authAxios = axios.create({
  baseURL: '/api/auth',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

export interface LoginRequest {
  email: string
  password: string
}

export interface RegisterRequest {
  email: string
  password: string
  firstName?: string
  lastName?: string
  location: string // IRI like "/api/locations/1"
}

export interface AuthResponse {
  message: string
  user?: {
    id: number
    email: string
    roles: string[]
    firstName?: string | null
    lastName?: string | null
    location: string
    timezone?: string
  }
}

export const authService = {
  login: async (credentials: LoginRequest): Promise<AuthResponse> => {
    const { data } = await authAxios.post<AuthResponse>('/login', credentials)
    return data
  },

  register: async (userData: RegisterRequest): Promise<AuthResponse> => {
    const { data } = await authAxios.post<AuthResponse>('/register', userData)
    return data
  },

  logout: async (): Promise<{ message: string }> => {
    const { data } = await authAxios.post<{ message: string }>('/logout')
    return data
  },

  getCurrentUser: async () => {
    // Use the main API client for /me endpoint
    const { data } = await axios.get('/api/me', {
      withCredentials: true,
    })
    return data
  },
}
