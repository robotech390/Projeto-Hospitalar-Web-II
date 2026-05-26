import { useQuery } from '@tanstack/react-query'
import { useNavigate } from 'react-router-dom'
import {
  CalendarClock, History, UserCircle, Clock, ArrowRight,
  CalendarDays, Stethoscope,
} from 'lucide-react'
import { meuPerfilApi } from '../api/meuPerfil'
import { useAuth } from '../contexts/AuthContext'
import { formatarData } from '../utils/datas'

/**
 * Dashboard customizado para médicos:
 *  - Saudação personalizada
 *  - Cards de resumo (próximo plantão, plantões do mês, especialidade)
 *  - Próximos 5 plantões
 *  - Atalhos rápidos
 */
export default function DashboardMedico() {
  const { user }   = useAuth()
  const navigate   = useNavigate()
  const primeiroNome = user?.nome?.split(' ')[0] || 'Doutor(a)'

  const { data: perfil } = useQuery({
    queryKey: ['meu-perfil'],
    queryFn:  () => meuPerfilApi.buscar().then((r) => r.data),
  })

  const hojeIso = new Date().toISOString().slice(0, 10)
  const { data: plantoes = [], isLoading } = useQuery({
    queryKey: ['minha-agenda', hojeIso],
    queryFn:  () => meuPerfilApi.minhaAgenda({ data_inicio: hojeIso }).then((r) => r.data),
  })

  // Resumo do mês atual
  const inicioMes  = new Date()
  inicioMes.setDate(1)
  const fimMes = new Date(inicioMes.getFullYear(), inicioMes.getMonth() + 1, 0)

  const plantoesDoMes = plantoes.filter((p) => {
    const d = new Date(p.data_disponibilidade + 'T00:00:00')
    return d >= inicioMes && d <= fimMes
  })

  const proximoPlantao = plantoes[0]
  const proximos5      = plantoes.slice(0, 5)

  const saudacao = (() => {
    const h = new Date().getHours()
    if (h < 12) return 'Bom dia'
    if (h < 18) return 'Boa tarde'
    return 'Boa noite'
  })()

  return (
    <div className="space-y-6">
      {/* Saudação */}
      <div>
        <h1 className="text-xl font-semibold text-slate-800">
          {saudacao}, Dr(a). {primeiroNome} 👋
        </h1>
        <p className="text-sm text-slate-400 mt-0.5">
          Esta é uma visão geral do seu dia. Use o menu lateral para acessar sua agenda e histórico.
        </p>
      </div>

      {/* Cards resumo */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* Próximo plantão */}
        <div className="card">
          <div className="flex items-center justify-between mb-3">
            <div className="p-2 rounded-lg bg-teal-50">
              <CalendarClock size={18} className="text-teal-600" />
            </div>
          </div>
          <p className="text-xs text-slate-400 mb-1">Próximo plantão</p>
          {isLoading ? (
            <p className="text-sm text-slate-400">Carregando...</p>
          ) : proximoPlantao ? (
            <>
              <p className="text-lg font-semibold text-slate-800">
                {formatarData(proximoPlantao.data_disponibilidade)}
              </p>
              <p className="text-sm text-slate-500 mt-0.5">
                {proximoPlantao.hora_inicio?.slice(0, 5)} — {proximoPlantao.hora_fim?.slice(0, 5)}
                {proximoPlantao.plantao && (
                  <span className="ml-2 text-xs px-1.5 py-0.5 rounded bg-purple-100 text-purple-700">Plantão</span>
                )}
              </p>
            </>
          ) : (
            <p className="text-sm text-slate-500">Nenhum plantão futuro</p>
          )}
        </div>

        {/* Plantões no mês */}
        <div className="card">
          <div className="flex items-center justify-between mb-3">
            <div className="p-2 rounded-lg bg-blue-50">
              <CalendarDays size={18} className="text-blue-600" />
            </div>
          </div>
          <p className="text-xs text-slate-400 mb-1">Plantões neste mês</p>
          <p className="text-2xl font-semibold text-slate-800">{plantoesDoMes.length}</p>
          <p className="text-sm text-slate-500 mt-0.5">
            {plantoesDoMes.filter((p) => p.plantao).length} de plantão
          </p>
        </div>

        {/* Especialidade */}
        <div className="card">
          <div className="flex items-center justify-between mb-3">
            <div className="p-2 rounded-lg bg-purple-50">
              <Stethoscope size={18} className="text-purple-600" />
            </div>
          </div>
          <p className="text-xs text-slate-400 mb-1">Especialidade</p>
          <p className="text-lg font-semibold text-slate-800">
            {perfil?.medico?.especialidade || '—'}
          </p>
          <p className="text-sm text-slate-500 mt-0.5">
            CRM {perfil?.medico?.crm}-{perfil?.medico?.uf_crm}
          </p>
        </div>
      </div>

      {/* Próximos plantões */}
      <div className="card">
        <div className="flex items-center justify-between mb-4">
          <div>
            <p className="text-sm font-medium text-slate-700">Seus próximos plantões</p>
            <p className="text-xs text-slate-400">As 5 próximas datas agendadas</p>
          </div>
          <button onClick={() => navigate('/minha-agenda')}
            className="text-xs text-brand hover:text-brand-dark flex items-center gap-1 font-medium">
            Ver agenda completa <ArrowRight size={12} />
          </button>
        </div>

        {isLoading ? (
          <p className="text-center py-8 text-slate-400 text-sm">Carregando...</p>
        ) : proximos5.length === 0 ? (
          <div className="text-center py-8">
            <Clock size={28} className="mx-auto text-slate-200 mb-2" />
            <p className="text-slate-400 text-sm">Sem plantões agendados.</p>
          </div>
        ) : (
          <div className="space-y-1">
            {proximos5.map((p) => (
              <div key={p.id}
                className="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors">
                <div className="w-10 h-10 rounded-lg bg-brand/10 flex items-center justify-center shrink-0">
                  <CalendarClock size={16} className="text-brand" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-slate-800">
                    {formatarData(p.data_disponibilidade)}
                  </p>
                  <p className="text-xs text-slate-400">
                    {p.hora_inicio?.slice(0, 5)} — {p.hora_fim?.slice(0, 5)}
                  </p>
                </div>
                <span className={`text-xs font-medium px-2 py-0.5 rounded-md ${
                  p.plantao ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700'
                }`}>
                  {p.plantao ? 'Plantão' : 'Turno'}
                </span>
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Atalhos */}
      <div className="grid grid-cols-2 gap-4">
        <button onClick={() => navigate('/meu-historico')}
          className="card text-left hover:border-brand/40 hover:shadow-sm transition-all">
          <div className="flex items-center gap-3">
            <div className="p-2 rounded-lg bg-amber-50">
              <History size={18} className="text-amber-600" />
            </div>
            <div className="flex-1">
              <p className="text-sm font-medium text-slate-800">Meu histórico</p>
              <p className="text-xs text-slate-400">Ações registradas no sistema</p>
            </div>
            <ArrowRight size={14} className="text-slate-400" />
          </div>
        </button>

        <button onClick={() => navigate('/meu-perfil')}
          className="card text-left hover:border-brand/40 hover:shadow-sm transition-all">
          <div className="flex items-center gap-3">
            <div className="p-2 rounded-lg bg-slate-100">
              <UserCircle size={18} className="text-slate-600" />
            </div>
            <div className="flex-1">
              <p className="text-sm font-medium text-slate-800">Meu perfil</p>
              <p className="text-xs text-slate-400">Editar dados pessoais e senha</p>
            </div>
            <ArrowRight size={14} className="text-slate-400" />
          </div>
        </button>
      </div>
    </div>
  )
}
