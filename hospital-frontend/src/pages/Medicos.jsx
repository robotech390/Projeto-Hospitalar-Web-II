import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Plus, Search, UserX, UserCheck, Pencil } from 'lucide-react'
import { useNavigate } from 'react-router-dom'
import { medicosApi } from '../api/medicos'
import Badge from '../components/ui/Badge'
import Tooltip from '../components/ui/Tooltip'

const STATUS_OPTIONS = [
  { value: 'A', label: 'Ativos'   },
  { value: 'I', label: 'Inativos' },
  { value: '',  label: 'Todos'    },
]

export default function Medicos() {
  const qc       = useQueryClient()
  const navigate = useNavigate()
  const [search, setSearch] = useState('')
  const [status, setStatus] = useState('A')

  const { data: medicos = [], isLoading } = useQuery({
    queryKey: ['medicos', status],
    queryFn:  () => medicosApi.listar(status ? { status } : {}).then((r) => r.data),
  })

  const inativar = useMutation({
    mutationFn: (id) => medicosApi.inativar(id),
    onSuccess: () => qc.invalidateQueries(['medicos']),
  })

  // Reativar: envia status A via atualizar
  const reativar = useMutation({
    mutationFn: (id) => medicosApi.atualizar(id, { status: 'A' }),
    onSuccess: () => qc.invalidateQueries(['medicos']),
  })

  const filtered = medicos.filter((m) =>
    m.nome?.toLowerCase().includes(search.toLowerCase()) ||
    m.crm?.includes(search) ||
    m.especialidade?.toLowerCase().includes(search.toLowerCase())
  )

  return (
    <div className="space-y-5">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold text-slate-800">Médicos</h1>
          <p className="text-sm text-slate-400 mt-0.5">Cadastro e gerenciamento de médicos</p>
        </div>
        <Tooltip text="Cadastrar novo médico">
          <button onClick={() => navigate('/medicos/novo')} className="btn-primary flex items-center gap-1.5">
            <Plus size={15} /> Novo Médico
          </button>
        </Tooltip>
      </div>

      {/* Filters */}
      <div className="flex gap-3 flex-wrap">
        <div className="relative flex-1 max-w-xs">
          <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
          <input value={search} onChange={(e) => setSearch(e.target.value)}
            placeholder="Buscar por nome, CRM..." className="input pl-8" />
        </div>

        {/* Filtro de status com tabs */}
        <div className="flex rounded-lg border border-slate-200 bg-white overflow-hidden">
          {STATUS_OPTIONS.map((opt) => (
            <button key={opt.value}
              onClick={() => setStatus(opt.value)}
              className={`px-4 py-1.5 text-sm transition-colors border-r last:border-r-0 border-slate-200 ${
                status === opt.value
                  ? 'bg-brand text-white font-medium'
                  : 'text-slate-600 hover:bg-slate-50'
              }`}
            >
              {opt.label}
            </button>
          ))}
        </div>
      </div>

      {/* Table */}
      <div className="card p-0 overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-slate-100">
              {['Nome', 'CRM', 'Especialidade', 'Tipo', 'Status', 'Ações'].map((h) => (
                <th key={h} className={`px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider ${
                  h === 'Ações' ? 'text-right' : 'text-left'
                }`}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-50">
            {isLoading ? (
              <tr><td colSpan={6} className="text-center py-10 text-slate-400 text-sm">Carregando...</td></tr>
            ) : filtered.length === 0 ? (
              <tr><td colSpan={6} className="text-center py-10 text-slate-400 text-sm">Nenhum médico encontrado.</td></tr>
            ) : filtered.map((m) => (
              <tr key={m.id} className="hover:bg-slate-50/50 transition-colors">
                <td className="px-5 py-3">
                  <p className="font-medium text-slate-700">{m.nome}</p>
                  <p className="text-xs text-slate-400">{m.email}</p>
                </td>
                <td className="px-5 py-3 text-slate-500">{m.crm}-{m.uf_crm}</td>
                <td className="px-5 py-3 text-slate-500">{m.especialidade || '—'}</td>
                <td className="px-5 py-3 text-slate-500">{m.tipo || '—'}</td>
                <td className="px-5 py-3">
                  <Badge variant={m.status === 'A' ? 'active' : 'inactive'}>
                    {m.status === 'A' ? 'Ativo' : 'Inativo'}
                  </Badge>
                </td>
                <td className="px-5 py-3">
                  <div className="flex items-center justify-end gap-1">
                    <Tooltip text="Editar médico">
                      <button onClick={() => navigate(`/medicos/${m.id}/editar`)}
                        className="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                        <Pencil size={15} />
                      </button>
                    </Tooltip>

                    {m.status === 'A' ? (
                      <Tooltip text="Inativar médico">
                        <button
                          onClick={() => { if (confirm(`Inativar Dr(a). ${m.nome}?`)) inativar.mutate(m.id) }}
                          className="p-1.5 rounded-lg hover:bg-red-50 hover:text-red-500 text-slate-400 transition-colors"
                        >
                          <UserX size={15} />
                        </button>
                      </Tooltip>
                    ) : (
                      <Tooltip text="Reativar médico">
                        <button
                          onClick={() => { if (confirm(`Reativar Dr(a). ${m.nome}?`)) reativar.mutate(m.id) }}
                          className="p-1.5 rounded-lg hover:bg-green-50 hover:text-green-600 text-slate-400 transition-colors"
                        >
                          <UserCheck size={15} />
                        </button>
                      </Tooltip>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  )
}
