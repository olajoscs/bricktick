/**
 * Base URL for API requests as seen from the browser (e.g. http://localhost:8080).
 * Set via VITE_API_BASE_URL. Empty means same origin (e.g. reverse proxy in production).
 */
export function getApiBaseUrl(): string {
  const raw = import.meta.env.VITE_API_BASE_URL
  if (typeof raw !== 'string' || raw.trim() === '') {
    return ''
  }
  return raw.trim().replace(/\/$/, '')
}

export function apiUrl(path: string): string {
  const base = getApiBaseUrl()
  const normalized = path.startsWith('/') ? path : `/${path}`
  if (!base) {
    return normalized
  }
  return `${base}${normalized}`
}
