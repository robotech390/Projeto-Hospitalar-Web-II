import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Plus, Search, UserX, UserCheck, Pencil } from 'lucide-react'
import { medicosApi } from '../api/medicos'
import { mascararCpf, mascararTelefone } from '../utils/mascaras'
import Badge from '../components/ui/Badge'
import Tooltip from '../components/ui/Tooltip'
import DialogoConfirmacao from '../components/ui/DialogoConfirmacao'
import { useToast } from '../contexts/ToastContext'

const STATUS_OPCOES = [
  { valor: 'A', label: 'Ativos'   },
  { valor: 'I', label: 'Inativos' },
  { valor: '',  label: 'Todos'    },
]

export default function Medicos() {
  const qc       = useQueryClient()
  const navigate = useNavigate()
  const { mostrar } = useToast()

  const [busca, setBusca]   = useState('')
  const [status, setStatus] = useState('A')
  const [confirmar, setConfirmar] = useState(null)

  const { data: medicos = [], isLoading } = useQuery({
    queryKey: ['medicos', status],
    queryFn:  () => medicosApi.listar(status ? { status } : {}).then((r) => r.data),
  })

  const inativar = useMutation({
    mutationFn: (id) => medicosApi.inativar(id),
    onSuccess: () => {
      qc.invalidateQueries(['medicos'])
      mostrar('Médico inativado.', 'sucesso')
    },
    onError: () => mostrar('Não foi possível inativar o médico.', 'erro'),
  })

  const reativar = useMutation({
    mutationFn: (medico) => medicosApi.atualizar(medico.id, {
      nome:   medico.nome,
      cpf:    medico.cpf,
      email:  medico.email,
      crm:    medico.crm,
      uf_crm: medico.uf_crm,
      status: 'A',
    }),
    onSuccess: () => {
      qc.invalidateQueries(['medicos'])
      mostrar('Médico reativado.', 'sucesso')
    },
    onError: () => mostrar('Não foi possível reativar o médico.', 'erro'),
  })

  const medicosFiltrados = medicos.filter((m) =>
    m.nome?.toLowerCase().includes(busca.toLowerCase()) ||
    m.crm?.includes(busca) ||
    m.especialidade?.toLowerCase().includes(busca.toLowerCase())
  )

  return (
    <div className="space-y-5">
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

      <div className="flex gap-3 flex-wrap">
        <div className="relative flex-1 max-w-xs">
          <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
          <input value={busca} onChange={(e) => setBusca(e.target.value)}
            placeholder="Buscar por nome, CRM..." className="input pl-8" />
        </div>

        <div className="flex rounded-lg border border-slate-200 bg-white overflow-hidden">
          {STATUS_OPCOES.map((opt) => (
            <button key={opt.valor}
              onClick={() => setStatus(opt.valor)}
              className={`px-4 py-1.5 text-sm transition-colors border-r last:border-r-0 border-slate-200 ${
                status === opt.valor
                  ? 'bg-brand text-white font-medium'
                  : 'text-slate-600 hover:bg-slate-50'
              }`}
            >
              {opt.label}
            </button>
          ))}
        </div>
      </div>

      <div className="card p-0 overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-slate-100">
              {['Nome', 'CRM', 'CPF', 'Especialidade', 'Status', 'Ações'].map((h) => (
                <th key={h} className={`px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider ${
                  h === 'Ações' ? 'text-right' : 'text-left'
                }`}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-50">
            {isLoading ? (
              <tr><td colSpan={6} className="text-center py-10 text-slate-400 text-sm">Carregando...</td></tr>
            ) : medicosFiltrados.length === 0 ? (
              <tr><td colSpan={6} className="text-center py-10 text-slate-400 text-sm">Nenhum médico encontrado.</td></tr>
            ) : medicosFiltrados.map((m) => (
              <tr key={m.id} className="hover:bg-slate-50/50 transition-colors">
                <td className="px-5 py-3">
                  <p className="font-medium text-slate-700">{m.nome}</p>
                  <p className="text-xs text-slate-400">{m.email}</p>
                </td>
                <td className="px-5 py-3 text-slate-500">{m.crm}-{m.uf_crm}</td>
                <td className="px-5 py-3 text-slate-500">{m.cpf ? mascararCpf(m.cpf) : '—'}</td>
                <td className="px-5 py-3 text-slate-500">{m.especialidade || '—'}</td>
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
                        <button onClick={() => setConfirmar({ ...m, acao: 'inativar' })}
                          className="p-1.5 rounded-lg hover:bg-red-50 hover:text-red-500 text-slate-400 transition-colors">
                          <UserX size={15} />
                        </button>
                      </Tooltip>
                    ) : (
                      <Tooltip text="Reativar médico">
                        <button onClick={() => setConfirmar({ ...m, acao: 'reativar' })}
                          className="p-1.5 rounded-lg hover:bg-green-50 hover:text-green-600 text-slate-400 transition-colors">
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

      <DialogoConfirmacao
        aberto={Boolean(confirmar)}
        titulo={confirmar?.acao === 'inativar' ? 'Inativar médico?' : 'Reativar médico?'}
        mensagem={
          confirmar?.acao === 'inativar'
            ? `Tem certeza que deseja inativar Dr(a). ${confirmar?.nome}? Você poderá reativar depois.`
            : `Reativar Dr(a). ${confirmar?.nome}? O médico voltará a aparecer nas listagens ativas.`
        }
        textoConfirmar={confirmar?.acao === 'inativar' ? 'Inativar' : 'Reativar'}
        variante={confirmar?.acao === 'inativar' ? 'perigo' : 'aviso'}
        onCancelar={() => setConfirmar(null)}
        onConfirmar={() => {
          if (confirmar.acao === 'inativar') inativar.mutate(confirmar.id)
          else reativar.mutate(confirmar)
          setConfirmar(null)
        }}
      />
    </div>
  )
}
