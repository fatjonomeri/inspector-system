import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { jobApi } from '@/generated/client'
import type { ApiJobsPostRequest } from '@/generated/client'
import {
  customJobService,
  type AssignJobRequest,
  type CompleteJobRequest,
} from '@/services/customJobService'

export const jobKeys = {
  all: ['jobs'] as const,
  lists: () => [...jobKeys.all, 'list'] as const,
  list: (filters?: { page?: number; status?: string; locationId?: number }) =>
    [...jobKeys.lists(), filters] as const,
  available: () => [...jobKeys.all, 'available'] as const,
  myJobs: () => [...jobKeys.all, 'myJobs'] as const,
  details: () => [...jobKeys.all, 'detail'] as const,
  detail: (id: string) => [...jobKeys.details(), id] as const,
}

export function useJobs(filters?: {
  page?: number
  status?: string
  locationId?: number
}) {
  return useQuery({
    queryKey: jobKeys.list(filters),
    queryFn: async () => {
      const response = await jobApi.apiJobsGetCollection(
        filters?.page,
        filters?.status,
        filters?.status ? [filters.status] : undefined,
        filters?.locationId,
        filters?.locationId ? [filters.locationId] : undefined,
      )
      return response.data
    },
  })
}

export function useJob(id: string | undefined) {
  return useQuery({
    queryKey: jobKeys.detail(id || ''),
    queryFn: async () => {
      if (!id) throw new Error('Job ID is required')
      const response = await jobApi.apiJobsIdGet(id)
      return response.data
    },
    enabled: !!id,
  })
}

export function useCreateJob() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: async (data: ApiJobsPostRequest) => {
      const response = await jobApi.apiJobsPost(data)
      return response.data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: jobKeys.lists() })
    },
  })
}

export function useAvailableJobs() {
  return useQuery({
    queryKey: jobKeys.available(),
    queryFn: customJobService.getAvailableJobs,
  })
}

export function useMyJobs() {
  return useQuery({
    queryKey: jobKeys.myJobs(),
    queryFn: customJobService.getMyJobs,
  })
}

export function useAssignJob() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: ({ id, payload }: { id: number; payload: AssignJobRequest }) =>
      customJobService.assignJob(id, payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: jobKeys.available() })
      queryClient.invalidateQueries({ queryKey: jobKeys.myJobs() })
    },
  })
}

export function useCompleteJob() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: ({
      id,
      payload,
    }: {
      id: number
      payload: CompleteJobRequest
    }) => customJobService.completeJob(id, payload),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: jobKeys.myJobs() })
    },
  })
}

export function useAssignedJobs() {
  return useJobs({ status: 'assigned' })
}

export function useCompletedJobs() {
  return useJobs({ status: 'completed' })
}
