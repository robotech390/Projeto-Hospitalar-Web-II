import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Plus, Trash2, Search, Pencil, MailOpen } from 'lucide-react'
import { usuariosApi } from '../api/usuarios'
import Badge from '../components/ui/Badge'
import Tooltip from '../components/ui/Tooltip'
import DialogoConfirmacao from '../components/ui/DialogoConfirmacao'
import { useToast } from '../contexts/ToastContext'

const FUNCOES = ['administrador', 'medico', 'farmaceutico', 'recepcionista', 'paciente']

const FUNCAO_LABELS = {
  administrador: 'Admin',
  medico:        'Médico',
  farmaceutico:  'Farmacêutico',
  recepcionista: 'Recepcionista',
  paciente:      'Paciente',
}

const CORES_AVATAR = ['bg-teal-500', 'bg-blue-500', 'bg-purple-500', 'bg-amber-500', 'bg-rose-500', 'bg-indigo-500']

export default function Usuarios() {
  const qc       = useQueryClient()
  const navigate = useNavigate()
  const { mostrar } = useToast()

  const [busca, setBusca]   = useState('')
  const [funcao, setFuncao] = useState('')
  const [confirmar, setConfirmar] = useState(null) // { usuario, acao: 'remover'|'reenviar' }

  const { data: usuarios = [], isLoading } = useQuery({
    queryKey: ['usuarios', funcao],
    queryFn:  () => usuariosApi.listar(funcao || undefined).then((r) => r.data),
  })

  const remover = useMutation({
    mutationFn: (id) => usuariosApi.remover(id),
    onSuccess: () => {
      qc.invalidateQueries(['usuarios'])
      mostrar('Usuário removido.', 'sucesso')
    },
    onError: (err) => mostrar(err.mensagemAmigavel || 'Não foi possível remover o usuário.', 'erro'),
  })

  const reenviar = useMutation({
    mutationFn: (id) => usuariosApi.reenviarSenha(id),
    onSuccess: (resp) => {
      qc.invalidateQueries(['usuarios'])
      mostrar(resp.data?.mensagem || 'Nova senha temporária enviada por e-mail.', 'sucesso')
    },
    onError: (err) => mostrar(err.mensagemAmigavel || 'Não foi possível reenviar a senha.', 'erro'),
  })

  const usuariosFiltrados = usuarios.filter((u) =>
    u.usuario?.toLowerCase().includes(busca.toLowerCase()) ||
    u.email?.toLowerCase().includes(busca.toLowerCase())
  )

  return (
    <div className="space-y-5">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold text-slate-800">Acesso & Usuários</h1>
          <p className="text-sm text-slate-400 mt-0.5">Gerenciamento de usuários do sistema</p>
        </div>
        <Tooltip text="Cadastrar novo usuário">
          <button onClick={() => navigate('/usuarios/novo')} className="btn-primary flex items-center gap-1.5">
            <Plus size={15} /> Novo Usuário
          </button>
        </Tooltip>
      </div>

      <div className="flex gap-3 flex-wrap">
        <div className="relative flex-1 max-w-xs">
          <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
          <input value={busca} onChange={(e) => setBusca(e.target.value)}
            placeholder="Buscar usuário..." className="input pl-8" />
        </div>
        <select value={funcao} onChange={(e) => setFuncao(e.target.value)} className="input w-44">
          <option value="">Todas as funções</option>
          {FUNCOES.map((f) => <option key={f} value={f}>{FUNCAO_LABELS[f]}</option>)}
        </select>
      </div>

      <div className="card p-0 overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-slate-100">
              <th className="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Usuário</th>
              <th className="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">E-mail</th>
              <th className="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Função</th>
              <th className="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
              <th className="text-right px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-50">
            {isLoading ? (
              <tr><td colSpan={5} className="text-center py-10 text-slate-400 text-sm">Carregando...</td></tr>
            ) : usuariosFiltrados.length === 0 ? (
              <tr><td colSpan={5} className="text-center py-10 text-slate-400 text-sm">Nenhum usuário encontrado.</td></tr>
            ) : usuariosFiltrados.map((u, i) => (
              <tr key={u.id} className="hover:bg-slate-50/50 transition-colors">
                <td className="px-5 py-3">
                  <div className="flex items-center gap-2.5">
                    <div className={`w-7 h-7 rounded-full ${CORES_AVATAR[i % CORES_AVATAR.length]} flex items-center justify-center shrink-0`}>
                      <span className="text-white text-[10px] font-semibold">
                        {u.usuario?.split(' ').slice(0, 2).map((n) => n[0]).join('').toUpperCase()}
                      </span>
                    </div>
                    <span className="font-medium text-slate-700">{u.usuario}</span>
                  </div>
                </td>
                <td className="px-5 py-3 text-slate-500">{u.email}</td>
                <td className="px-5 py-3">
                  <Badge variant={u.funcao}>{FUNCAO_LABELS[u.funcao] || u.funcao}</Badge>
                </td>
                <td className="px-5 py-3">
                  <Badge variant={u.primeiro_acesso ? 'waiting' : 'active'}>
                    {u.primeiro_acesso ? 'Senha pendente' : 'Ativo'}
                  </Badge>
                </td>
                <td className="px-5 py-3">
                  <div className="flex items-center justify-end gap-1">
                    <Tooltip text="Reenviar senha por e-mail">
                      <button onClick={() => setConfirmar({ usuario: u, acao: 'reenviar' })}
                        className="p-1.5 rounded-lg hover:bg-teal-50 hover:text-teal-600 text-slate-400 transition-colors">
                        <MailOpen size={15} />
                      </button>
                    </Tooltip>
                    <Tooltip text="Editar usuário">
                      <button onClick={() => navigate(`/usuarios/${u.id}/editar`)}
                        className="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                        <Pencil size={15} />
                      </button>
                    </Tooltip>
                    <Tooltip text="Remover usuário">
                      <button onClick={() => setConfirmar({ usuario: u, acao: 'remover' })}
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
        titulo={
          confirmar?.acao === 'remover' ? 'Remover usuário?'
          : confirmar?.acao === 'reenviar' ? 'Reenviar senha por e-mail?'
          : ''
        }
        mensagem={
          confirmar?.acao === 'remover'
            ? `Tem certeza que deseja remover ${confirmar?.usuario?.usuario}? Esta ação não pode ser desfeita.`
            : `Uma nova senha temporária será enviada para ${confirmar?.usuario?.email} e o usuário precisará trocá-la no próximo login.`
        }
        textoConfirmar={confirmar?.acao === 'remover' ? 'Remover' : 'Reenviar'}
        variante={confirmar?.acao === 'remover' ? 'perigo' : 'aviso'}
        onCancelar={() => setConfirmar(null)}
        onConfirmar={() => {
          if (confirmar.acao === 'remover')  remover.mutate(confirmar.usuario.id)
          if (confirmar.acao === 'reenviar') reenviar.mutate(confirmar.usuario.id)
          setConfirmar(null)
        }}
      />
    </div>
  )
}
