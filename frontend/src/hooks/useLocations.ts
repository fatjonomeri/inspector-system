import { useQuery } from '@tanstack/react-query'
import { locationApi } from '@/generated/client'

export const locationKeys = {
  all: ['locations'] as const,
  lists: () => [...locationKeys.all, 'list'] as const,
  list: (filters?: { page?: number }) =>
    [...locationKeys.lists(), filters] as const,
  details: () => [...locationKeys.all, 'detail'] as const,
  detail: (id: string) => [...locationKeys.details(), id] as const,
}

export function useLocations(filters?: { page?: number }) {
  return useQuery({
    queryKey: locationKeys.list(filters),
    queryFn: async () => {
      const response = await locationApi.apiLocationsGetCollection(
        filters?.page,
      )
      return response.data
    },
  })
}

export function useLocation(id: string | undefined) {
  return useQuery({
    queryKey: locationKeys.detail(id || ''),
    queryFn: async () => {
      if (!id) throw new Error('Location ID is required')
      const response = await locationApi.apiLocationsIdGet(id)
      return response.data
    },
    enabled: !!id,
  })
}
