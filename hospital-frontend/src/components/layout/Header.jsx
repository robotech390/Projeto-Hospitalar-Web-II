import { Bell, LogOut } from 'lucide-react'
import { useAuth } from '../../contexts/AuthContext'
import { useNavigate } from 'react-router-dom'
import CommandMenu from '../ui/CommandMenu'
import Tooltip from '../ui/Tooltip'

const ROLE_LABELS = {
  administrador: 'Administrador', medico: 'Médico',
  recepcionista: 'Recepcionista', farmaceutico: 'Farmacêutico', paciente: 'Paciente',
}

export default function Header() {
  const { user, logout } = useAuth()
  const navigate = useNavigate()

  const initials = user?.nome?.split(' ').slice(0, 2).map((n) => n[0]).join('').toUpperCase() || 'AD'

  const handleLogout = async () => {
    await logout()
    navigate('/login')
  }

  return (
    <header className="h-14 bg-white border-b border-slate-100 flex items-center px-6 gap-4 shrink-0">
      {/* Command menu — busca de menus */}
      <CommandMenu />

      <div className="flex items-center gap-3 ml-auto">
        <Tooltip text="Notificações">
          <button className="relative p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
            <Bell size={18} className="text-slate-500" />
            <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full" />
          </button>
        </Tooltip>

        <div className="flex items-center gap-2.5">
          <div className="w-8 h-8 rounded-full bg-brand flex items-center justify-center">
            <span className="text-white text-xs font-semibold">{initials}</span>
          </div>
          <div className="hidden sm:block">
            <p className="text-sm font-medium text-slate-800 leading-none">
              {user?.nome?.split(' ').slice(0, 2).join(' ') || 'Usuário'}
            </p>
            <p className="text-xs text-slate-400 mt-0.5">{ROLE_LABELS[user?.funcao] || 'Usuário'}</p>
          </div>
        </div>

        <Tooltip text="Sair do sistema">
          <button
            onClick={handleLogout}
            className="p-1.5 rounded-lg hover:bg-red-50 hover:text-red-500 text-slate-400 transition-colors"
          >
            <LogOut size={17} />
          </button>
        </Tooltip>
      </div>
    </header>
  )
}
