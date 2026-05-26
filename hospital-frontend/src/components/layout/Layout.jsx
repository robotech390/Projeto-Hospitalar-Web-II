import { useEffect } from 'react'
import { Outlet, Navigate, useLocation, useNavigate } from 'react-router-dom'
import Sidebar from './Sidebar'
import Header from './Header'
import { useAuth } from '../../contexts/AuthContext'
import { podeAcessarRota } from '../../utils/permissoes'
import { useToast } from '../../contexts/ToastContext'

export default function Layout() {
  const { user, loading } = useAuth()
  const location = useLocation()
  const navigate = useNavigate()
  const { mostrar } = useToast()

  // Verifica se o usuário pode ver a rota atual; se não puder, redireciona ao dashboard
  useEffect(() => {
    if (!user) return
    if (!podeAcessarRota(user, location.pathname)) {
      mostrar('Você não tem permissão para acessar essa página.', 'aviso')
      navigate('/', { replace: true })
    }
  }, [location.pathname, user, navigate, mostrar])

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-slate-50">
        <div className="w-6 h-6 border-2 border-brand border-t-transparent rounded-full animate-spin" />
      </div>
    )
  }

  if (!user) return <Navigate to="/login" replace />

  return (
    <div className="flex min-h-screen">
      <Sidebar />
      <div className="flex-1 flex flex-col min-w-0">
        <Header />
        <main className="flex-1 p-6 overflow-auto">
          <Outlet />
        </main>
      </div>
    </div>
  )
}
