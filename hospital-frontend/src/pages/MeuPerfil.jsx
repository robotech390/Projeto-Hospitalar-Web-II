import { useEffect, useState } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { Save, Lock, AlertCircle, UserCircle, Stethoscope } from 'lucide-react'
import { meuPerfilApi } from '../api/meuPerfil'
import { authApi } from '../api/auth'
import InputMascarado from '../components/ui/InputMascarado'
import { mascararCpf, mascararTelefone, mascararCep, removerMascara } from '../utils/mascaras'
import { emailValido, senhaForte, telefoneValido } from '../utils/validadores'
import { paraInputDate, formatarData } from '../utils/datas'
import { useToast } from '../contexts/ToastContext'

function Campo({ label, erro, somenteLeitura, hint, children, className = '' }) {
  return (
    <div className={className}>
      <label className="block text-xs font-medium text-slate-600 mb-1.5">
        {label}
        {somenteLeitura && <span className="ml-1 text-[10px] text-slate-400">(somente leitura)</span>}
      </label>
      {children}
      {hint && !erro && <p className="mt-1 text-xs text-slate-400">{hint}</p>}
      {erro && (
        <p className="mt-1 text-xs text-red-500 flex items-center gap-1">
          <AlertCircle size={11} /> {erro}
        </p>
      )}
    </div>
  )
}

export default function MeuPerfil() {
  const qc = useQueryClient()
  const { mostrar } = useToast()

  const [form, setForm] = useState({
    email: '', telefone: '', data_nascimento: '',
    endereco: { cep: '', logradouro: '', numero: '', cidade: '', estado: '' },
  })
  const [erro, setErro] = useState('')

  const [formSenha, setFormSenha] = useState({
    senha_atual: '',
    nova_senha: '',
    nova_senha_confirmation: '',
  })
  const [erroSenha, setErroSenha] = useState('')

  const { data: perfil, isLoading } = useQuery({
    queryKey: ['meu-perfil'],
    queryFn:  () => meuPerfilApi.buscar().then((r) => r.data),
  })

  useEffect(() => {
    if (perfil?.pessoa) {
      setForm({
        email:           perfil.pessoa.email    || perfil.email || '',
        telefone:        perfil.pessoa.telefone ? mascararTelefone(perfil.pessoa.telefone) : '',
        data_nascimento: paraInputDate(perfil.pessoa.data_nascimento),
        endereco: {
          cep:        perfil.pessoa.endereco?.cep ? mascararCep(perfil.pessoa.endereco.cep) : '',
          logradouro: perfil.pessoa.endereco?.logradouro || '',
          numero:     perfil.pessoa.endereco?.numero     || '',
          cidade:     perfil.pessoa.endereco?.cidade     || '',
          estado:     perfil.pessoa.endereco?.estado     || '',
        },
      })
    } else if (perfil) {
      // Usuário sem pessoa vinculada (ex: admin)
      setForm((p) => ({ ...p, email: perfil.email || '' }))
    }
  }, [perfil])

  const atualizar = useMutation({
    mutationFn: (dados) => meuPerfilApi.atualizar(dados),
    onSuccess: () => {
      qc.invalidateQueries(['meu-perfil'])
      mostrar('Dados atualizados com sucesso.', 'sucesso')
    },
    onError: (err) => setErro(err.mensagemAmigavel || 'Não foi possível atualizar seus dados.'),
  })

  const alterarSenha = useMutation({
    mutationFn: (dados) => authApi.alterarSenha(dados),
    onSuccess: () => {
      mostrar('Senha alterada com sucesso.', 'sucesso')
      setFormSenha({ senha_atual: '', nova_senha: '', nova_senha_confirmation: '' })
    },
    onError: (err) => setErroSenha(err.mensagemAmigavel || 'Não foi possível alterar a senha.'),
  })

  const handleSubmitDados = (e) => {
    e.preventDefault()
    setErro('')

    if (form.email && !emailValido(form.email)) {
      setErro('Informe um e-mail válido.')
      return
    }
    if (form.telefone && !telefoneValido(form.telefone)) {
      setErro('Telefone inválido (10 ou 11 dígitos).')
      return
    }

    const payload = {}
    if (form.email)           payload.email           = form.email.trim().toLowerCase()
    if (form.telefone)        payload.telefone        = removerMascara(form.telefone)
    if (form.data_nascimento) payload.data_nascimento = form.data_nascimento

    if (form.endereco.cep || form.endereco.logradouro) {
      payload.endereco = {
        cep:        removerMascara(form.endereco.cep) || undefined,
        logradouro: form.endereco.logradouro || undefined,
        numero:     form.endereco.numero || undefined,
        cidade:     form.endereco.cidade || undefined,
        estado:     form.endereco.estado.toUpperCase() || undefined,
      }
    }

    atualizar.mutate(payload)
  }

  const handleSubmitSenha = (e) => {
    e.preventDefault()
    setErroSenha('')

    if (!formSenha.senha_atual) {
      setErroSenha('Informe sua senha atual.')
      return
    }
    if (!senhaForte(formSenha.nova_senha)) {
      setErroSenha('A nova senha deve ter ao menos 8 caracteres, uma letra e um número.')
      return
    }
    if (formSenha.nova_senha !== formSenha.nova_senha_confirmation) {
      setErroSenha('As novas senhas não conferem.')
      return
    }

    alterarSenha.mutate(formSenha)
  }

  const f  = (campo, valor) => setForm((p) => ({ ...p, [campo]: valor }))
  const fe = (campo, valor) => setForm((p) => ({ ...p, endereco: { ...p.endereco, [campo]: valor } }))

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="w-6 h-6 border-2 border-brand border-t-transparent rounded-full animate-spin" />
      </div>
    )
  }

  return (
    <div className="max-w-3xl mx-auto space-y-6">
      <div>
        <h1 className="text-xl font-semibold text-slate-800">Meu perfil</h1>
        <p className="text-sm text-slate-400 mt-0.5">Atualize seus dados pessoais e altere sua senha</p>
      </div>

      {/* Identificação (somente leitura) */}
      <div className="card">
        <div className="flex items-center gap-4">
          <div className="w-14 h-14 rounded-full bg-brand flex items-center justify-center shrink-0">
            <UserCircle size={28} className="text-white" />
          </div>
          <div className="flex-1 min-w-0">
            <p className="text-base font-semibold text-slate-800 truncate">{perfil?.nome}</p>
            <p className="text-xs text-slate-400">
              {perfil?.funcao === 'medico' ? 'Médico(a)' : perfil?.funcao}
              {perfil?.medico && (
                <> · CRM {perfil.medico.crm}-{perfil.medico.uf_crm} · {perfil.medico.especialidade}</>
              )}
            </p>
          </div>
        </div>
      </div>

      {/* Dados profissionais (somente para médicos, somente leitura) */}
      {perfil?.medico && (
        <div className="card space-y-4">
          <div className="flex items-center gap-2">
            <Stethoscope size={16} className="text-slate-400" />
            <h2 className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
              Dados profissionais
            </h2>
          </div>
          <p className="text-xs text-slate-400">
            Para alterar essas informações, entre em contato com a administração.
          </p>
          <div className="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            <div>
              <p className="text-xs text-slate-400 mb-0.5">CRM</p>
              <p className="text-slate-700">{perfil.medico.crm}-{perfil.medico.uf_crm}</p>
            </div>
            <div>
              <p className="text-xs text-slate-400 mb-0.5">Tipo</p>
              <p className="text-slate-700">{perfil.medico.tipo || '—'}</p>
            </div>
            <div>
              <p className="text-xs text-slate-400 mb-0.5">Especialidade</p>
              <p className="text-slate-700">{perfil.medico.especialidade || '—'}</p>
            </div>
            <div>
              <p className="text-xs text-slate-400 mb-0.5">Sub-especialidade</p>
              <p className="text-slate-700">{perfil.medico.sub_especialidade || '—'}</p>
            </div>
            <div>
              <p className="text-xs text-slate-400 mb-0.5">Data de contratação</p>
              <p className="text-slate-700">{formatarData(perfil.medico.data_contratacao)}</p>
            </div>
            <div>
              <p className="text-xs text-slate-400 mb-0.5">Status</p>
              <p className="text-slate-700">{perfil.medico.status === 'A' ? 'Ativo' : 'Inativo'}</p>
            </div>
          </div>
        </div>
      )}

      {/* Dados pessoais (editáveis) */}
      {perfil?.pessoa && (
        <form onSubmit={handleSubmitDados} className="card space-y-4">
          <h2 className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Dados pessoais
          </h2>

          <div className="grid grid-cols-2 gap-4">
            <Campo label="Nome completo" somenteLeitura className="col-span-2">
              <input value={perfil.pessoa.nome || ''} disabled className="input bg-slate-50 cursor-not-allowed" />
            </Campo>

            <Campo label="CPF" somenteLeitura>
              <input value={mascararCpf(perfil.pessoa.cpf || '')} disabled className="input bg-slate-50 cursor-not-allowed" />
            </Campo>

            <Campo label="Data de nascimento">
              <input type="date" value={form.data_nascimento}
                onChange={(e) => f('data_nascimento', e.target.value)} className="input" />
            </Campo>

            <Campo label="E-mail">
              <input required type="email" value={form.email}
                onChange={(e) => f('email', e.target.value)}
                placeholder="seu@email.com" className="input" />
            </Campo>

            <Campo label="Telefone">
              <InputMascarado mascara="telefone" value={form.telefone}
                onChange={(v) => f('telefone', v)} placeholder="(48) 99999-9999" />
            </Campo>
          </div>

          {/* Endereço */}
          <div className="pt-2 mt-2 border-t border-slate-100">
            <p className="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">
              Endereço
            </p>
            <div className="grid grid-cols-3 gap-4">
              <Campo label="CEP">
                <InputMascarado mascara="cep" value={form.endereco.cep}
                  onChange={(v) => fe('cep', v)} placeholder="00000-000" />
              </Campo>
              <Campo label="Logradouro" className="col-span-2">
                <input value={form.endereco.logradouro}
                  onChange={(e) => fe('logradouro', e.target.value)}
                  placeholder="Rua das Flores" className="input" />
              </Campo>
              <Campo label="Número">
                <input value={form.endereco.numero}
                  onChange={(e) => fe('numero', e.target.value)}
                  placeholder="123" className="input" />
              </Campo>
              <Campo label="Cidade">
                <input value={form.endereco.cidade}
                  onChange={(e) => fe('cidade', e.target.value)}
                  placeholder="Tubarão" className="input" />
              </Campo>
              <Campo label="Estado">
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

          <div className="flex justify-end">
            <button type="submit" disabled={atualizar.isPending}
              className="btn-primary flex items-center gap-2">
              {atualizar.isPending
                ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                : <><Save size={15} /> Salvar alterações</>
              }
            </button>
          </div>
        </form>
      )}

      {/* Alterar senha */}
      <form onSubmit={handleSubmitSenha} className="card space-y-4">
        <div className="flex items-center gap-2">
          <Lock size={16} className="text-slate-400" />
          <h2 className="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            Alterar senha
          </h2>
        </div>
        <p className="text-xs text-slate-400">
          A nova senha deve ter ao menos 8 caracteres, com uma letra e um número.
        </p>

        <div className="grid grid-cols-2 gap-4">
          <Campo label="Senha atual" className="col-span-2">
            <input required type="password" value={formSenha.senha_atual}
              onChange={(e) => setFormSenha({ ...formSenha, senha_atual: e.target.value })}
              className="input" />
          </Campo>
          <Campo label="Nova senha">
            <input required type="password" minLength={8} value={formSenha.nova_senha}
              onChange={(e) => setFormSenha({ ...formSenha, nova_senha: e.target.value })}
              placeholder="Mínimo 8 caracteres" className="input" />
          </Campo>
          <Campo label="Confirme a nova senha">
            <input required type="password" value={formSenha.nova_senha_confirmation}
              onChange={(e) => setFormSenha({ ...formSenha, nova_senha_confirmation: e.target.value })}
              placeholder="Repita a nova senha" className="input" />
          </Campo>
        </div>

        {erroSenha && (
          <div className="text-sm bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-start gap-2">
            <AlertCircle size={18} className="text-red-500 shrink-0 mt-0.5" />
            <p className="text-red-700">{erroSenha}</p>
          </div>
        )}

        <div className="flex justify-end">
          <button type="submit" disabled={alterarSenha.isPending}
            className="btn-primary flex items-center gap-2">
            {alterarSenha.isPending
              ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
              : <><Lock size={14} /> Alterar senha</>
            }
          </button>
        </div>
      </form>
    </div>
  )
}
