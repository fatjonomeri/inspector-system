import api from '@/lib/api'
import type {
  User,
  Location,
  Job,
  LoginRequest,
  RegisterRequest,
  AssignJobRequest,
  CompleteJobRequest,
  JobsResponse,
} from '@/types/api'

export const authApi = {
  login: async (credentials: LoginRequest) => {
    const { data } = await api.post('/auth/login', credentials)
    return data
  },

  register: async (userData: RegisterRequest) => {
    const { data } = await api.post('/auth/register', userData)
    return data
  },

  logout: async () => {
    const { data } = await api.post('/auth/logout')
    return data
  },

  getCurrentUser: async (): Promise<User> => {
    const { data } = await api.get('/me')
    return data
  },
}

export const jobsApi = {
  getAvailableJobs: async (): Promise<Job[]> => {
    const { data } = await api.get<JobsResponse>('/jobs/available')
    return data.jobs
  },

  getMyJobs: async (): Promise<Job[]> => {
    const { data } = await api.get<JobsResponse>('/me/jobs')
    return data.jobs
  },

  getJobById: async (id: number): Promise<Job> => {
    const { data } = await api.get(`/jobs/${id}`)
    return data
  },

  assignJob: async (id: number, payload: AssignJobRequest) => {
    const { data } = await api.post(`/jobs/${id}/assign`, payload)
    return data
  },

  completeJob: async (id: number, payload: CompleteJobRequest) => {
    const { data } = await api.post(`/jobs/${id}/complete`, payload)
    return data
  },
}

export const locationsApi = {
  getAll: async (): Promise<Location[]> => {
    const { data } = await api.get('/locations')
    return data['hydra:member'] || data
  },
}
