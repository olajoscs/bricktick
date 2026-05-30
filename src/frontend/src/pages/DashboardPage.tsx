import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { fetchDashboard, type DashboardResponse } from '../api/dashboard'
import { handleAuthError } from '../auth/handleAuthError'
import './DashboardPage.css'

export default function DashboardPage() {
  const navigate = useNavigate()
  const [data, setData] = useState<DashboardResponse | null>(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  useEffect(() => {
    let cancelled = false

    async function load() {
      setLoading(true)
      setError(null)

      try {
        const dashboard = await fetchDashboard()
        if (!cancelled) {
          setData(dashboard)
        }
      } catch (err) {
        if (cancelled) {
          return
        }

        if (
          handleAuthError(err, {
            onUnauthenticated: () => navigate('/login', { replace: true }),
            onAlreadyAuthenticated: (redirect) =>
              navigate(redirect, { replace: true }),
          })
        ) {
          return
        }

        setError('Unable to load dashboard. Please try again.')
      } finally {
        if (!cancelled) {
          setLoading(false)
        }
      }
    }

    load()

    return () => {
      cancelled = true
    }
  }, [navigate])

  if (loading) {
    return (
      <section className="dashboard-page">
        <p className="dashboard-page__status">Loading dashboard…</p>
      </section>
    )
  }

  if (error || !data) {
    return (
      <section className="dashboard-page">
        <p className="dashboard-page__error" role="alert">
          {error ?? 'Dashboard unavailable.'}
        </p>
      </section>
    )
  }

  return (
    <section className="dashboard-page">
      <h1>Dashboard</h1>
      <p className="dashboard-page__message">{data.message}</p>

      <dl className="dashboard-page__details">
        <div>
          <dt>Name</dt>
          <dd>{data.user.name}</dd>
        </div>
        <div>
          <dt>Email</dt>
          <dd>{data.user.email}</dd>
        </div>
      </dl>

      <ul className="dashboard-page__stats">
        <li>
          <span className="dashboard-page__stat-value">
            {data.stats.active_projects}
          </span>
          <span className="dashboard-page__stat-label">Active projects</span>
        </li>
        <li>
          <span className="dashboard-page__stat-value">
            {data.stats.pending_tasks}
          </span>
          <span className="dashboard-page__stat-label">Pending tasks</span>
        </li>
      </ul>
    </section>
  )
}
