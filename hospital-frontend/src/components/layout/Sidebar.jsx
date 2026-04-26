import { NavLink } from 'react-router-dom'
import { LayoutDashboard, Users, ShieldPlus, CalendarDays } from 'lucide-react'
import Tooltip from '../ui/Tooltip'

// Apenas os módulos gerenciados pela Equipe 1
const NAV = [
  {
    section: 'GERAL',
    items: [
      { to: '/', icon: LayoutDashboard, label: 'Dashboard' },
    ],
  },
  {
    section: 'GESTÃO',
    items: [
      { to: '/usuarios', icon: Users,        label: 'Acesso & Usuários' },
      { to: '/medicos',  icon: ShieldPlus,   label: 'Médicos'           },
      { to: '/agenda',   icon: CalendarDays, label: 'Recepção & Agenda' },
    ],
  },
]

export default function Sidebar() {
  return (
    <aside className="w-56 min-h-screen bg-sidebar flex flex-col shrink-0">
      {/* Logo */}
      <div className="px-5 py-6">
        <div className="flex items-center gap-2">
          <div className="w-7 h-7 rounded-md bg-brand flex items-center justify-center">
            <ShieldPlus size={15} className="text-white" />
          </div>
          <div>
            <p className="text-white text-sm font-semibold leading-none">Saúde+Vc</p>
            <p className="text-white/40 text-[10px] mt-0.5">Sistema Hospitalar</p>
          </div>
        </div>
      </div>

      {/* Nav */}
      <nav className="flex-1 px-3 pb-4 space-y-5">
        {NAV.map(({ section, items }) => (
          <div key={section}>
            <p className="text-white/30 text-[10px] font-semibold tracking-widest px-2 mb-1">
              {section}
            </p>
            {items.map(({ to, icon: Icon, label }) => (
              <Tooltip key={to} text={label} position="right">
                <NavLink
                  to={to}
                  end={to === '/'}
                  className={({ isActive }) =>
                    `flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition-colors duration-150 w-full ${
                      isActive
                        ? 'bg-white/10 text-white font-medium'
                        : 'text-white/60 hover:text-white hover:bg-white/5'
                    }`
                  }
                >
                  <Icon size={16} />
                  {label}
                </NavLink>
              </Tooltip>
            ))}
          </div>
        ))}
      </nav>

      {/* Footer */}
      <div className="px-5 py-4 border-t border-white/10">
        <p className="text-white/25 text-[10px]">Saúde+Vc v1.0</p>
        <p className="text-white/20 text-[10px]">© 2026 IFSC — Equipe 1</p>
      </div>
    </aside>
  )
}
