import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { AuthProvider } from './contexts/AuthContext'
import Layout from './components/layout/Layout'
import Login from './pages/Login'
import AlterarSenha from './pages/AlterarSenha'
import Dashboard from './pages/Dashboard'
import Usuarios from './pages/Usuarios'
import Medicos from './pages/Medicos'
import MedicoForm from './pages/MedicoForm'
import Agenda from './pages/Agenda'

const qc = new QueryClient({
  defaultOptions: { queries: { retry: 1, staleTime: 30_000 } },
})

export default function App() {
  return (
    <QueryClientProvider client={qc}>
      <AuthProvider>
        <BrowserRouter>
          <Routes>
            <Route path="/login"         element={<Login />} />
            <Route path="/alterar-senha" element={<AlterarSenha />} />

            <Route element={<Layout />}>
              <Route path="/"                      element={<Dashboard />} />
              <Route path="/usuarios"              element={<Usuarios />} />
              <Route path="/medicos"               element={<Medicos />} />
              <Route path="/medicos/novo"          element={<MedicoForm />} />
              <Route path="/medicos/:id/editar"    element={<MedicoForm />} />
              <Route path="/agenda"                element={<Agenda />} />
              <Route path="*"                      element={<Navigate to="/" replace />} />
            </Route>
          </Routes>
        </BrowserRouter>
      </AuthProvider>
    </QueryClientProvider>
  )
}
