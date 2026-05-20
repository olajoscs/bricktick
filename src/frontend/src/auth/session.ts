const ACCESS_TOKEN_KEY = 'access_token'

export function isLoggedIn(): boolean {
  return localStorage.getItem(ACCESS_TOKEN_KEY) !== null
}

export function getAccessToken(): string | null {
  return localStorage.getItem(ACCESS_TOKEN_KEY)
}

export function setAccessToken(token: string): void {
  localStorage.setItem(ACCESS_TOKEN_KEY, token)
}

export function clearSession(): void {
  localStorage.removeItem(ACCESS_TOKEN_KEY)
}
