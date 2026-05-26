import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { ArrowLeft, Save, AlertCircle } from 'lucide-react'
import { medicosApi } from '../api/medicos'
import InputMascarado from '../components/ui/InputMascarado'
import { mascararCpf, mascararTelefone, mascararCep, removerMascara } from '../utils/mascaras'
import { cpfValido, emailValido, telefoneValido } from '../utils/validadores'
import { useToast } from '../contexts/ToastContext'

const FORM_VAZIO = {
  nome: '', cpf: '', email: '', telefone: '', data_nascimento: '',
  crm: '', uf_crm: 'SC', tipo: '', especialidade: '', sub_especialidade: '',
  data_contratacao: '',
  endereco: { cep: '', logradouro: '', numero: '', cidade: '', estado: 'SC' },
}

const TIPOS_ATENDIMENTO = ['Clínico Geral', 'Especialista', 'Plantonista', 'Cirurgião', 'Residente']

function erroDoCampo(erros, campo) {
  return erros?.[campo]?.[0]
}

function Campo({ label, obrigatorio, erro, children, className = '' }) {
  return (
    <div className={className}>
      <label className="block text-xs font-medium text-slate-600 mb-1.5">
        {label}
        {obrigatorio && <span className="text-red-500 ml-0.5">*</span>}
      </label>
      {children}
      {erro && (
        <p className="mt-1 text-xs text-red-500 flex items-center gap-1">
          <AlertCircle size={11} /> {erro}
        </p>
      )}
    </div>
  )
}

export default function MedicoForm() {
  const { id }   = useParams()
  const navigate = useNavigate()
  const qc       = useQueryClient()
  const { mostrar } = useToast()
  const ehEdicao = Boolean(id)

  const [form, setForm] = useState(FORM_VAZIO)
  const [erro, setErro] = useState('')
  const [errosCampos, setErrosCampos] = useState({})

  const { data: medico, isLoading: carregando } = useQuery({
    queryKey: ['medico', id],
    queryFn:  () => medicosApi.buscar(id).then((r) => r.data),
    enabled:  ehEdicao,
  })

  useEffect(() => {
    if (medico) {
      setForm({
        nome:              medico.nome              || '',
        cpf:               medico.cpf ? mascararCpf(medico.cpf) : '',
        email:             medico.email             || '',
        telefone:          medico.telefone ? mascararTelefone(medico.telefone) : '',
        data_nascimento:   medico.data_nascimento   || '',
        crm:               medico.crm               || '',
        uf_crm:            medico.uf_crm            || 'SC',
        tipo:              medico.tipo              || '',
        especialidade:     medico.especialidade     || '',
        sub_especialidade: medico.sub_especialidade || '',
        data_contratacao:  medico.data_contratacao  || '',
        endereco: {
          cep:        medico.endereco?.cep ? mascararCep(medico.endereco.cep) : '',
          logradouro: medico.endereco?.logradouro || '',
          numero:     medico.endereco?.numero     || '',
          cidade:     medico.endereco?.cidade     || '',
          estado:     medico.endereco?.estado     || 'SC',
        },
      })
    }
  }, [medico])

  const salvar = useMutation({
    mutationFn: (payload) => ehEdicao ? medicosApi.atualizar(id, payload) : medicosApi.criar(payload),
    onSuccess: () => {
      qc.invalidateQueries(['medicos'])
      mostrar(ehEdicao ? 'Médico atualizado.' : 'Médico cadastrado. Senha enviada por e-mail.', 'sucesso')
      navigate('/medicos')
    },
    onError: (err) => {
      setErro(err.mensagemAmigavel || 'Não foi possível salvar o médico.')
      setErrosCampos(err.errosPorCampo || {})
    },
  })

  const f  = (campo, valor) => setForm((p) => ({ ...p, [campo]: valor }))
  const fe = (campo, valor) => setForm((p) => ({ ...p, endereco: { ...p.endereco, [campo]: valor } }))

  const handleSubmit = (e) => {
    e.preventDefault()
    setErro('')
    setErrosCampos({})

    // Validações locais
    if (form.nome.trim().length < 3) {
      setErro('Informe o nome completo do médico.')
      return
    }
    if (!cpfValido(form.cpf)) {
      setErro('CPF inválido. Verifique os dígitos.')
      return
    }
    if (!form.data_nascimento) {
      setErro('Informe a data de nascimento.')
      return
    }
    if (!emailValido(form.email)) {
      setErro('Informe um e-mail válido.')
      return
    }
    if (!telefoneValido(form.telefone)) {
      setErro('Informe um telefone válido (10 ou 11 dígitos).')
      return
    }
    if (!form.crm.trim()) {
      setErro('Informe o CRM.')
      return
    }
    if (!form.tipo) {
      setErro('Selecione o tipo de atendimento.')
      return
    }
    if (!form.especialidade.trim()) {
      setErro('Informe a especialidade do médico.')
      return
    }
    if (!form.data_contratacao) {
      setErro('Informe a data de contratação.')
      return
    }

    const payload = {
      nome:              form.nome.trim(),
      cpf:               removerMascara(form.cpf),
      email:             form.email.trim().toLowerCase(),
      telefone:          removerMascara(form.telefone),
      data_nascimento:   form.data_nascimento,
      crm:               form.crm.trim(),
      uf_crm:            form.uf_crm.toUpperCase(),
      tipo:              form.tipo,
      especialidade:     form.especialidade.trim(),
      sub_especialidade: form.sub_especialidade.trim() || undefined,
      data_contratacao:  form.data_contratacao,
    }

    if (form.endereco.cep || form.endereco.logradouro) {
      payload.endereco = {
        cep:        removerMascara(form.endereco.cep) || undefined,
        logradouro: form.endereco.logradouro || undefined,
        numero:     form.endereco.numero || undefined,
        cidade:     form.endereco.cidade || undefined,
        estado:     form.endereco.estado.toUpperCase() || undefined,
      }
    }

    salvar.mutate(payload)
  }

  if (ehEdicao && carregando) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="w-6 h-6 border-2 border-brand border-t-transparent rounded-full animate-spin" />
      </div>
    )
  }

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <div className="flex items-center gap-3">
        <button onClick={() => navigate('/medicos')}
          className="p-1.5 rounded-lg hover:bg-slate-200 text-slate-500 transition-colors">
          <ArrowLeft size={18} />
        </button>
        <div>
          <h1 className="text-xl font-semibold text-slate-800">
            {ehEdicao ? 'Editar Médico' : 'Cadastrar Médico'}
          </h1>
          <p className="text-sm text-slate-400 mt-0.5">
            Campos com <span className="text-red-500">*</span> são obrigatórios
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-5">

        {/* Dados pessoais */}
        <div className="card space-y-4">
          <h2 className="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dados pessoais</h2>
          <div className="grid grid-cols-2 gap-4">
            <Campo label="Nome completo" obrigatorio erro={erroDoCampo(errosCampos, 'nome')} className="col-span-2">
              <input required value={form.nome} onChange={(e) => f('nome', e.target.value)}
                placeholder="Dra. Ana Lima" className="input" />
            </Campo>
            <Campo label="CPF" obrigatorio erro={erroDoCampo(errosCampos, 'cpf')}>
              <InputMascarado mascara="cpf" value={form.cpf}
                onChange={(v) => f('cpf', v)} required placeholder="000.000.000-00" />
            </Campo>
            <Campo label="Data de nascimento" obrigatorio erro={erroDoCampo(errosCampos, 'data_nascimento')}>
              <input type="date" required value={form.data_nascimento}
                onChange={(e) => f('data_nascimento', e.target.value)} className="input" />
            </Campo>
            <Campo label="E-mail" obrigatorio erro={erroDoCampo(errosCampos, 'email')}>
              <input required type="email" value={form.email}
                onChange={(e) => f('email', e.target.value)}
                placeholder="ana@hospital.com" className="input" />
            </Campo>
            <Campo label="Telefone" obrigatorio erro={erroDoCampo(errosCampos, 'telefone')}>
              <InputMascarado mascara="telefone" value={form.telefone}
                onChange={(v) => f('telefone', v)} required placeholder="(48) 99999-9999" />
            </Campo>
          </div>
        </div>

        {/* Dados profissionais */}
        <div className="card space-y-4">
          <h2 className="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dados profissionais</h2>
          <div className="grid grid-cols-3 gap-4">
            <Campo label="CRM" obrigatorio erro={erroDoCampo(errosCampos, 'crm')}>
              <input required maxLength={6} value={form.crm}
                onChange={(e) => f('crm', e.target.value.replace(/\D/g, ''))}
                placeholder="123456" className="input" />
            </Campo>
            <Campo label="UF do CRM" obrigatorio erro={erroDoCampo(errosCampos, 'uf_crm')}>
              <input required maxLength={2} value={form.uf_crm}
                onChange={(e) => f('uf_crm', e.target.value.toUpperCase())} className="input" />
            </Campo>
            <Campo label="Tipo de atendimento" obrigatorio erro={erroDoCampo(errosCampos, 'tipo')}>
              <select required value={form.tipo} onChange={(e) => f('tipo', e.target.value)} className="input">
                <option value="">Selecione</option>
                {TIPOS_ATENDIMENTO.map((t) => <option key={t} value={t}>{t}</option>)}
              </select>
            </Campo>
            <Campo label="Especialidade" obrigatorio erro={erroDoCampo(errosCampos, 'especialidade')}>
              <input required value={form.especialidade}
                onChange={(e) => f('especialidade', e.target.value)}
                placeholder="Cardiologia" className="input" />
            </Campo>
            <Campo label="Sub-especialidade" erro={erroDoCampo(errosCampos, 'sub_especialidade')}>
              <input value={form.sub_especialidade}
                onChange={(e) => f('sub_especialidade', e.target.value)}
                placeholder="(opcional)" className="input" />
            </Campo>
            <Campo label="Data de contratação" obrigatorio erro={erroDoCampo(errosCampos, 'data_contratacao')}>
              <input required type="date" value={form.data_contratacao}
                onChange={(e) => f('data_contratacao', e.target.value)} className="input" />
            </Campo>
          </div>
        </div>

        {/* Endereço */}
        <div className="card space-y-4">
          <h2 className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Endereço <span className="normal-case font-normal text-slate-400">(opcional)</span>
          </h2>
          <div className="grid grid-cols-3 gap-4">
            <Campo label="CEP" erro={erroDoCampo(errosCampos, 'endereco.cep')}>
              <InputMascarado mascara="cep" value={form.endereco.cep}
                onChange={(v) => fe('cep', v)} placeholder="00000-000" />
            </Campo>
            <Campo label="Logradouro" erro={erroDoCampo(errosCampos, 'endereco.logradouro')} className="col-span-2">
              <input value={form.endereco.logradouro}
                onChange={(e) => fe('logradouro', e.target.value)}
                placeholder="Rua das Flores" className="input" />
            </Campo>
            <Campo label="Número" erro={erroDoCampo(errosCampos, 'endereco.numero')}>
              <input value={form.endereco.numero} onChange={(e) => fe('numero', e.target.value)}
                placeholder="123" className="input" />
            </Campo>
            <Campo label="Cidade" erro={erroDoCampo(errosCampos, 'endereco.cidade')}>
              <input value={form.endereco.cidade} onChange={(e) => fe('cidade', e.target.value)}
                placeholder="Tubarão" className="input" />
            </Campo>
            <Campo label="Estado" erro={erroDoCampo(errosCampos, 'endereco.estado')}>
              <input maxLength={2} value={form.endereco.estado}
                onChange={(e) => fe('estado', e.target.value.toUpperCase())} className="input" />
            </Campo>
          </div>
        </div>

        {erro && (
          <div className="text-sm bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-start gap-2">
            <AlertCircle size={18} className="text-red-500 shrink-0 mt-0.5" />
            <p className="text-red-700">{erro}</p>
          </div>
        )}

        <div className="flex justify-end gap-3">
          <button type="button" onClick={() => navigate('/medicos')} className="btn-secondary">
            Cancelar
          </button>
          <button type="submit" disabled={salvar.isPending} className="btn-primary flex items-center gap-2">
            {salvar.isPending
              ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
              : <><Save size={15} /> {ehEdicao ? 'Salvar alterações' : 'Cadastrar médico'}</>
            }
          </button>
        </div>
      </form>
    </div>
  )
}
