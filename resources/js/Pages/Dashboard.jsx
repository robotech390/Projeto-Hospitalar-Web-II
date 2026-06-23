import { Users, Calendar, Pill, DollarSign, Clock } from 'lucide-react';
import { useState, useEffect } from 'react';

export default function Dashboard() {
  const [pacientesHoje, setPacientesHoje] = useState([]);
  const [carregando, setCarregando] = useState(true);
  const [erro, setErro] = useState(null);

  useEffect(() => {
    const carregarPacientes = async () => {
      try {
        setCarregando(true);
        const response = await fetch('/consultas/api/pacientes-hoje', {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
        });

        if (!response.ok) {
          throw new Error(`Erro: ${response.status}`);
        }

        const data = await response.json();
        if (data.success && data.data) {
          setPacientesHoje(data.data);
        }
      } catch (err) {
        console.error('Erro ao carregar pacientes:', err);
        setErro(err.message);
      } finally {
        setCarregando(false);
      }
    };

    carregarPacientes();
  }, []);

  const getStatusBadge = (status) => {
    const statusMap = {
      'em_consulta': { bg: 'bg-gray-100', text: 'text-gray-600', label: 'Em Consulta' },
      'aguardando': { bg: 'bg-orange-50', text: 'text-orange-600', label: 'Aguardando' },
      'concluida': { bg: 'bg-emerald-50', text: 'text-emerald-600', label: 'Concluída' },
      'cancelada': { bg: 'bg-red-50', text: 'text-red-600', label: 'Cancelada' },
    };
    
    const mapped = statusMap[status?.toLowerCase()] || { bg: 'bg-gray-100', text: 'text-gray-600', label: status };
    return mapped;
  };

  const getCorAvatarColor = (index) => {
    const cores = ['bg-brand', 'bg-brand-light', 'bg-brand-dark', 'bg-blue-500', 'bg-purple-500', 'bg-pink-500'];
    return cores[index % cores.length];
  };

  return (
    <div className="animate-fade-in">
      {/* Título e Data */}
      <div className="mb-8">
        <h1 className="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p className="text-sm text-gray-500">Visão geral do hospital — {new Date().toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
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
          <h3 className="text-3xl font-bold text-gray-800">{pacientesHoje.length}</h3>
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

        {carregando ? (
          <div className="flex justify-center items-center py-8">
            <p className="text-gray-500">Carregando pacientes...</p>
          </div>
        ) : erro ? (
          <div className="flex justify-center items-center py-8">
            <p className="text-red-500">Erro ao carregar: {erro}</p>
          </div>
        ) : pacientesHoje.length === 0 ? (
          <div className="flex justify-center items-center py-8">
            <p className="text-gray-500">Nenhum paciente agendado para hoje</p>
          </div>
        ) : (
          <div className="space-y-0">
            {pacientesHoje.map((paciente, index) => {
              const statusInfo = getStatusBadge(paciente.status);
              return (
                <div key={paciente.id} className="flex items-center justify-between py-4 border-b border-gray-50 last:border-0">
                  <div className="flex items-center space-x-3">
                    <div className={`w-9 h-9 rounded-full ${getCorAvatarColor(index)} text-white flex items-center justify-center font-bold text-xs`}>
                      {paciente.paciente_iniciais}
                    </div>
                    <div>
                      <p className="font-bold text-gray-800 text-sm">{paciente.paciente_nome}</p>
                      <p className="text-xs text-gray-500">Dr(a). {paciente.medico_nome}</p>
                    </div>
                  </div>
                  <div className="flex items-center space-x-4">
                    <span className="text-sm font-medium text-gray-500">{paciente.hora}</span>
                    <span className={`text-xs font-semibold px-3 py-1.5 rounded-md ${statusInfo.bg} ${statusInfo.text} w-28 text-center`}>
                      {statusInfo.label}
                    </span>
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>
    </div>
  );
}