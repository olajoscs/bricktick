export type ApiErrorBody = {
  message?: string
  redirect?: string
  errors?: Record<string, string[]>
}

export function asApiErrorBody(body: unknown): ApiErrorBody {
  if (typeof body === 'object' && body !== null) {
    return body as ApiErrorBody
  }

  return {}
}

export function getApiErrorMessage(body: unknown, fallback: string): string {
  return asApiErrorBody(body).message ?? fallback
}

export function getApiErrorRedirect(body: unknown): string | undefined {
  return asApiErrorBody(body).redirect
}
