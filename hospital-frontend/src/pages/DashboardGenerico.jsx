import { ShieldPlus } from 'lucide-react'
import { useAuth } from '../contexts/AuthContext'

/**
 * Dashboard de boas-vindas para funções sem painel próprio
 * (farmacêutico, recepcionista, paciente).
 */
export default function DashboardGenerico() {
  const { user } = useAuth()
  const primeiroNome = user?.nome?.split(' ')[0] || 'Usuário'

  return (
    <div className="max-w-2xl mx-auto py-16 text-center">
      <div className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand/10 mb-4">
        <ShieldPlus size={32} className="text-brand" />
      </div>
      <h1 className="text-2xl font-semibold text-slate-800 mb-2">
        Bem-vindo(a), {primeiroNome}
      </h1>
      <p className="text-slate-500 mb-6 max-w-md mx-auto">
        Este é o sistema Saúde+Vc. Os módulos específicos para o seu perfil
        ({user?.funcao}) estão em desenvolvimento por outras equipes do projeto.
      </p>
      <p className="text-xs text-slate-400">
        Use o menu lateral para acessar o que está disponível ao seu perfil.
      </p>
    </div>
  )
}
