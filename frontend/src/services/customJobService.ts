import axios from 'axios'

// Custom job endpoints that are not in OpenAPI spec
const jobAxios = axios.create({
  baseURL: '/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

export interface AssignJobRequest {
  scheduledDate: string // ISO date string
}

export interface CompleteJobRequest {
  assessment: string
  completedAt?: string // ISO date string, optional
}

// Custom Job type for custom endpoints (different from OpenAPI Job)
export interface CustomJob {
  id: number
  title: string
  description?: string | null
  status: 'available' | 'assigned' | 'completed'
  location: string // Just the code, not the full object
  createdAt: string
  updatedAt?: string | null
  scheduledDate?: string | null
  completedAt?: string | null
  assessment?: string | null
  timezone: string
  assignedTo?: {
    id: number
    email: string
    firstName?: string | null
    lastName?: string | null
    location: string
  }
}

export interface JobsResponse {
  success: boolean
  jobs: CustomJob[]
}

export const customJobService = {
  getAvailableJobs: async (): Promise<CustomJob[]> => {
    const { data } = await jobAxios.get<JobsResponse>('/jobs/available')
    return data.jobs
  },

  getMyJobs: async (): Promise<CustomJob[]> => {
    const { data } = await jobAxios.get<JobsResponse>('/me/jobs')
    return data.jobs
  },

  assignJob: async (id: number, payload: AssignJobRequest) => {
    const { data } = await jobAxios.post(`/jobs/${id}/assign`, payload)
    return data
  },

  completeJob: async (id: number, payload: CompleteJobRequest) => {
    const { data } = await jobAxios.post(`/jobs/${id}/complete`, payload)
    return data
  },
}
