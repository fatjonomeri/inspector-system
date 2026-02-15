import { Configuration, JobApi, LocationApi, UserApi } from './api'
import axios, { type AxiosInstance } from 'axios'

// Create axios instance with credentials for session-based auth
const axiosInstance: AxiosInstance = axios.create({
  baseURL: '',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

const configuration = new Configuration({
  basePath: '',
  baseOptions: {
    withCredentials: true,
  },
})

// Export configured API instances
export const jobApi = new JobApi(configuration, undefined, axiosInstance)
export const locationApi = new LocationApi(
  configuration,
  undefined,
  axiosInstance,
)
export const userApi = new UserApi(configuration, undefined, axiosInstance)

export * from './api'
