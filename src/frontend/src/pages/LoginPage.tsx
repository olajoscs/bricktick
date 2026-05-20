import { type FormEvent, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { login, LoginError } from '../api/auth'
import { getRedirectFromError, isApiError } from '../api/client'
import { useAuth } from '../auth/AuthProvider'
import { handleAuthError } from '../auth/handleAuthError'
import './LoginPage.css'

function fieldErrors(
  errors: Record<string, string[]> | undefined,
  field: string,
): string | undefined {
  return errors?.[field]?.[0]
}

export default function LoginPage() {
  const navigate = useNavigate()
  const { signIn } = useAuth()
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [emailError, setEmailError] = useState<string | null>(null)
  const [passwordError, setPasswordError] = useState<string | null>(null)

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    setSubmitting(true)
    setError(null)
    setEmailError(null)
    setPasswordError(null)

    try {
      const result = await login(email, password)
      signIn(result.access_token)
      navigate('/', { replace: true })
    } catch (err) {
      if (
        handleAuthError(err, {
          onUnauthenticated: () => setError('Please sign in to continue.'),
          onAlreadyAuthenticated: (redirect) =>
            navigate(redirect, { replace: true }),
        })
      ) {
        return
      }

      if (err instanceof LoginError) {
        setError(err.message)
        setEmailError(fieldErrors(err.body.errors, 'email') ?? null)
        setPasswordError(fieldErrors(err.body.errors, 'password') ?? null)
      } else if (isApiError(err)) {
        const redirect = getRedirectFromError(err)
        if (redirect) {
          navigate(redirect, { replace: true })
          return
        }
        setError(err.message)
      } else {
        setError('Unable to reach the server. Please try again.')
      }
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <section className="login-page">
      <h1>Sign in</h1>
      <p className="login-page__lead">Use your email and password to continue.</p>

      <form className="login-form" onSubmit={handleSubmit} noValidate>
        <div className="login-form__field">
          <label htmlFor="email">Email</label>
          <input
            id="email"
            name="email"
            type="email"
            autoComplete="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            aria-invalid={emailError ? true : undefined}
            aria-describedby={emailError ? 'email-error' : undefined}
          />
          {emailError ? (
            <p id="email-error" className="login-form__error" role="alert">
              {emailError}
            </p>
          ) : null}
        </div>

        <div className="login-form__field">
          <label htmlFor="password">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            autoComplete="current-password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            aria-invalid={passwordError ? true : undefined}
            aria-describedby={passwordError ? 'password-error' : undefined}
          />
          {passwordError ? (
            <p id="password-error" className="login-form__error" role="alert">
              {passwordError}
            </p>
          ) : null}
        </div>

        {error && !emailError && !passwordError ? (
          <p className="login-form__error login-form__error--general" role="alert">
            {error}
          </p>
        ) : null}

        <button type="submit" className="login-form__submit" disabled={submitting}>
          {submitting ? 'Signing in…' : 'Sign in'}
        </button>
      </form>
    </section>
  )
}
