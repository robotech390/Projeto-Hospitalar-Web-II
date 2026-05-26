import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import { History, User as UserIcon, ArrowRight } from 'lucide-react'
import { meuPerfilApi } from '../api/meuPerfil'
import { useAuth } from '../contexts/AuthContext'
import { formatarDataHora } from '../utils/datas'

/**
 * Tela "Meu Histórico" — mostra os logs em que o usuário foi autor da ação
 * OU em que o nome dele foi citado (ex: "Admin criou plantão para você").
 */
export default function MeuHistorico() {
  const { user } = useAuth()
  const [filtro, setFiltro] = useState('todos') // 'todos' | 'meus' | 'sobre-mim'

  const { data: logs = [], isLoading } = useQuery({
    queryKey: ['meu-historico'],
    queryFn:  () => meuPerfilApi.meuHistorico({ limite: 100 }).then((r) => r.data),
  })

  const meuId = user?.id

  const logsFiltrados = logs.filter((log) => {
    if (filtro === 'meus')      return log.id_usuario === meuId
    if (filtro === 'sobre-mim') return log.id_usuario !== meuId
    return true
  })

  return (
    <div className="space-y-5">
      <div>
        <h1 className="text-xl font-semibold text-slate-800">Meu histórico</h1>
        <p className="text-sm text-slate-400 mt-0.5">
          Ações realizadas por você e ações que envolvem o seu cadastro
        </p>
      </div>

      {/* Tabs de filtro */}
      <div className="flex rounded-lg border border-slate-200 bg-white overflow-hidden w-fit">
        {[
          { valor: 'todos',     label: 'Todos'       },
          { valor: 'meus',      label: 'Feitas por mim' },
          { valor: 'sobre-mim', label: 'Sobre mim'   },
        ].map((opt) => (
          <button key={opt.valor}
            onClick={() => setFiltro(opt.valor)}
            className={`px-4 py-1.5 text-sm transition-colors border-r last:border-r-0 border-slate-200 ${
              filtro === opt.valor
                ? 'bg-brand text-white font-medium'
                : 'text-slate-600 hover:bg-slate-50'
            }`}
          >
            {opt.label}
          </button>
        ))}
      </div>

      {/* Lista */}
      <div className="card p-0 overflow-hidden">
        {isLoading ? (
          <p className="text-center py-10 text-slate-400 text-sm">Carregando...</p>
        ) : logsFiltrados.length === 0 ? (
          <div className="text-center py-12">
            <History size={32} className="mx-auto text-slate-200 mb-2" />
            <p className="text-slate-400 text-sm">Nenhum registro encontrado.</p>
          </div>
        ) : (
          <ul className="divide-y divide-slate-50">
            {logsFiltrados.map((log) => {
              const ehMeu = log.id_usuario === meuId
              return (
                <li key={log.id} className="px-5 py-3 hover:bg-slate-50/50 transition-colors">
                  <div className="flex items-start gap-3">
                    {/* Avatar do autor */}
                    <div className={`w-8 h-8 rounded-full flex items-center justify-center shrink-0 ${
                      ehMeu ? 'bg-brand/15 text-brand' : 'bg-slate-100 text-slate-500'
                    }`}>
                      <UserIcon size={14} />
                    </div>

                    <div className="flex-1 min-w-0">
                      <div className="flex items-start justify-between gap-2 mb-0.5">
                        <p className="text-xs text-slate-500">
                          <span className="font-medium text-slate-700">
                            {ehMeu ? 'Você' : (log.usuario?.usuario || 'Sistema')}
                          </span>
                          {!ehMeu && log.usuario?.funcao && (
                            <span className="text-slate-400"> · {log.usuario.funcao}</span>
                          )}
                        </p>
                        <p className="text-xs text-slate-400 whitespace-nowrap">
                          {formatarDataHora(log.data)}
                        </p>
                      </div>
                      <p className="text-sm text-slate-700">{log.descricao}</p>
                    </div>
                  </div>
                </li>
              )
            })}
          </ul>
        )}
      </div>
    </div>
  )
}
