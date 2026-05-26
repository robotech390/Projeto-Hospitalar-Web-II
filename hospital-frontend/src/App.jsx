import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { AuthProvider } from './contexts/AuthContext'
import { ToastProvider } from './contexts/ToastContext'
import Layout from './components/layout/Layout'

// Auth
import Login from './pages/Login'
import EsqueciSenha from './pages/EsqueciSenha'
import RedefinirSenha from './pages/RedefinirSenha'
import AlterarSenha from './pages/AlterarSenha'

// Dashboard (decide qual exibir conforme função)
import Dashboard from './pages/Dashboard'

// Admin
import Usuarios from './pages/Usuarios'
import UsuarioForm from './pages/UsuarioForm'
import Medicos from './pages/Medicos'
import MedicoForm from './pages/MedicoForm'
import Agenda from './pages/Agenda'
import AgendaForm from './pages/AgendaForm'

// Meu espaço (médico e outros usuários)
import MinhaAgenda from './pages/MinhaAgenda'
import MeuHistorico from './pages/MeuHistorico'
import MeuPerfil from './pages/MeuPerfil'

const qc = new QueryClient({
  defaultOptions: { queries: { retry: 1, staleTime: 30_000 } },
})

export default function App() {
  return (
    <QueryClientProvider client={qc}>
      <ToastProvider>
        <AuthProvider>
          <BrowserRouter>
            <Routes>
              {/* Públicas */}
              <Route path="/login"           element={<Login />} />
              <Route path="/esqueci-senha"   element={<EsqueciSenha />} />
              <Route path="/redefinir-senha" element={<RedefinirSenha />} />
              <Route path="/alterar-senha"   element={<AlterarSenha />} />

              {/* Privadas com layout */}
              <Route element={<Layout />}>
                <Route path="/" element={<Dashboard />} />

                {/* Admin */}
                <Route path="/usuarios"            element={<Usuarios />} />
                <Route path="/usuarios/novo"       element={<UsuarioForm />} />
                <Route path="/usuarios/:id/editar" element={<UsuarioForm />} />
                <Route path="/medicos"             element={<Medicos />} />
                <Route path="/medicos/novo"        element={<MedicoForm />} />
                <Route path="/medicos/:id/editar"  element={<MedicoForm />} />
                <Route path="/agenda"              element={<Agenda />} />
                <Route path="/agenda/novo"         element={<AgendaForm />} />
                <Route path="/agenda/:id/editar"   element={<AgendaForm />} />

                {/* Meu espaço */}
                <Route path="/minha-agenda"   element={<MinhaAgenda />} />
                <Route path="/meu-historico"  element={<MeuHistorico />} />
                <Route path="/meu-perfil"     element={<MeuPerfil />} />

                <Route path="*" element={<Navigate to="/" replace />} />
              </Route>
            </Routes>
          </BrowserRouter>
        </AuthProvider>
      </ToastProvider>
    </QueryClientProvider>
  )
}
