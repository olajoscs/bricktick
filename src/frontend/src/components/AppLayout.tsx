import { Outlet, useLocation } from 'react-router-dom'
import AppHeader from './AppHeader'
import './AppLayout.css'

export default function AppLayout() {
  const { pathname } = useLocation()
  const showHeader = pathname !== '/login'

  return (
    <>
      {showHeader ? <AppHeader /> : null}
      <main className="app-main">
        <Outlet />
      </main>
    </>
  )
}
