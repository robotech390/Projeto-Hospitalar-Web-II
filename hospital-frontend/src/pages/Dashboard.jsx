import { useAuth } from '../contexts/AuthContext'
import DashboardAdmin from './DashboardAdmin'
import DashboardMedico from './DashboardMedico'
import DashboardGenerico from './DashboardGenerico'

/**
 * Roteia o Dashboard conforme a função do usuário logado.
 * - Administrador → painel completo com indicadores do hospital
 * - Médico        → painel focado em plantões e atendimentos próprios
 * - Outros        → painel genérico de boas-vindas
 */
export default function Dashboard() {
  const { user } = useAuth()

  if (user?.funcao === 'administrador') return <DashboardAdmin />
  if (user?.funcao === 'medico')        return <DashboardMedico />
  return <DashboardGenerico />
}
