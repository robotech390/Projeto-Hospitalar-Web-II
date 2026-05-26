import { useState } from 'react'
import { Link, useNavigate, useSearchParams } from 'react-router-dom'
import { ArrowLeft } from 'lucide-react'
import { authApi } from '../api/auth'
import { senhaForte } from '../utils/validadores'
import { useToast } from '../contexts/ToastContext'
import AuthLayout from '../components/layout/AuthLayout'

export default function RedefinirSenha() {
  const [params]    = useSearchParams()
  const navigate    = useNavigate()
  const { mostrar } = useToast()

  const tokenUrl = params.get('token') || ''
  const emailUrl = params.get('email') || ''

  const [form, setForm] = useState({ nova_senha: '', nova_senha_confirmation: '' })
  const [erro, setErro] = useState('')
  const [loading, setLoading] = useState(false)

  if (!tokenUrl || !emailUrl) {
    return (
      <AuthLayout etiqueta="Redefinição de senha">
        <h1 className="text-2xl font-semibold text-slate-800 mb-2">Link inválido</h1>
        <p className="text-sm text-slate-500 mb-6">
          O link de redefinição é inválido ou está incompleto. Solicite um novo no formulário "Esqueci minha senha".
        </p>
        <Link to="/esqueci-senha" className="btn-primary w-full block text-center">
          Solicitar novo link
        </Link>
      </AuthLayout>
    )
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErro('')

    if (!senhaForte(form.nova_senha)) {
      setErro('A senha deve ter ao menos 8 caracteres, uma letra e um número.')
      return
    }
    if (form.nova_senha !== form.nova_senha_confirmation) {
      setErro('As senhas não conferem.')
      return
    }

    setLoading(true)
    try {
      await authApi.redefinirSenha({
        email: emailUrl,
        token: tokenUrl,
        nova_senha:              form.nova_senha,
        nova_senha_confirmation: form.nova_senha_confirmation,
      })
      mostrar('Senha redefinida! Faça login com a nova senha.', 'sucesso')
      navigate('/login')
    } catch (err) {
      setErro(err.mensagemAmigavel || 'Não foi possível redefinir sua senha. O link pode ter expirado.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout etiqueta="Nova senha">
      <h1 className="text-2xl font-semibold text-slate-800 mb-1">Redefinir senha</h1>
      <p className="text-sm text-slate-400 mb-7">
        Para <strong className="text-slate-700">{emailUrl}</strong>. Crie uma senha com ao menos 8 caracteres,
        uma letra e um número.
      </p>

      <form onSubmit={handleSubmit} className="space-y-4">
        <div>
          <label className="block text-xs font-medium text-slate-600 mb-1.5">Nova senha</label>
          <input type="password" required autoFocus minLength={8}
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

        {erro && (
          <p className="text-xs text-red-500 bg-red-50 border border-red-100 rounded-lg px-3 py-2">{erro}</p>
        )}

        <button type="submit" disabled={loading}
          className="btn-primary w-full py-2.5 flex items-center justify-center">
          {loading
            ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
            : 'Salvar nova senha'}
        </button>
      </form>

      <Link to="/login"
        className="mt-6 text-sm text-slate-500 hover:text-brand flex items-center gap-1.5 justify-center transition-colors">
        <ArrowLeft size={14} /> Voltar para o login
      </Link>
    </AuthLayout>
  )
}
