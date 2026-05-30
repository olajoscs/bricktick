export type LoginResponse = {
  access_token: string
  token_type: string
  expires_in: number
}

import { ApiError, apiFetch } from './client'
import { asApiErrorBody, type ApiErrorBody } from './errors'

export type LoginErrorBody = ApiErrorBody

export class LoginError extends Error {
  status: number
  body: LoginErrorBody

  constructor(status: number, body: LoginErrorBody) {
    super(body.message ?? 'Login failed.')
    this.status = status
    this.body = body
  }
}

export async function login(
  email: string,
  password: string,
): Promise<LoginResponse> {
  try {
    return await apiFetch<LoginResponse>('/api/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ email, password }),
    })
  } catch (error) {
    if (error instanceof ApiError) {
      throw new LoginError(error.status, asApiErrorBody(error.body))
    }

    throw error
  }
}

export type GuestSessionResponse = {
  guest: true
}

export type AuthMeResponse = {
  authenticated: true
  user: {
    name: string
    email: string
  }
}

export function fetchGuestSession(): Promise<GuestSessionResponse> {
  return apiFetch<GuestSessionResponse>('/api/auth/session')
}

export function fetchAuthMe(): Promise<AuthMeResponse> {
  return apiFetch<AuthMeResponse>('/api/auth/me')
}
