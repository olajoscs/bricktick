import { useEffect, useId, useRef, useState } from 'react'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import { useAuth } from '../auth/AuthProvider'
import './AppHeader.css'

export default function AppHeader() {
  const navigate = useNavigate()
  const location = useLocation()
  const { isAuthenticated, signOut } = useAuth()
  const menuId = useId()
  const menuRef = useRef<HTMLDivElement>(null)
  const [menuOpen, setMenuOpen] = useState(false)

  useEffect(() => {
    setMenuOpen(false)
  }, [location.pathname, isAuthenticated])

  useEffect(() => {
    if (!menuOpen) {
      return
    }

    function handlePointerDown(event: MouseEvent) {
      if (menuRef.current && !menuRef.current.contains(event.target as Node)) {
        setMenuOpen(false)
      }
    }

    function handleKeyDown(event: KeyboardEvent) {
      if (event.key === 'Escape') {
        setMenuOpen(false)
      }
    }

    document.addEventListener('mousedown', handlePointerDown)
    document.addEventListener('keydown', handleKeyDown)

    return () => {
      document.removeEventListener('mousedown', handlePointerDown)
      document.removeEventListener('keydown', handleKeyDown)
    }
  }, [menuOpen])

  function handleLogout() {
    setMenuOpen(false)
    signOut()
    navigate('/login', { replace: true })
  }

  return (
    <header className="app-header">
      <div className="app-header__inner">
        {isAuthenticated ? (
          <Link className="app-header__brand" to="/">
            Bricktick
          </Link>
        ) : (
          <span className="app-header__brand">Bricktick</span>
        )}

        <div className="app-header__menu" ref={menuRef}>
          <button
            type="button"
            className="app-header__menu-trigger"
            aria-expanded={menuOpen}
            aria-haspopup="menu"
            aria-controls={menuId}
            onClick={() => setMenuOpen((open) => !open)}
          >
            Account
            <span className="app-header__menu-chevron" aria-hidden="true">
              ▾
            </span>
          </button>
          {menuOpen ? (
            <ul id={menuId} className="app-header__dropdown" role="menu">
              {isAuthenticated ? (
                <li role="none">
                  <button
                    type="button"
                    className="app-header__dropdown-item"
                    role="menuitem"
                    onClick={handleLogout}
                  >
                    Log out
                  </button>
                </li>
              ) : (
                <li role="none">
                  <Link
                    className="app-header__dropdown-item app-header__dropdown-link"
                    role="menuitem"
                    to="/login"
                    onClick={() => setMenuOpen(false)}
                  >
                    Sign in
                  </Link>
                </li>
              )}
            </ul>
          ) : null}
        </div>
      </div>
    </header>
  )
}
