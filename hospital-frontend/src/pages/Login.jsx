import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { ShieldPlus } from 'lucide-react'
import { useAuth } from '../contexts/AuthContext'

export default function Login() {
  const { login } = useAuth()
  const navigate = useNavigate()
  const [form, setForm]     = useState({ email: '', senha: '' })
  const [error, setError]   = useState('')
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    setLoading(true)
    try {
      const data = await login(form.email, form.senha)
      if (data.primeiro_acesso) {
        navigate('/alterar-senha', { state: { email: form.email, senhaAtual: form.senha } })
      } else {
        navigate('/')
      }
    } catch (err) {
      setError(err.response?.data?.mensagem || 'E-mail ou senha incorretos.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-sidebar flex items-center justify-center p-4">
      <div className="w-full max-w-sm">
        {/* Logo */}
        <div className="flex items-center gap-3 mb-8">
          <div className="w-10 h-10 rounded-xl bg-brand flex items-center justify-center">
            <ShieldPlus size={20} className="text-white" />
          </div>
          <div>
            <p className="text-white text-lg font-semibold leading-none">Saúde+Vc</p>
            <p className="text-white/40 text-xs mt-0.5">Sistema Hospitalar</p>
          </div>
        </div>

        {/* Card */}
        <div className="bg-white rounded-2xl p-8 shadow-2xl">
          <h1 className="text-xl font-semibold text-slate-800 mb-1">Bem-vindo</h1>
          <p className="text-sm text-slate-400 mb-6">Entre com suas credenciais para acessar o sistema.</p>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">E-mail</label>
              <input type="email" required autoFocus
                value={form.email}
                onChange={(e) => setForm({ ...form, email: e.target.value })}
                placeholder="seu@email.com"
                className="input" />
            </div>

            <div>
              <label className="block text-xs font-medium text-slate-600 mb-1.5">Senha</label>
              <input type="password" required
                value={form.senha}
                onChange={(e) => setForm({ ...form, senha: e.target.value })}
                placeholder="••••••••"
                className="input" />
            </div>

            {error && (
              <p className="text-xs text-red-500 bg-red-50 border border-red-100 rounded-lg px-3 py-2">
                {error}
              </p>
            )}

            <button type="submit" disabled={loading}
              className="btn-primary w-full py-2.5 flex items-center justify-center gap-2">
              {loading ? (
                <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
              ) : 'Entrar'}
            </button>
          </form>
        </div>

        <p className="text-center text-white/20 text-xs mt-6">© 2026 IFSC — Uso interno</p>
      </div>
    </div>
  )
}
