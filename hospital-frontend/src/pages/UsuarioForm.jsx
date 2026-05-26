import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { ArrowLeft, Save, AlertCircle, Info } from 'lucide-react'
import { usuariosApi } from '../api/usuarios'
import { emailValido } from '../utils/validadores'
import { useToast } from '../contexts/ToastContext'

const FUNCOES = [
  { valor: 'administrador',  label: 'Administrador'  },
  { valor: 'medico',         label: 'Médico'         },
  { valor: 'farmaceutico',   label: 'Farmacêutico'   },
  { valor: 'recepcionista',  label: 'Recepcionista'  },
  { valor: 'paciente',       label: 'Paciente'       },
]

const FORM_VAZIO = { nome: '', email: '', funcao: 'paciente', id_cadastro: '' }

function erroDoCampo(erros, campo) {
  return erros?.[campo]?.[0]
}

function Campo({ label, erro, children, hint, className = '' }) {
  return (
    <div className={className}>
      <label className="block text-xs font-medium text-slate-600 mb-1.5">{label}</label>
      {children}
      {hint && !erro && (
        <p className="mt-1 text-xs text-slate-400 flex items-center gap-1">
          <Info size={11} /> {hint}
        </p>
      )}
      {erro && (
        <p className="mt-1 text-xs text-red-500 flex items-center gap-1">
          <AlertCircle size={11} /> {erro}
        </p>
      )}
    </div>
  )
}

export default function UsuarioForm() {
  const { id }      = useParams()
  const navigate    = useNavigate()
  const qc          = useQueryClient()
  const { mostrar } = useToast()
  const ehEdicao    = Boolean(id)

  const [form, setForm] = useState(FORM_VAZIO)
  const [erro, setErro] = useState('')
  const [errosCampos, setErrosCampos] = useState({})

  const ehAdmin = form.funcao === 'administrador'

  const { data: usuario, isLoading: carregando } = useQuery({
    queryKey: ['usuario', id],
    queryFn:  () => usuariosApi.buscar(id).then((r) => r.data),
    enabled:  ehEdicao,
  })

  useEffect(() => {
    if (usuario) {
      setForm({
        nome:        usuario.usuario     || '',
        email:       usuario.email       || '',
        funcao:      usuario.funcao      || 'paciente',
        id_cadastro: usuario.id_cadastro || '',
      })
    }
  }, [usuario])

  const salvar = useMutation({
    mutationFn: (dados) => ehEdicao
      ? usuariosApi.atualizar(id, { nome: dados.nome, email: dados.email, funcao: dados.funcao })
      : usuariosApi.registrar(dados),
    onSuccess: () => {
      qc.invalidateQueries(['usuarios'])
      mostrar(ehEdicao ? 'Usuário atualizado.' : 'Usuário criado. Senha enviada por e-mail.', 'sucesso')
      navigate('/usuarios')
    },
    onError: (err) => {
      setErro(err.mensagemAmigavel || 'Erro ao salvar o usuário.')
      setErrosCampos(err.errosPorCampo || {})
    },
  })

  const handleSubmit = (e) => {
    e.preventDefault()
    setErro('')
    setErrosCampos({})

    if (form.nome.trim().length < 3) {
      setErro('Informe o nome completo do usuário (mínimo 3 caracteres).')
      return
    }
    if (!emailValido(form.email)) {
      setErro('Informe um e-mail válido.')
      return
    }
    if (!ehEdicao && !ehAdmin && !form.id_cadastro) {
      setErro('Informe o ID do cadastro no módulo de origem.')
      return
    }

    salvar.mutate({
      nome:        form.nome.trim(),
      email:       form.email.trim().toLowerCase(),
      funcao:      form.funcao,
      id_cadastro: ehAdmin ? null : Number(form.id_cadastro),
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
    <div className="max-w-2xl mx-auto space-y-6">
      <div className="flex items-center gap-3">
        <button onClick={() => navigate('/usuarios')}
          className="p-1.5 rounded-lg hover:bg-slate-200 text-slate-500 transition-colors">
          <ArrowLeft size={18} />
        </button>
        <div>
          <h1 className="text-xl font-semibold text-slate-800">
            {ehEdicao ? 'Editar Usuário' : 'Novo Usuário'}
          </h1>
          <p className="text-sm text-slate-400 mt-0.5">
            {ehEdicao
              ? 'Atualize os dados de acesso do usuário'
              : 'Preencha os dados para criar um novo usuário do sistema'}
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="space-y-5">
        <div className="card space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <Campo label="Nome completo" erro={erroDoCampo(errosCampos, 'nome')} className="col-span-2">
              <input required value={form.nome}
                onChange={(e) => setForm({ ...form, nome: e.target.value })}
                placeholder="João da Silva" className="input" />
            </Campo>

            <Campo label="E-mail" erro={erroDoCampo(errosCampos, 'email')} className="col-span-2">
              <input required type="email" value={form.email}
                onChange={(e) => setForm({ ...form, email: e.target.value })}
                placeholder="joao@email.com" className="input" />
            </Campo>

            <Campo label="Função" erro={erroDoCampo(errosCampos, 'funcao')}>
              <select required value={form.funcao}
                onChange={(e) => setForm({ ...form, funcao: e.target.value, id_cadastro: e.target.value === 'administrador' ? '' : form.id_cadastro })}
                className="input"
                disabled={ehEdicao}
              >
                {FUNCOES.map((f) => <option key={f.valor} value={f.valor}>{f.label}</option>)}
              </select>
              {ehEdicao && (
                <p className="mt-1 text-xs text-slate-400">A função não pode ser alterada após o cadastro.</p>
              )}
            </Campo>

            {!ehEdicao && !ehAdmin && (
              <Campo
                label="ID do cadastro"
                erro={erroDoCampo(errosCampos, 'id_cadastro')}
                hint="ID do usuário no módulo de origem (ex: ID do paciente no Grupo 2)"
              >
                <input required type="number" min="1" value={form.id_cadastro}
                  onChange={(e) => setForm({ ...form, id_cadastro: e.target.value })}
                  placeholder="Ex: 1" className="input" />
              </Campo>
            )}

            {!ehEdicao && ehAdmin && (
              <div className="col-span-2 bg-sky-50 border border-sky-200 rounded-lg p-3 text-xs text-sky-800 flex items-start gap-2">
                <Info size={14} className="shrink-0 mt-0.5" />
                <p>
                  Administradores não precisam de um ID de cadastro externo.
                  O usuário será criado apenas no módulo de acesso.
                </p>
              </div>
            )}
          </div>
        </div>

        {erro && (
          <div className="text-sm bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-start gap-2">
            <AlertCircle size={18} className="text-red-500 shrink-0 mt-0.5" />
            <p className="text-red-700">{erro}</p>
          </div>
        )}

        <div className="flex justify-end gap-3">
          <button type="button" onClick={() => navigate('/usuarios')} className="btn-secondary">
            Cancelar
          </button>
          <button type="submit" disabled={salvar.isPending} className="btn-primary flex items-center gap-2">
            {salvar.isPending
              ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
              : <><Save size={15} /> {ehEdicao ? 'Salvar alterações' : 'Criar usuário'}</>
            }
          </button>
        </div>
      </form>
    </div>
  )
}
