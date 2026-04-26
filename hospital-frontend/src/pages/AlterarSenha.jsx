import { useState } from 'react'
import { useLocation, useNavigate } from 'react-router-dom'
import { ShieldPlus } from 'lucide-react'
import { authApi } from '../api/auth'

export default function AlterarSenha() {
  const { state } = useLocation()
  const navigate  = useNavigate()
  const [form, setForm]     = useState({ nova_senha: '', nova_senha_confirmation: '' })
  const [error, setError]   = useState('')
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (form.nova_senha !== form.nova_senha_confirmation) {
      setError('As senhas não conferem.')
      return
    }
    setLoading(true)
    try {
      await authApi.alterarSenhaPrimeiroAcesso({
        email:                   state?.email,
        senha_atual:             state?.senhaAtual,
        nova_senha:              form.nova_senha,
        nova_senha_confirmation: form.nova_senha_confirmation,
      })
      navigate('/')
    } catch (err) {
      setError(err.response?.data?.mensagem || 'Erro ao alterar senha.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-sidebar flex items-center justify-center p-4">
      <div className="w-full max-w-sm">
        <div className="flex items-center gap-3 mb-8">
          <div className="w-10 h-10 rounded-xl bg-brand flex items-center justify-center">
            <ShieldPlus size={20} className="text-white" />
          </div>
          <div>
            <p className="text-white text-lg font-semibold leading-none">Saúde+Vc</p>
            <p className="text-white/40 text-xs mt-0.5">Primeiro acesso</p>
          </div>
        </div>

        <div className="bg-white rounded-2xl p-8 shadow-2xl">
          <h1 className="text-xl font-semibold text-slate-800 mb-1">Defina sua senha</h1>
          <p className="text-sm text-slate-400 mb-6">
            Por segurança, crie uma senha pessoal para continuar.
          </p>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Nova senha</label>
              <input type="password" required minLength={8}
                value={form.nova_senha}
                onChange={(e) => setForm({ ...form, nova_senha: e.target.value })}
                placeholder="Mínimo 8 caracteres"
                className="input" />
            </div>
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Confirme a senha</label>
              <input type="password" required
                value={form.nova_senha_confirmation}
                onChange={(e) => setForm({ ...form, nova_senha_confirmation: e.target.value })}
                placeholder="Repita a nova senha"
                className="input" />
            </div>

            {error && (
              <p className="text-xs text-red-500 bg-red-50 border border-red-100 rounded-lg px-3 py-2">{error}</p>
            )}

            <button type="submit" disabled={loading}
              className="btn-primary w-full py-2.5 flex items-center justify-center">
              {loading
                ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
                : 'Salvar e entrar'}
            </button>
          </form>
        </div>
      </div>
    </div>
  )
}
