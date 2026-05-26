import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { ArrowLeft, Save } from 'lucide-react'
import { agendaApi } from '../api/agenda'
import { medicosApi } from '../api/medicos'
import { paraInputDate } from '../utils/datas'
import { useToast } from '../contexts/ToastContext'

const FORM_VAZIO = {
  id_medico: '',
  data_disponibilidade: '',
  hora_inicio: '',
  hora_fim: '',
  plantao: false,
}

export default function AgendaForm() {
  const { id }     = useParams()
  const navigate   = useNavigate()
  const qc         = useQueryClient()
  const { mostrar } = useToast()
  const ehEdicao   = Boolean(id)

  const [form, setForm] = useState(FORM_VAZIO)
  const [erro, setErro] = useState('')

  const { data: medicos = [] } = useQuery({
    queryKey: ['medicos-select'],
    queryFn:  () => medicosApi.listar({ status: 'A' }).then((r) => r.data),
  })

  const { data: plantao, isLoading: carregando } = useQuery({
    queryKey: ['agenda', id],
    queryFn:  () => agendaApi.buscar(id).then((r) => r.data),
    enabled:  ehEdicao,
  })

  useEffect(() => {
    if (plantao) {
      setForm({
        id_medico:            plantao.id_medico || '',
        data_disponibilidade: paraInputDate(plantao.data_disponibilidade),
        hora_inicio:          plantao.hora_inicio?.slice(0, 5) || '',
        hora_fim:             plantao.hora_fim?.slice(0, 5) || '',
        plantao:              Boolean(plantao.plantao),
      })
    }
  }, [plantao])

  const salvar = useMutation({
    mutationFn: (dados) => ehEdicao
      ? agendaApi.atualizar(id, dados)
      : agendaApi.criar(dados),
    onSuccess: () => {
      qc.invalidateQueries(['agenda'])
      mostrar(ehEdicao ? 'Plantão atualizado.' : 'Plantão cadastrado.', 'sucesso')
      navigate('/agenda')
    },
    onError: (err) => {
      setErro(err.mensagemAmigavel || 'Erro ao salvar o plantão.')
    },
  })

  const handleSubmit = (e) => {
    e.preventDefault()
    setErro('')

    if (!form.id_medico) {
      setErro('Selecione um médico.')
      return
    }
    if (!form.data_disponibilidade) {
      setErro('Informe a data do plantão.')
      return
    }
    if (form.hora_inicio >= form.hora_fim) {
      setErro('O horário de término deve ser depois do início.')
      return
    }

    salvar.mutate({
      id_medico:            Number(form.id_medico),
      data_disponibilidade: form.data_disponibilidade,
      hora_inicio:          form.hora_inicio,
      hora_fim:             form.hora_fim,
      plantao:              form.plantao,
    })
  }

  if (ehEdicao && carregando) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="w-6 h-6 border-2 border-brand border-t-transparent rounded-full animate-spin" />
      </div>
    )
  }

  return (
    <div className="max-w-xl mx-auto space-y-6">
      <div className="flex items-center gap-3">
        <button onClick={() => navigate('/agenda')}
          className="p-1.5 rounded-lg hover:bg-slate-200 text-slate-500 transition-colors">
          <ArrowLeft size={18} />
        </button>
        <div>
          <h1 className="text-xl font-semibold text-slate-800">
            {ehEdicao ? 'Editar Plantão' : 'Novo Plantão'}
          </h1>
          <p className="text-sm text-slate-400 mt-0.5">
            {ehEdicao ? 'Atualize os dados do plantão' : 'Cadastre a disponibilidade de um médico'}
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-5">
        <div className="card space-y-4">
          <div>
            <label className="block text-xs font-medium text-slate-600 mb-1.5">Médico</label>
            <select required value={form.id_medico}
              onChange={(e) => setForm({ ...form, id_medico: e.target.value })} className="input">
              <option value="">Selecione um médico</option>
              {medicos.map((m) => (
                <option key={m.id} value={m.id}>{m.nome} — CRM {m.crm}-{m.uf_crm}</option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-xs font-medium text-slate-600 mb-1.5">Data</label>
            <input required type="date" value={form.data_disponibilidade}
              onChange={(e) => setForm({ ...form, data_disponibilidade: e.target.value })}
              className="input" />
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Horário início</label>
              <input required type="time" value={form.hora_inicio}
                onChange={(e) => setForm({ ...form, hora_inicio: e.target.value })}
                className="input" />
            </div>
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Horário término</label>
              <input required type="time" value={form.hora_fim}
                onChange={(e) => setForm({ ...form, hora_fim: e.target.value })}
                className="input" />
            </div>
          </div>

          <label className="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" checked={form.plantao}
              onChange={(e) => setForm({ ...form, plantao: e.target.checked })}
              className="w-4 h-4 rounded accent-brand" />
            <span className="text-sm text-slate-600">Plantão (turno extra, 12h ou 24h)</span>
          </label>
        </div>

        {erro && (
          <p className="text-xs text-red-500 bg-red-50 border border-red-100 rounded-lg px-4 py-3">{erro}</p>
        )}

        <div className="flex justify-end gap-3">
          <button type="button" onClick={() => navigate('/agenda')} className="btn-secondary">
            Cancelar
          </button>
          <button type="submit" disabled={salvar.isPending} className="btn-primary flex items-center gap-2">
            {salvar.isPending
              ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
              : <><Save size={15} /> {ehEdicao ? 'Salvar alterações' : 'Cadastrar plantão'}</>
            }
          </button>
        </div>
      </form>
    </div>
  )
}
