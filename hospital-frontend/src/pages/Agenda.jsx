import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Plus, Trash2, CalendarDays } from 'lucide-react'
import { agendaApi } from '../api/agenda'
import { medicosApi } from '../api/medicos'
import Modal from '../components/ui/Modal'

export default function Agenda() {
  const qc = useQueryClient()
  const [modal, setModal] = useState(false)
  const [filtroData, setFiltroData] = useState('')
  const [filtroMedico, setFiltroMedico] = useState('')
  const [form, setForm] = useState({
    id_medico: '', data_disponibilidade: '', hora_inicio: '', hora_fim: '', plantao: false,
  })
  const [formError, setFormError] = useState('')

  const { data: agenda = [], isLoading } = useQuery({
    queryKey: ['agenda', filtroData, filtroMedico],
    queryFn: () => agendaApi.listar({
      ...(filtroData   ? { data: filtroData }         : {}),
      ...(filtroMedico ? { id_medico: filtroMedico }  : {}),
    }).then((r) => r.data),
  })

  const { data: medicos = [] } = useQuery({
    queryKey: ['medicos-select'],
    queryFn:  () => medicosApi.listar({ status: 'A' }).then((r) => r.data),
  })

  const criar = useMutation({
    mutationFn: (data) => agendaApi.criar(data),
    onSuccess: () => { qc.invalidateQueries(['agenda']); setModal(false); setForm({ id_medico: '', data_disponibilidade: '', hora_inicio: '', hora_fim: '', plantao: false }) },
    onError: (err) => setFormError(err.response?.data?.mensagem || JSON.stringify(err.response?.data?.erros) || 'Erro ao criar plantão.'),
  })

  const remover = useMutation({
    mutationFn: (id) => agendaApi.remover(id),
    onSuccess: () => qc.invalidateQueries(['agenda']),
  })

  const handleSubmit = (e) => {
    e.preventDefault()
    setFormError('')
    criar.mutate({ ...form, id_medico: Number(form.id_medico) })
  }

  return (
    <div className="space-y-5">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold text-slate-800">Recepção & Agenda</h1>
          <p className="text-sm text-slate-400 mt-0.5">Plantões e disponibilidade dos médicos</p>
        </div>
        <button onClick={() => setModal(true)} className="btn-primary flex items-center gap-1.5">
          <Plus size={15} /> Novo Plantão
        </button>
      </div>

      {/* Filters */}
      <div className="flex gap-3">
        <input type="date" value={filtroData} onChange={(e) => setFiltroData(e.target.value)} className="input w-44" />
        <select value={filtroMedico} onChange={(e) => setFiltroMedico(e.target.value)} className="input w-56">
          <option value="">Todos os médicos</option>
          {medicos.map((m) => <option key={m.id} value={m.id}>{m.nome}</option>)}
        </select>
        {(filtroData || filtroMedico) && (
          <button onClick={() => { setFiltroData(''); setFiltroMedico('') }}
            className="text-sm text-slate-500 hover:text-slate-700">Limpar filtros</button>
        )}
      </div>

      {/* Table */}
      <div className="card p-0 overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-slate-100">
              {['Médico', 'Data', 'Início', 'Término', 'Tipo', ''].map((h) => (
                <th key={h} className="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">{h}</th>
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
                <td className="px-5 py-3 text-slate-500">
                  {a.data_disponibilidade
                    ? new Date(a.data_disponibilidade + 'T00:00:00').toLocaleDateString('pt-BR')
                    : '—'}
                </td>
                <td className="px-5 py-3 text-slate-500">{a.hora_inicio?.slice(0, 5) || '—'}</td>
                <td className="px-5 py-3 text-slate-500">{a.hora_fim?.slice(0, 5) || '—'}</td>
                <td className="px-5 py-3">
                  <span className={`text-xs font-medium px-2 py-0.5 rounded-md ${
                    a.plantao ? 'bg-purple-100 text-purple-700' : 'bg-teal-100 text-teal-700'
                  }`}>
                    {a.plantao ? 'Plantão' : 'Turno'}
                  </span>
                </td>
                <td className="px-5 py-3 text-right">
                  <button onClick={() => { if (confirm('Remover plantão?')) remover.mutate(a.id) }}
                    className="p-1.5 rounded-lg hover:bg-red-50 hover:text-red-500 text-slate-400 transition-colors">
                    <Trash2 size={15} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal open={modal} onClose={() => setModal(false)} title="Novo Plantão">
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-xs font-medium text-slate-600 mb-1.5">Médico</label>
            <select required value={form.id_medico} onChange={(e) => setForm({ ...form, id_medico: e.target.value })} className="input">
              <option value="">Selecione um médico</option>
              {medicos.map((m) => <option key={m.id} value={m.id}>{m.nome} — CRM {m.crm}-{m.uf_crm}</option>)}
            </select>
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-600 mb-1.5">Data</label>
            <input required type="date" value={form.data_disponibilidade}
              onChange={(e) => setForm({ ...form, data_disponibilidade: e.target.value })} className="input" />
          </div>

          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Horário início</label>
              <input required type="time" value={form.hora_inicio}
                onChange={(e) => setForm({ ...form, hora_inicio: e.target.value })} className="input" />
            </div>
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Horário término</label>
              <input required type="time" value={form.hora_fim}
                onChange={(e) => setForm({ ...form, hora_fim: e.target.value })} className="input" />
            </div>
          </div>

          <label className="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked={form.plantao}
              onChange={(e) => setForm({ ...form, plantao: e.target.checked })}
              className="w-4 h-4 rounded accent-brand" />
            <span className="text-sm text-slate-600">Plantão (12h ou 24h)</span>
          </label>

          {formError && <p className="text-xs text-red-500 bg-red-50 border border-red-100 rounded-lg px-3 py-2">{formError}</p>}

          <div className="flex justify-end gap-2 pt-1">
            <button type="button" onClick={() => setModal(false)} className="btn-secondary">Cancelar</button>
            <button type="submit" disabled={criar.isPending} className="btn-primary flex items-center gap-1.5">
              {criar.isPending ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" /> : 'Salvar plantão'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  )
}
