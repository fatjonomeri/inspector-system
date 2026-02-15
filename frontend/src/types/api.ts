export interface User {
  id: number;
  email: string;
  firstName: string;
  lastName: string;
  roles: string[];
  location: Location;
}

export interface Location {
  id: number;
  name: string;
  code: string;
  timezone: string;
  countryCode: string;
}

export interface Job {
  id: number;
  title: string;
  description?: string;
  status: 'available' | 'assigned' | 'completed';
  assignedTo?: User;
  scheduledDate?: string;
  completedAt?: string;
  assessment?: string;
  location: Location;
  createdAt: string;
  updatedAt?: string;
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  locationId: number;
}

export interface AssignJobRequest {
  scheduledDate: string;
}

export interface CompleteJobRequest {
  assessment: string;
  completedAt?: string;
}

export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  message?: string;
}

export interface JobsResponse {
  success: boolean;
  jobs: Job[];
}
