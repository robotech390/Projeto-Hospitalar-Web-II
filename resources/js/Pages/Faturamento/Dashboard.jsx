import { Users, Calendar, Pill, DollarSign, Clock } from 'lucide-react';

export default function Dashboard() {
  return (
    <div className="animate-fade-in">
      {/* Título e Data */}
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p className="text-sm text-gray-500">Visão geral do hospital — terça-feira, 3 de março de 2026</p>
      </div>

      {/* Cartões de Métricas */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {/* Cartão 1 */}
        <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
          <div className="flex justify-between items-start mb-4">
            <div className="p-2 bg-brand/10 text-brand rounded-lg">
              <Users size={20} />
            </div>
            <span className="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+12%</span>
          </div>
          <h3 className="text-3xl font-bold text-gray-800">47</h3>
          <p className="text-sm text-gray-500 font-medium mt-1">Pacientes Hoje</p>
        </div>

        {/* Cartão 2 */}
        <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
          <div className="flex justify-between items-start mb-4">
            <div className="p-2 bg-blue-50 text-blue-500 rounded-lg">
              <Calendar size={20} />
            </div>
            <span className="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+8%</span>
          </div>
          <h3 className="text-3xl font-bold text-gray-800">128</h3>
          <p className="text-sm text-gray-500 font-medium mt-1">Consultas Agendadas</p>
        </div>

        {/* Cartão 3 */}
        <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
          <div className="flex justify-between items-start mb-4">
            <div className="p-2 bg-orange-50 text-orange-500 rounded-lg">
              <Pill size={20} />
            </div>
            <span className="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full">-3%</span>
          </div>
          <h3 className="text-3xl font-bold text-gray-800">342</h3>
          <p className="text-sm text-gray-500 font-medium mt-1">Medicamentos Dispensados</p>
        </div>

        {/* Cartão 4 */}
        <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
          <div className="flex justify-between items-start mb-4">
            <div className="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
              <DollarSign size={20} />
            </div>
            <span className="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">+15%</span>
          </div>
          <h3 className="text-3xl font-bold text-gray-800">R$ 45.2k</h3>
          <p className="text-sm text-gray-500 font-medium mt-1">Receita do Dia</p>
        </div>
      </div>

      {/* Áreas dos Gráficos (Espaço Reservado) */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-64 flex flex-col justify-between">
          <div>
            <h3 className="text-sm font-bold text-gray-800">Consultas & Exames</h3>
            <p className="text-xs text-gray-500">Últimos 7 dias</p>
          </div>
          <div className="flex-1 flex items-center justify-center text-gray-400 text-sm border-2 border-dashed border-gray-100 mt-4 rounded-lg">
            [ Área do Gráfico de Barras ]
          </div>
        </div>
        <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-64 flex flex-col justify-between">
          <div>
            <h3 className="text-sm font-bold text-gray-800">Receita Mensal</h3>
            <p className="text-xs text-gray-500">Últimos 6 meses</p>
          </div>
          <div className="flex-1 flex items-center justify-center text-gray-400 text-sm border-2 border-dashed border-gray-100 mt-4 rounded-lg">
            [ Área do Gráfico de Linha ]
          </div>
        </div>
      </div>

      {/* Lista de Pacientes de Hoje */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div className="flex justify-between items-center mb-6">
          <div>
            <h3 className="text-sm font-bold text-gray-800">Pacientes de Hoje</h3>
            <p className="text-xs text-gray-500">Próximos atendimentos</p>
          </div>
          <Clock size={18} className="text-gray-400" />
        </div>

        <div className="space-y-0">
          {/* Paciente 1 */}
          <div className="flex items-center justify-between py-4 border-b border-gray-50 last:border-0">
            <div className="flex items-center space-x-3">
              <div className="w-9 h-9 rounded-full bg-brand text-white flex items-center justify-center font-bold text-xs">
                MS
              </div>
              <div>
                <p className="font-bold text-gray-800 text-sm">Maria Silva</p>
                <p className="text-xs text-gray-500">Dr. Carlos</p>
              </div>
            </div>
            <div className="flex items-center space-x-4">
              <span className="text-sm font-medium text-gray-500">08:30</span>
              <span className="text-xs font-semibold px-3 py-1.5 rounded-md bg-gray-100 text-gray-600 w-28 text-center">
                Em Consulta
              </span>
            </div>
          </div>

          {/* Paciente 2 */}
          <div className="flex items-center justify-between py-4 border-b border-gray-50 last:border-0">
            <div className="flex items-center space-x-3">
              <div className="w-9 h-9 rounded-full bg-brand-light text-white flex items-center justify-center font-bold text-xs">
                JS
              </div>
              <div>
                <p className="font-bold text-gray-800 text-sm">João Santos</p>
                <p className="text-xs text-gray-500">Dra. Ana</p>
              </div>
            </div>
            <div className="flex items-center space-x-4">
              <span className="text-sm font-medium text-gray-500">09:00</span>
              <span className="text-xs font-semibold px-3 py-1.5 rounded-md bg-orange-50 text-orange-600 w-28 text-center">
                Aguardando
              </span>
            </div>
          </div>

          {/* Paciente 3 */}
          <div className="flex items-center justify-between py-4 border-b border-gray-50 last:border-0">
            <div className="flex items-center space-x-3">
              <div className="w-9 h-9 rounded-full bg-brand-dark text-white flex items-center justify-center font-bold text-xs">
                AO
              </div>
              <div>
                <p className="font-bold text-gray-800 text-sm">Ana Oliveira</p>
                <p className="text-xs text-gray-500">Dr. Roberto</p>
              </div>
            </div>
            <div className="flex items-center space-x-4">
              <span className="text-sm font-medium text-gray-500">09:30</span>
              <span className="text-xs font-semibold px-3 py-1.5 rounded-md bg-orange-50 text-orange-600 w-28 text-center">
                Aguardando
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}