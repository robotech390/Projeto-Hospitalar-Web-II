import { useState } from 'react'
import { Link } from 'react-router-dom'
import { ArrowLeft, CheckCircle2 } from 'lucide-react'
import { authApi } from '../api/auth'
import { emailValido } from '../utils/validadores'
import { useToast } from '../contexts/ToastContext'
import AuthLayout from '../components/layout/AuthLayout'

export default function EsqueciSenha() {
  const { mostrar } = useToast()
  const [email, setEmail]     = useState('')
  const [enviado, setEnviado] = useState(false)
  const [erro, setErro]       = useState('')
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    setErro('')

    if (!emailValido(email)) {
      setErro('Informe um e-mail válido.')
      return
    }

    setLoading(true)
    try {
      await authApi.esqueciSenha(email)
      setEnviado(true)
      mostrar('Se o e-mail estiver cadastrado, você receberá o link em instantes.', 'sucesso')
    } catch (err) {
      setErro(err.mensagemAmigavel || 'Não foi possível processar sua solicitação.')
    } finally {
      setLoading(false)
    }
  }

  return (
    <AuthLayout etiqueta="Recuperação de acesso">
      <h1 className="text-2xl font-semibold text-slate-800 mb-1">Esqueci minha senha</h1>
      <p className="text-sm text-slate-400 mb-7">
        {enviado
          ? 'Verifique sua caixa de entrada. O link expira em 60 minutos.'
          : 'Informe seu e-mail cadastrado e enviaremos um link para criar uma nova senha.'}
      </p>

      {!enviado ? (
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-xs font-medium text-slate-600 mb-1.5">E-mail</label>
            <input type="email" required autoFocus
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="seu@email.com"
              className="input" />
          </div>

          {erro && (
            <p className="text-xs text-red-500 bg-red-50 border border-red-100 rounded-lg px-3 py-2">{erro}</p>
          )}

          <button type="submit" disabled={loading}
            className="btn-primary w-full py-2.5 flex items-center justify-center">
            {loading
              ? <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin" />
              : 'Enviar link de redefinição'}
          </button>
        </form>
      ) : (
        <div className="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800 flex items-start gap-2">
          <CheckCircle2 size={18} className="shrink-0 mt-0.5 text-green-600" />
          <p>Solicitação enviada. Acesse seu e-mail e siga o link recebido.</p>
        </div>
      )}

      <Link to="/login"
        className="mt-6 text-sm text-slate-500 hover:text-brand flex items-center gap-1.5 justify-center transition-colors">
        <ArrowLeft size={14} /> Voltar para o login
      </Link>
    </AuthLayout>
  )
}
