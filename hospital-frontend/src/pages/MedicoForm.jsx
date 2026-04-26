import { useState, useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { ArrowLeft, Save } from 'lucide-react'
import { medicosApi } from '../api/medicos'
import { useQuery, useMutation } from '@tanstack/react-query'

const EMPTY_FORM = {
  nome: '', cpf: '', email: '', telefone: '', data_nascimento: '',
  crm: '', uf_crm: 'SC', tipo: '', especialidade: '', sub_especialidade: '',
  data_contratacao: '',
  endereco: { cep: '', logradouro: '', numero: '', cidade: '', estado: 'SC' },
}

function Field({ label, children, span }) {
  return (
    <div className={span ? `col-span-${span}` : ''}>
      <label className="block text-xs font-medium text-slate-600 mb-1.5">{label}</label>
      {children}
    </div>
  )
}

export default function MedicoForm() {
  const { id }   = useParams()
  const navigate = useNavigate()
  const isEdit   = Boolean(id)

  const [form, setForm]   = useState(EMPTY_FORM)
  const [error, setError] = useState('')

  // Carrega dados do médico se for edição
  const { isLoading: loadingMedico } = useQuery({
    queryKey: ['medico', id],
    queryFn:  () => medicosApi.buscar(id).then((r) => r.data),
    enabled:  isEdit,
    onSuccess: (data) => {
      setForm({
        nome:              data.pessoa?.nome              || '',
        cpf:               data.pessoa?.cpf               || '',
        email:             data.pessoa?.email             || '',
        telefone:          data.pessoa?.telefone          || '',
        data_nascimento:   data.pessoa?.data_nascimento   || '',
        crm:               data.crm                      || '',
        uf_crm:            data.uf_crm                   || 'SC',
        tipo:              data.tipo                      || '',
        especialidade:     data.especialidade             || '',
        sub_especialidade: data.sub_especialidade         || '',
        data_contratacao:  data.data_contratacao          || '',
        endereco: {
          cep:        data.pessoa?.endereco?.cep        || '',
          logradouro: data.pessoa?.endereco?.logradouro || '',
          numero:     data.pessoa?.endereco?.numero     || '',
          cidade:     data.pessoa?.endereco?.cidade     || '',
          estado:     data.pessoa?.endereco?.estado     || 'SC',
        },
      })
    },
  })

  const salvar = useMutation({
    mutationFn: (payload) => isEdit ? medicosApi.atualizar(id, payload) : medicosApi.criar(payload),
    onSuccess: () => navigate('/medicos'),
    onError: (err) => setError(
      err.response?.data?.mensagem ||
      JSON.stringify(err.response?.data?.erros) ||
      'Erro ao salvar.'
    ),
  })

  const f  = (field, val) => setForm((p) => ({ ...p, [field]: val }))
  const fe = (field, val) => setForm((p) => ({ ...p, endereco: { ...p.endereco, [field]: val } }))

  const handleSubmit = (e) => {
    e.preventDefault()
    setError('')
    const payload = { ...form }
    if (!payload.endereco.cep && !payload.endereco.logradouro) delete payload.endereco
    salvar.mutate(payload)
  }

  if (isEdit && loadingMedico) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="w-6 h-6 border-2 border-brand border-t-transparent rounded-full animate-spin" />
      </div>
    )
  }

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      {/* Cabeçalho da página */}
      <div className="flex items-center gap-3">
        <button onClick={() => navigate('/medicos')}
          className="p-1.5 rounded-lg hover:bg-slate-200 text-slate-500 transition-colors">
          <ArrowLeft size={18} />
        </button>
        <div>
          <h1 className="text-xl font-semibold text-slate-800">
            {isEdit ? 'Editar Médico' : 'Cadastrar Médico'}
          </h1>
          <p className="text-sm text-slate-400 mt-0.5">
            {isEdit ? 'Atualize os dados do médico' : 'Preencha os dados para cadastrar um novo médico'}
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-5">

        {/* Dados pessoais */}
        <div className="card space-y-4">
          <h2 className="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dados pessoais</h2>
          <div className="grid grid-cols-2 gap-4">
            <div className="col-span-2">
              <Field label="Nome completo">
                <input required value={form.nome} onChange={(e) => f('nome', e.target.value)}
                  placeholder="Dra. Ana Lima" className="input" />
              </Field>
            </div>
            <Field label="CPF">
              <input required maxLength={11} value={form.cpf} onChange={(e) => f('cpf', e.target.value)}
                placeholder="Somente números" className="input" />
            </Field>
            <Field label="Data de nascimento">
              <input type="date" value={form.data_nascimento}
                onChange={(e) => f('data_nascimento', e.target.value)} className="input" />
            </Field>
            <Field label="E-mail">
              <input required type="email" value={form.email} onChange={(e) => f('email', e.target.value)}
                placeholder="ana@hospital.com" className="input" />
            </Field>
            <Field label="Telefone">
              <input maxLength={11} value={form.telefone} onChange={(e) => f('telefone', e.target.value)}
                placeholder="48999999999" className="input" />
            </Field>
          </div>
        </div>

        {/* Dados profissionais */}
        <div className="card space-y-4">
          <h2 className="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dados profissionais</h2>
          <div className="grid grid-cols-3 gap-4">
            <Field label="CRM">
              <input required maxLength={6} value={form.crm} onChange={(e) => f('crm', e.target.value)}
                placeholder="123456" className="input" />
            </Field>
            <Field label="UF CRM">
              <input required maxLength={2} value={form.uf_crm}
                onChange={(e) => f('uf_crm', e.target.value.toUpperCase())} className="input" />
            </Field>
            <Field label="Tipo">
              <input value={form.tipo} onChange={(e) => f('tipo', e.target.value)}
                placeholder="Ex: Especialista" className="input" />
            </Field>
            <Field label="Especialidade">
              <input value={form.especialidade} onChange={(e) => f('especialidade', e.target.value)}
                placeholder="Cardiologia" className="input" />
            </Field>
            <Field label="Sub-especialidade">
              <input value={form.sub_especialidade} onChange={(e) => f('sub_especialidade', e.target.value)}
                className="input" />
            </Field>
            <Field label="Data de contratação">
              <input type="date" value={form.data_contratacao}
                onChange={(e) => f('data_contratacao', e.target.value)} className="input" />
            </Field>
          </div>
        </div>

        {/* Endereço */}
        <div className="card space-y-4">
          <h2 className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Endereço <span className="normal-case font-normal text-slate-400">(opcional)</span>
          </h2>
          <div className="grid grid-cols-3 gap-4">
            <Field label="CEP">
              <input maxLength={8} value={form.endereco.cep} onChange={(e) => fe('cep', e.target.value)}
                placeholder="88700000" className="input" />
            </Field>
            <div className="col-span-2">
              <Field label="Logradouro">
                <input value={form.endereco.logradouro} onChange={(e) => fe('logradouro', e.target.value)}
                  placeholder="Rua das Flores" className="input" />
              </Field>
            </div>
            <Field label="Número">
              <input value={form.endereco.numero} onChange={(e) => fe('numero', e.target.value)}
                placeholder="123" className="input" />
            </Field>
            <Field label="Cidade">
              <input value={form.endereco.cidade} onChange={(e) => fe('cidade', e.target.value)}
                placeholder="Tubarão" className="input" />
            </Field>
            <Field label="Estado">
              <input maxLength={2} value={form.endereco.estado}
                onChange={(e) => fe('estado', e.target.value.toUpperCase())} className="input" />
            </Field>
          </div>
        </div>

        {/* Erro + submit */}
        {error && (
          <p className="text-xs text-red-500 bg-red-50 border border-red-100 rounded-lg px-4 py-3">{error}</p>
        )}

        <div className="flex justify-end gap-3">
          <button type="button" onClick={() => navigate('/medicos')} className="btn-secondary">
            Cancelar
          </button>
          <button type="submit" disabled={salvar.isPending} className="btn-primary flex items-center gap-2">
            {salvar.isPending
              ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
              : <><Save size={15} /> {isEdit ? 'Salvar alterações' : 'Cadastrar médico'}</>
            }
          </button>
        </div>
      </form>
    </div>
  )
}
