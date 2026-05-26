import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Plus, Trash2, Pencil, CalendarDays } from 'lucide-react'
import { agendaApi } from '../api/agenda'
import { medicosApi } from '../api/medicos'
import { formatarData } from '../utils/datas'
import Tooltip from '../components/ui/Tooltip'
import DialogoConfirmacao from '../components/ui/DialogoConfirmacao'
import { useToast } from '../contexts/ToastContext'

export default function Agenda() {
  const qc       = useQueryClient()
  const navigate = useNavigate()
  const { mostrar } = useToast()

  const [filtroData, setFiltroData]     = useState('')
  const [filtroMedico, setFiltroMedico] = useState('')
  const [confirmar, setConfirmar]       = useState(null)

  const { data: agenda = [], isLoading } = useQuery({
    queryKey: ['agenda', filtroData, filtroMedico],
    queryFn: () => agendaApi.listar({
      ...(filtroData   ? { data: filtroData }       : {}),
      ...(filtroMedico ? { id_medico: filtroMedico} : {}),
    }).then((r) => r.data),
  })

  const { data: medicos = [] } = useQuery({
    queryKey: ['medicos-select'],
    queryFn:  () => medicosApi.listar({ status: 'A' }).then((r) => r.data),
  })

  const remover = useMutation({
    mutationFn: (id) => agendaApi.remover(id),
    onSuccess: () => {
      qc.invalidateQueries(['agenda'])
      mostrar('Plantão removido.', 'sucesso')
    },
    onError: (err) => mostrar(err.mensagemAmigavel || 'Não foi possível remover o plantão.', 'erro'),
  })

  return (
    <div className="space-y-5">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold text-slate-800">Recepção & Agenda</h1>
          <p className="text-sm text-slate-400 mt-0.5">Plantões e disponibilidade dos médicos</p>
        </div>
        <Tooltip text="Cadastrar novo plantão">
          <button onClick={() => navigate('/agenda/novo')} className="btn-primary flex items-center gap-1.5">
            <Plus size={15} /> Novo Plantão
          </button>
        </Tooltip>
      </div>

      <div className="flex gap-3 flex-wrap">
        <input type="date" value={filtroData}
          onChange={(e) => setFiltroData(e.target.value)} className="input w-44" />
        <select value={filtroMedico} onChange={(e) => setFiltroMedico(e.target.value)} className="input w-56">
          <option value="">Todos os médicos</option>
          {medicos.map((m) => <option key={m.id} value={m.id}>{m.nome}</option>)}
        </select>
        {(filtroData || filtroMedico) && (
          <button onClick={() => { setFiltroData(''); setFiltroMedico('') }}
            className="text-sm text-slate-500 hover:text-slate-700">Limpar filtros</button>
        )}
      </div>

      <div className="card p-0 overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-slate-100">
              {['Médico', 'Data', 'Início', 'Término', 'Tipo', 'Ações'].map((h) => (
                <th key={h} className={`px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider ${
                  h === 'Ações' ? 'text-right' : 'text-left'
                }`}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-50">
            {isLoading ? (
              <tr><td colSpan={6} className="text-center py-10 text-slate-400 text-sm">Carregando...</td></tr>
            ) : agenda.length === 0 ? (
              <tr>
                <td colSpan={6} className="text-center py-12">
                  <CalendarDays size={32} className="mx-auto text-slate-200 mb-2" />
                  <p className="text-slate-400 text-sm">Nenhum plantão encontrado.</p>
                </td>
              </tr>
            ) : agenda.map((a) => (
              <tr key={a.id} className="hover:bg-slate-50/50 transition-colors">
                <td className="px-5 py-3 font-medium text-slate-700">{a.medico?.nome || '—'}</td>
                <td className="px-5 py-3 text-slate-500">{formatarData(a.data_disponibilidade)}</td>
                <td className="px-5 py-3 text-slate-500">{a.hora_inicio?.slice(0, 5) || '—'}</td>
                <td className="px-5 py-3 text-slate-500">{a.hora_fim?.slice(0, 5) || '—'}</td>
                <td className="px-5 py-3">
                  <span className={`text-xs font-medium px-2 py-0.5 rounded-md ${
                    a.plantao ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700'
                  }`}>
                    {a.plantao ? 'Plantão' : 'Turno'}
                  </span>
                </td>
                <td className="px-5 py-3">
                  <div className="flex items-center justify-end gap-1">
                    <Tooltip text="Editar plantão">
                      <button onClick={() => navigate(`/agenda/${a.id}/editar`)}
                        className="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                        <Pencil size={15} />
                      </button>
                    </Tooltip>
                    <Tooltip text="Remover plantão">
                      <button onClick={() => setConfirmar(a)}
                        className="p-1.5 rounded-lg hover:bg-red-50 hover:text-red-500 text-slate-400 transition-colors">
                        <Trash2 size={15} />
                      </button>
                    </Tooltip>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <DialogoConfirmacao
        aberto={Boolean(confirmar)}
        titulo="Remover plantão?"
        mensagem={`Tem certeza que deseja remover o plantão de ${confirmar?.medico?.nome || 'este médico'} em ${formatarData(confirmar?.data_disponibilidade)}?`}
        textoConfirmar="Remover"
        variante="perigo"
        onCancelar={() => setConfirmar(null)}
        onConfirmar={() => {
          remover.mutate(confirmar.id)
          setConfirmar(null)
        }}
      />
    </div>
  )
}
