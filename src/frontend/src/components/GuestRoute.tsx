import { useEffect, useState } from 'react'
import { Outlet, useNavigate } from 'react-router-dom'
import { fetchGuestSession } from '../api/auth'
import { handleAuthError } from '../auth/handleAuthError'
import { isLoggedIn } from '../auth/session'
import RouteGuardStatus from './RouteGuardStatus'

export default function GuestRoute() {
  const navigate = useNavigate()
  const [allowed, setAllowed] = useState<boolean | null>(null)

  useEffect(() => {
    if (!isLoggedIn()) {
      setAllowed(true)
      return
    }

    let cancelled = false

    async function verify() {
      try {
        await fetchGuestSession()
        if (!cancelled) {
          setAllowed(true)
        }
      } catch (error) {
        if (cancelled) {
          return
        }

        if (
          handleAuthError(error, {
            onUnauthenticated: () => setAllowed(true),
            onAlreadyAuthenticated: (redirect) =>
              navigate(redirect, { replace: true }),
          })
        ) {
          return
        }

        setAllowed(true)
      }
    }

    verify()

    return () => {
      cancelled = true
    }
  }, [navigate])

  if (allowed === null) {
    return <RouteGuardStatus message="Checking authentication…" />
  }

  if (!allowed) {
    return null
  }

  return <Outlet />
}
