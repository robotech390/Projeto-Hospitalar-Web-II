import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Plus, Trash2, Search } from 'lucide-react'
import { usuariosApi } from '../api/usuarios'
import Modal from '../components/ui/Modal'
import Badge from '../components/ui/Badge'

const FUNCOES = ['administrador', 'medico', 'farmaceutico', 'recepcionista', 'paciente']

const FUNCAO_LABELS = {
  administrador: 'Admin', medico: 'Médico', farmaceutico: 'Farmacêutico',
  recepcionista: 'Recepcionista', paciente: 'Paciente',
}

const AVATAR_COLORS = ['bg-teal-500', 'bg-blue-500', 'bg-purple-500', 'bg-amber-500', 'bg-rose-500', 'bg-indigo-500']

export default function Usuarios() {
  const qc = useQueryClient()
  const [search, setSearch]   = useState('')
  const [funcao, setFuncao]   = useState('')
  const [modal, setModal]     = useState(false)
  const [form, setForm]       = useState({ nome: '', email: '', funcao: 'paciente', id_cadastro: '' })
  const [formError, setFormError] = useState('')

  const { data: usuarios = [], isLoading } = useQuery({
    queryKey: ['usuarios', funcao],
    queryFn:  () => usuariosApi.listar(funcao || undefined).then((r) => r.data),
  })

  const criar = useMutation({
    mutationFn: (data) => usuariosApi.registrar(data),
    onSuccess: () => { qc.invalidateQueries(['usuarios']); setModal(false); setForm({ nome: '', email: '', funcao: 'paciente', id_cadastro: '' }) },
    onError: (err) => setFormError(err.response?.data?.mensagem || 'Erro ao criar usuário.'),
  })

  const remover = useMutation({
    mutationFn: (id) => usuariosApi.remover(id),
    onSuccess: () => qc.invalidateQueries(['usuarios']),
  })

  const filtered = usuarios.filter((u) =>
    u.usuario?.toLowerCase().includes(search.toLowerCase()) ||
    u.email?.toLowerCase().includes(search.toLowerCase())
  )

  const handleSubmit = (e) => {
    e.preventDefault()
    setFormError('')
    criar.mutate({ ...form, id_cadastro: Number(form.id_cadastro) || 1 })
  }

  return (
    <div className="space-y-5">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-semibold text-slate-800">Acesso & Usuários</h1>
          <p className="text-sm text-slate-400 mt-0.5">Gerenciamento de usuários do sistema</p>
        </div>
        <button onClick={() => setModal(true)} className="btn-primary flex items-center gap-1.5">
          <Plus size={15} /> Novo Usuário
        </button>
      </div>

      {/* Filters */}
      <div className="flex gap-3">
        <div className="relative flex-1 max-w-xs">
          <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
          <input value={search} onChange={(e) => setSearch(e.target.value)}
            placeholder="Buscar usuário..." className="input pl-8" />
        </div>
        <select value={funcao} onChange={(e) => setFuncao(e.target.value)} className="input w-44">
          <option value="">Todas as funções</option>
          {FUNCOES.map((f) => <option key={f} value={f}>{FUNCAO_LABELS[f]}</option>)}
        </select>
      </div>

      {/* Table */}
      <div className="card p-0 overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-slate-100">
              <th className="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Usuário</th>
              <th className="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">E-mail</th>
              <th className="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Função</th>
              <th className="text-left px-5 py-3 text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
              <th className="px-5 py-3" />
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-50">
            {isLoading ? (
              <tr><td colSpan={5} className="text-center py-10 text-slate-400 text-sm">Carregando...</td></tr>
            ) : filtered.length === 0 ? (
              <tr><td colSpan={5} className="text-center py-10 text-slate-400 text-sm">Nenhum usuário encontrado.</td></tr>
            ) : filtered.map((u, i) => (
              <tr key={u.id} className="hover:bg-slate-50/50 transition-colors">
                <td className="px-5 py-3">
                  <div className="flex items-center gap-2.5">
                    <div className={`w-7 h-7 rounded-full ${AVATAR_COLORS[i % AVATAR_COLORS.length]} flex items-center justify-center shrink-0`}>
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
                    {u.primeiro_acesso ? 'Pendente' : 'Ativo'}
                  </Badge>
                </td>
                <td className="px-5 py-3 text-right">
                  <button onClick={() => { if (confirm('Remover usuário?')) remover.mutate(u.id) }}
                    className="p-1.5 rounded-lg hover:bg-red-50 hover:text-red-500 text-slate-400 transition-colors">
                    <Trash2 size={15} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Modal criar */}
      <Modal open={modal} onClose={() => setModal(false)} title="Novo Usuário">
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-2 gap-3">
            <div className="col-span-2">
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Nome completo</label>
              <input required value={form.nome} onChange={(e) => setForm({ ...form, nome: e.target.value })}
                placeholder="João da Silva" className="input" />
            </div>
            <div className="col-span-2">
              <label className="block text-xs font-medium text-slate-600 mb-1.5">E-mail</label>
              <input required type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })}
                placeholder="joao@email.com" className="input" />
            </div>
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Função</label>
              <select required value={form.funcao} onChange={(e) => setForm({ ...form, funcao: e.target.value })} className="input">
                {FUNCOES.map((f) => <option key={f} value={f}>{FUNCAO_LABELS[f]}</option>)}
              </select>
            </div>
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">ID do cadastro</label>
              <input required type="number" min="1" value={form.id_cadastro}
                onChange={(e) => setForm({ ...form, id_cadastro: e.target.value })}
                placeholder="Ex: 1" className="input" />
            </div>
          </div>

          {formError && <p className="text-xs text-red-500 bg-red-50 border border-red-100 rounded-lg px-3 py-2">{formError}</p>}

          <div className="flex justify-end gap-2 pt-1">
            <button type="button" onClick={() => setModal(false)} className="btn-secondary">Cancelar</button>
            <button type="submit" disabled={criar.isPending} className="btn-primary flex items-center gap-1.5">
              {criar.isPending
                ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                : 'Criar usuário'}
            </button>
          </div>
        </form>
      </Modal>
    </div>
  )
}
