import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom'
import { AuthProvider } from './auth/AuthProvider'
import AppLayout from './components/AppLayout'
import GuestRoute from './components/GuestRoute'
import ProtectedRoute from './components/ProtectedRoute'
import DashboardPage from './pages/DashboardPage'
import LoginPage from './pages/LoginPage'

function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <Routes>
          <Route element={<AppLayout />}>
            <Route element={<ProtectedRoute />}>
              <Route index element={<DashboardPage />} />
            </Route>
            <Route element={<GuestRoute />}>
              <Route path="login" element={<LoginPage />} />
            </Route>
            <Route path="*" element={<Navigate to="/" replace />} />
          </Route>
        </Routes>
      </AuthProvider>
    </BrowserRouter>
  )
}

export default App
