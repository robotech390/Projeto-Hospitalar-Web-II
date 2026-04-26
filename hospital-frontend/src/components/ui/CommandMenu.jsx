import { useState, useEffect, useRef } from 'react'
import { useNavigate } from 'react-router-dom'
import { Search, LayoutDashboard, Users, ShieldPlus, CalendarDays } from 'lucide-react'

const MENUS = [
  { label: 'Dashboard',        to: '/',         icon: LayoutDashboard, keywords: 'dashboard visão geral' },
  { label: 'Acesso & Usuários',to: '/usuarios', icon: Users,           keywords: 'usuários acesso login permissão' },
  { label: 'Médicos',          to: '/medicos',  icon: ShieldPlus,      keywords: 'médico crm cadastro especialidade' },
  { label: 'Recepção & Agenda',to: '/agenda',   icon: CalendarDays,    keywords: 'agenda plantão horário recepção' },
]

export default function CommandMenu() {
  const [open, setOpen]   = useState(false)
  const [query, setQuery] = useState('')
  const navigate          = useNavigate()
  const inputRef          = useRef(null)
  const containerRef      = useRef(null)

  const filtered = query.trim()
    ? MENUS.filter((m) =>
        m.label.toLowerCase().includes(query.toLowerCase()) ||
        m.keywords.includes(query.toLowerCase())
      )
    : MENUS

  // Atalho Ctrl+K
  useEffect(() => {
    const handler = (e) => {
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault()
        setOpen(true)
        setTimeout(() => inputRef.current?.focus(), 50)
      }
      if (e.key === 'Escape') setOpen(false)
    }
    document.addEventListener('keydown', handler)
    return () => document.removeEventListener('keydown', handler)
  }, [])

  // Fecha ao clicar fora
  useEffect(() => {
    const handler = (e) => {
      if (containerRef.current && !containerRef.current.contains(e.target)) setOpen(false)
    }
    if (open) document.addEventListener('mousedown', handler)
    return () => document.removeEventListener('mousedown', handler)
  }, [open])

  const go = (to) => {
    navigate(to)
    setOpen(false)
    setQuery('')
  }

  return (
    <div className="relative flex-1 max-w-sm" ref={containerRef}>
      {/* Input */}
      <div className="relative">
        <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
        <input
          ref={inputRef}
          type="text"
          placeholder="Buscar menu... (Ctrl+K)"
          value={query}
          onChange={(e) => { setQuery(e.target.value); setOpen(true) }}
          onFocus={() => setOpen(true)}
          className="w-full pl-9 pr-4 py-1.5 text-sm bg-slate-50 border border-slate-200
                     rounded-lg focus:outline-none focus:ring-2 focus:ring-brand/30
                     focus:border-brand placeholder-slate-400"
        />
      </div>

      {/* Dropdown */}
      {open && (
        <div className="absolute top-full mt-1.5 left-0 right-0 bg-white border border-slate-200
                        rounded-xl shadow-lg z-50 overflow-hidden">
          {filtered.length === 0 ? (
            <p className="text-sm text-slate-400 text-center py-4">Nenhum menu encontrado.</p>
          ) : (
            <ul className="py-1">
              {filtered.map(({ label, to, icon: Icon }) => (
                <li key={to}>
                  <button
                    onClick={() => go(to)}
                    className="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-slate-700
                               hover:bg-slate-50 transition-colors text-left"
                  >
                    <div className="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                      <Icon size={14} className="text-slate-500" />
                    </div>
                    {label}
                  </button>
                </li>
              ))}
            </ul>
          )}
          <p className="text-[10px] text-slate-300 text-center pb-2">ESC para fechar</p>
        </div>
      )}
    </div>
  )
}
