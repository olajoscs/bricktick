import { apiFetch } from './client'

export type DashboardResponse = {
  message: string
  user: {
    name: string
    email: string
  }
  stats: {
    active_projects: number
    pending_tasks: number
  }
}

export function fetchDashboard(): Promise<DashboardResponse> {
  return apiFetch<DashboardResponse>('/api/dashboard')
}
