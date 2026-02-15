import { useQuery } from '@tanstack/react-query'
import { userApi } from '@/generated/client'

export const userKeys = {
  all: ['users'] as const,
  lists: () => [...userKeys.all, 'list'] as const,
  list: (filters?: { page?: number }) =>
    [...userKeys.lists(), filters] as const,
  details: () => [...userKeys.all, 'detail'] as const,
  detail: (id: string) => [...userKeys.details(), id] as const,
  current: () => [...userKeys.all, 'current'] as const,
}

export function useUsers(filters?: { page?: number }) {
  return useQuery({
    queryKey: userKeys.list(filters),
    queryFn: async () => {
      const response = await userApi.apiUsersGetCollection(filters?.page)
      return response.data
    },
  })
}

export function useUser(id: string | undefined) {
  return useQuery({
    queryKey: userKeys.detail(id || ''),
    queryFn: async () => {
      if (!id) throw new Error('User ID is required')
      const response = await userApi.apiUsersIdGet(id)
      return response.data
    },
    enabled: !!id,
  })
}
