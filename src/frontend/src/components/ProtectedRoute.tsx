import { useEffect, useState } from 'react'
import { Navigate, Outlet, useNavigate } from 'react-router-dom'
import { fetchAuthMe } from '../api/auth'
import { handleAuthError } from '../auth/handleAuthError'
import { isLoggedIn } from '../auth/session'
import RouteGuardStatus from './RouteGuardStatus'

export default function ProtectedRoute() {
  const navigate = useNavigate()
  const [allowed, setAllowed] = useState<boolean | null>(null)

  useEffect(() => {
    if (!isLoggedIn()) {
      return
    }

    let cancelled = false

    async function verify() {
      try {
        await fetchAuthMe()
        if (!cancelled) {
          setAllowed(true)
        }
      } catch (error) {
        if (cancelled) {
          return
        }

        if (
          handleAuthError(error, {
            onUnauthenticated: () => navigate('/login', { replace: true }),
            onAlreadyAuthenticated: (redirect) =>
              navigate(redirect, { replace: true }),
          })
        ) {
          return
        }

        navigate('/login', { replace: true })
      }
    }

    verify()

    return () => {
      cancelled = true
    }
  }, [navigate])

  if (!isLoggedIn()) {
    return <Navigate to="/login" replace />
  }

  if (allowed !== true) {
    return <RouteGuardStatus message="Checking authentication…" />
  }

  return <Outlet />
}
