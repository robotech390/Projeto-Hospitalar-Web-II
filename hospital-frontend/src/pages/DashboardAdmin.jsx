import { Users, CalendarDays, Pill, DollarSign, Clock } from 'lucide-react'
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, AreaChart, Area } from 'recharts'

const STATS = [
  { label: 'Pacientes Hoje',            value: '47',       change: '+12%', up: true,  icon: Users       },
  { label: 'Consultas Agendadas',       value: '128',      change: '+8%',  up: true,  icon: CalendarDays},
  { label: 'Medicamentos Dispensados',  value: '342',      change: '-3%',  up: false, icon: Pill        },
  { label: 'Receita do Dia',            value: 'R$ 45.2k', change: '+15%', up: true,  icon: DollarSign  },
]

const SEMANA = [
  { day: 'Seg', consultas: 30, exames: 12 },
  { day: 'Ter', consultas: 45, exames: 18 },
  { day: 'Qua', consultas: 35, exames: 15 },
  { day: 'Qui', consultas: 50, exames: 20 },
  { day: 'Sex', consultas: 40, exames: 22 },
  { day: 'Sáb', consultas: 15, exames: 8  },
  { day: 'Dom', consultas: 8,  exames: 3  },
]

const MES = [
  { mes: 'Jan', receita: 82000 },
  { mes: 'Fev', receita: 95000 },
  { mes: 'Mar', receita: 110000 },
  { mes: 'Abr', receita: 130000 },
  { mes: 'Mai', receita: 125000 },
  { mes: 'Jun', receita: 140000 },
]

const PACIENTES = [
  { iniciais: 'MS', nome: 'Maria Silva',  medico: 'Dr. Carlos',  horario: '08:30', status: 'consulting' },
  { iniciais: 'JS', nome: 'João Santos',  medico: 'Dra. Ana',    horario: '09:00', status: 'waiting'    },
  { iniciais: 'AO', nome: 'Ana Oliveira', medico: 'Dr. Roberto', horario: '09:30', status: 'waiting'    },
  { iniciais: 'PC', nome: 'Pedro Costa',  medico: 'Dra. Julia',  horario: '10:00', status: 'checkin'    },
  { iniciais: 'LM', nome: 'Laura Mendes', medico: 'Dr. Carlos',  horario: '10:30', status: 'scheduled'  },
]

const STATUS_LABELS = {
  consulting: { label: 'Em Consulta', classe: 'bg-green-100 text-green-700' },
  waiting:    { label: 'Aguardando',  classe: 'bg-orange-100 text-orange-700' },
  checkin:    { label: 'Check-in',    classe: 'bg-teal-100 text-teal-700' },
  scheduled:  { label: 'Agendado',    classe: 'bg-slate-100 text-slate-600' },
}

const CORES_AVATAR = ['bg-teal-500', 'bg-blue-500', 'bg-purple-500', 'bg-amber-500', 'bg-rose-500']

function CardEstatistica({ label, value, change, up, icon: Icon }) {
  return (
    <div className="card">
      <div className="flex items-start justify-between mb-3">
        <div className="p-2 rounded-lg bg-slate-50">
          <Icon size={18} className="text-slate-500" />
        </div>
        <span className={`text-xs font-medium ${up ? 'text-green-600' : 'text-red-500'}`}>{change}</span>
      </div>
      <p className="text-2xl font-semibold text-slate-800">{value}</p>
      <p className="text-xs text-slate-400 mt-0.5">{label}</p>
    </div>
  )
}

export default function DashboardAdmin() {
  const hoje = new Date().toLocaleDateString('pt-BR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-xl font-semibold text-slate-800">Dashboard</h1>
        <p className="text-sm text-slate-400 mt-0.5">Visão geral do hospital — {hoje}</p>
      </div>

      <div className="grid grid-cols-2 xl:grid-cols-4 gap-4">
        {STATS.map((s) => <CardEstatistica key={s.label} {...s} />)}
      </div>

      <div className="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div className="card">
          <div className="mb-4">
            <p className="text-sm font-medium text-slate-700">Consultas & Exames</p>
            <p className="text-xs text-slate-400">Últimos 7 dias</p>
          </div>
          <ResponsiveContainer width="100%" height={180}>
            <BarChart data={SEMANA} barSize={10} barGap={4}>
              <XAxis dataKey="day" tick={{ fontSize: 11, fill: '#94a3b8' }} axisLine={false} tickLine={false} />
              <YAxis tick={{ fontSize: 11, fill: '#94a3b8' }} axisLine={false} tickLine={false} />
              <Tooltip contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 20px rgba(0,0,0,0.1)', fontSize: '12px' }} />
              <Bar dataKey="consultas" fill="#0f3d38" radius={[4, 4, 0, 0]} />
              <Bar dataKey="exames"    fill="#2a9d8f" radius={[4, 4, 0, 0]} />
            </BarChart>
          </ResponsiveContainer>
        </div>

        <div className="card">
          <div className="mb-4">
            <p className="text-sm font-medium text-slate-700">Receita Mensal</p>
            <p className="text-xs text-slate-400">Últimos 6 meses</p>
          </div>
          <ResponsiveContainer width="100%" height={180}>
            <AreaChart data={MES}>
              <defs>
                <linearGradient id="grad" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="5%"  stopColor="#2a9d8f" stopOpacity={0.15} />
                  <stop offset="95%" stopColor="#2a9d8f" stopOpacity={0} />
                </linearGradient>
              </defs>
              <XAxis dataKey="mes" tick={{ fontSize: 11, fill: '#94a3b8' }} axisLine={false} tickLine={false} />
              <YAxis tick={{ fontSize: 11, fill: '#94a3b8' }} axisLine={false} tickLine={false}
                tickFormatter={(v) => `${v / 1000}k`} />
              <Tooltip contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 20px rgba(0,0,0,0.1)', fontSize: '12px' }}
                formatter={(v) => [`R$ ${(v / 1000).toFixed(1)}k`, 'Receita']} />
              <Area type="monotone" dataKey="receita" stroke="#2a9d8f" strokeWidth={2} fill="url(#grad)" />
            </AreaChart>
          </ResponsiveContainer>
        </div>
      </div>

      <div className="card">
        <div className="flex items-center justify-between mb-4">
          <div>
            <p className="text-sm font-medium text-slate-700">Pacientes de Hoje</p>
            <p className="text-xs text-slate-400">Próximos atendimentos</p>
          </div>
          <Clock size={16} className="text-slate-400" />
        </div>

        <div className="space-y-1">
          {PACIENTES.map((p, i) => {
            const st = STATUS_LABELS[p.status]
            return (
              <div key={i} className="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-50 transition-colors">
                <div className={`w-8 h-8 rounded-full ${CORES_AVATAR[i % CORES_AVATAR.length]} flex items-center justify-center shrink-0`}>
                  <span className="text-white text-xs font-semibold">{p.iniciais}</span>
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-slate-800 truncate">{p.nome}</p>
                  <p className="text-xs text-slate-400">{p.medico}</p>
                </div>
                <p className="text-sm text-slate-500 font-medium shrink-0">{p.horario}</p>
                <span className={`text-xs font-medium px-2 py-0.5 rounded-md shrink-0 ${st.classe}`}>
                  {st.label}
                </span>
              </div>
            )
          })}
        </div>
      </div>
    </div>
  )
}
