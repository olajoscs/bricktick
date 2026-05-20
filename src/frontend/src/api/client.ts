import { apiUrl } from '../config/apiBase'
import { getAccessToken } from '../auth/session'
import { asApiErrorBody, getApiErrorMessage } from './errors'

export class ApiError extends Error {
  status: number
  body: unknown

  constructor(status: number, body: unknown, message?: string) {
    super(message ?? getApiErrorMessage(body, 'Request failed.'))
    this.status = status
    this.body = body
  }
}

export function isApiError(error: unknown): error is ApiError {
  return error instanceof ApiError
}

export function getRedirectFromError(error: ApiError): string | undefined {
  return asApiErrorBody(error.body).redirect
}

export async function apiFetch<T>(path: string, init?: RequestInit): Promise<T> {
  const token = getAccessToken()

  const headers = new Headers(init?.headers)
  headers.set('Accept', 'application/json')
  if (token) {
    headers.set('Authorization', `Bearer ${token}`)
  }

  const response = await fetch(apiUrl(path), { ...init, headers })
  const body: unknown = await response.json().catch(() => ({}))

  if (!response.ok) {
    throw new ApiError(response.status, body)
  }

  return body as T
}
