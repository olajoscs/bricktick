import {
  createContext,
  useCallback,
  useContext,
  useMemo,
  useSyncExternalStore,
  type ReactNode,
} from 'react'
import { clearSession, isLoggedIn, setAccessToken } from './session'

type AuthContextValue = {
  isAuthenticated: boolean
  signIn: (token: string) => void
  signOut: () => void
}

const AuthContext = createContext<AuthContextValue | null>(null)

function subscribe(callback: () => void) {
  window.addEventListener('storage', callback)
  window.addEventListener('bricktick:auth-changed', callback)

  return () => {
    window.removeEventListener('storage', callback)
    window.removeEventListener('bricktick:auth-changed', callback)
  }
}

function getAuthSnapshot() {
  return isLoggedIn()
}

export function AuthProvider({ children }: { children: ReactNode }) {
  const isAuthenticated = useSyncExternalStore(
    subscribe,
    getAuthSnapshot,
    () => false,
  )

  const signIn = useCallback((token: string) => {
    setAccessToken(token)
    window.dispatchEvent(new Event('bricktick:auth-changed'))
  }, [])

  const signOut = useCallback(() => {
    clearSession()
    window.dispatchEvent(new Event('bricktick:auth-changed'))
  }, [])

  const value = useMemo(
    () => ({ isAuthenticated, signIn, signOut }),
    [isAuthenticated, signIn, signOut],
  )

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth(): AuthContextValue {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider')
  }
  return context
}
