import { getRedirectFromError, isApiError } from '../api/client'
import { clearSession } from './session'

function notifyAuthChanged() {
  window.dispatchEvent(new Event('bricktick:auth-changed'))
}

type AuthErrorHandlers = {
  onUnauthenticated: () => void
  onAlreadyAuthenticated: (redirect: string) => void
}

export function handleAuthError(
  error: unknown,
  handlers: AuthErrorHandlers,
): boolean {
  if (!isApiError(error)) {
    return false
  }

  if (error.status === 401) {
    clearSession()
    notifyAuthChanged()
    handlers.onUnauthenticated()
    return true
  }

  if (error.status === 403) {
    const redirect = getRedirectFromError(error) ?? '/'
    handlers.onAlreadyAuthenticated(redirect)
    return true
  }

  return false
}
