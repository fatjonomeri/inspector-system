import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import {
  authService,
  type LoginRequest,
  type RegisterRequest,
} from '@/services/authService'

// Query Keys
export const authKeys = {
  currentUser: ['auth', 'currentUser'] as const,
}

export function useCurrentUser() {
  return useQuery({
    queryKey: authKeys.currentUser,
    queryFn: authService.getCurrentUser,
    retry: false,
    staleTime: 5 * 60 * 1000, // 5 minutes
    refetchOnWindowFocus: false,
    throwOnError: false,
  })
}

export function useLogin() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (credentials: LoginRequest) => authService.login(credentials),
    onSuccess: async () => {
      await queryClient.refetchQueries({ queryKey: authKeys.currentUser })
    },
  })
}

export function useRegister() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (userData: RegisterRequest) => authService.register(userData),
    onSuccess: async () => {
      await queryClient.refetchQueries({ queryKey: authKeys.currentUser })
    },
  })
}

export function useLogout() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: () => authService.logout(),
    onSuccess: () => {
      queryClient.setQueryData(authKeys.currentUser, null)
      queryClient.clear()
    },
  })
}
