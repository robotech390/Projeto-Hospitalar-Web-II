import { useState } from 'react';
import { 
  UserSquare2, 
  ClipboardType, 
  Pill, 
  Stethoscope, 
  History, 
  CheckCircle2, 
  Clock 
} from 'lucide-react';

export default function Prontuario() {
  // Controle das abas do prontuário
  const [abaAtiva, setAbaAtiva] = useState('consulta');

  // Fila de pacientes (Mock)
  const filaPacientes = [
    { id: 1, nome: 'Maria Silva', hora: '08:30', status: 'Em Atendimento', idade: 45 },
    { id: 2, nome: 'João Santos', hora: '09:00', status: 'Aguardando', idade: 32 },
    { id: 3, nome: 'Ana Oliveira', hora: '09:30', status: 'Aguardando', idade: 28 },
  ];

  return (
    <div className="flex h-full gap-6 animate-fade-in">
      
      {/* COLUNA ESQUERDA: PAINEL DO MÉDICO (Fila de Check-in) */}
      <div className="w-1/3 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-[calc(100vh-8rem)]">
        <div className="p-5 border-b border-gray-100">
          <h2 className="text-lg font-bold text-gray-800">Fila de Atendimento</h2>
          <p className="text-xs text-gray-500">Pacientes com check-in realizado</p>
        </div>
        
        <div className="overflow-y-auto flex-1 p-3 space-y-2">
          {filaPacientes.map((paciente, index) => (
            <div 
              key={paciente.id} 
              className={`p-4 rounded-lg cursor-pointer transition-colors border ${
                index === 0 ? 'bg-brand/5 border-brand/20' : 'bg-white border-gray-50 hover:bg-gray-50'
              }`}
            >
              <div className="flex justify-between items-start mb-2">
                <p className="font-bold text-gray-800">{paciente.nome}</p>
                <span className="flex items-center text-xs font-medium text-gray-500">
                  <Clock size={12} className="mr-1" /> {paciente.hora}
                </span>
              </div>
              <div className="flex items-center space-x-2">
                {index === 0 ? (
                  <span className="text-xs font-semibold px-2 py-1 rounded bg-brand text-white flex items-center">
                    <CheckCircle2 size={12} className="mr-1" /> {paciente.status}
                  </span>
                ) : (
                  <span className="text-xs font-semibold px-2 py-1 rounded bg-orange-50 text-orange-600">
                    {paciente.status}
                  </span>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* COLUNA DIREITA: ÁREA DO PRONTUÁRIO */}
      <div className="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-[calc(100vh-8rem)]">
        
        {/* Cabeçalho do Paciente Ativo */}
        <div className="p-5 border-b border-gray-100 flex items-center space-x-4 bg-gray-50/50 rounded-t-xl">
          <div className="w-12 h-12 rounded-full bg-brand text-white flex items-center justify-center font-bold text-lg">
            MS
          </div>
          <div>
            <h2 className="text-xl font-bold text-gray-800">Maria Silva</h2>
            <p className="text-sm text-gray-500">Feminino • 45 anos • Convênio: Unimed</p>
          </div>
        </div>

        {/* Menu de Abas */}
        <div className="flex border-b border-gray-100 px-2">
          <button 
            onClick={() => setAbaAtiva('consulta')}
            className={`flex items-center px-4 py-3 text-sm font-medium border-b-2 transition-colors ${abaAtiva === 'consulta' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
          >
            <ClipboardType size={16} className="mr-2" /> Evolução
          </button>
          <button 
            onClick={() => setAbaAtiva('prescricao')}
            className={`flex items-center px-4 py-3 text-sm font-medium border-b-2 transition-colors ${abaAtiva === 'prescricao' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
          >
            <Pill size={16} className="mr-2" /> Prescrição
          </button>
          <button 
            onClick={() => setAbaAtiva('exames')}
            className={`flex items-center px-4 py-3 text-sm font-medium border-b-2 transition-colors ${abaAtiva === 'exames' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
          >
            <Stethoscope size={16} className="mr-2" /> Exames
          </button>
          <button 
            onClick={() => setAbaAtiva('historico')}
            className={`flex items-center px-4 py-3 text-sm font-medium border-b-2 transition-colors ${abaAtiva === 'historico' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
          >
            <History size={16} className="mr-2" /> Histórico
          </button>
        </div>

        {/* CONTEÚDO DAS ABAS */}
        <div className="flex-1 overflow-y-auto p-6">
          
          {/* Aba: Realização da Consulta */}
          {abaAtiva === 'consulta' && (
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Motivo da Consulta / Histórico Médico</label>
                <textarea 
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand resize-none h-24 text-sm"
                  placeholder="Descreva os sintomas e o histórico do paciente..."
                ></textarea>
              </div>
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Diagnóstico (CID)</label>
                <input 
                  type="text" 
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm"
                  placeholder="Ex: J03.9 - Amigdalite aguda não especificada"
                />
              </div>
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Evolução / Conduta Clínica</label>
                <textarea 
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand resize-none h-32 text-sm"
                  placeholder="Anote a conduta, orientações e evolução do quadro..."
                ></textarea>
              </div>
              <div className="flex justify-end pt-2">
                <button className="px-6 py-2 bg-brand text-white font-bold rounded-lg hover:bg-brand-dark transition-colors">
                  Salvar Evolução
                </button>
              </div>
            </div>
          )}

          {/* Aba: Prescrição Eletrônica */}
          {abaAtiva === 'prescricao' && (
            <div>
              <div className="flex gap-2 mb-6">
                <div className="flex-1">
                  <input type="text" placeholder="Nome do Medicamento" className="w-full p-2 border border-gray-200 rounded-lg outline-none focus:border-brand text-sm" />
                </div>
                <div className="flex-1">
                  <input type="text" placeholder="Posologia (Ex: 1 comp de 8/8h)" className="w-full p-2 border border-gray-200 rounded-lg outline-none focus:border-brand text-sm" />
                </div>
                <button className="px-4 py-2 bg-brand text-white font-bold rounded-lg text-sm hover:bg-brand-dark">Adicionar</button>
              </div>
              
              <div className="border border-gray-100 rounded-lg">
                <div className="bg-gray-50 p-3 border-b border-gray-100 font-bold text-sm text-gray-700">Receituário Atual</div>
                <div className="p-4 text-center text-sm text-gray-400 py-10">
                  Nenhum medicamento prescrito ainda.
                </div>
              </div>
            </div>
          )}

          {/* Aba: Solicitação de Exames */}
          {abaAtiva === 'exames' && (
            <div>
              <div className="flex gap-2 mb-6">
                <input type="text" placeholder="Buscar exame (Ex: Hemograma Completo)" className="w-full p-2 border border-gray-200 rounded-lg outline-none focus:border-brand text-sm" />
                <button className="px-4 py-2 bg-brand text-white font-bold rounded-lg text-sm hover:bg-brand-dark">Solicitar</button>
              </div>
              <div className="border border-gray-100 rounded-lg">
                <div className="bg-gray-50 p-3 border-b border-gray-100 font-bold text-sm text-gray-700">Pedidos de Exames</div>
                <div className="p-4 text-center text-sm text-gray-400 py-10">
                  Nenhum exame solicitado nesta consulta.
                </div>
              </div>
            </div>
          )}

          {/* Aba: Histórico */}
          {abaAtiva === 'historico' && (
            <div className="space-y-4">
              <div className="p-4 border border-gray-100 rounded-lg hover:shadow-sm transition-shadow">
                <div className="flex justify-between items-center mb-2">
                  <span className="font-bold text-brand">Consulta de Rotina</span>
                  <span className="text-xs text-gray-500">12 de Jan, 2026</span>
                </div>
                <p className="text-sm text-gray-600 line-clamp-2">Paciente relatou dores de cabeça frequentes. Pressão arterial 12/8. Solicitado exames de sangue rotineiros.</p>
                <p className="text-xs text-gray-400 mt-2">Atendido por: Dr. Roberto (Clínico Geral)</p>
              </div>
              
              <div className="p-4 border border-gray-100 rounded-lg hover:shadow-sm transition-shadow">
                <div className="flex justify-between items-center mb-2">
                  <span className="font-bold text-brand">Retorno de Exames</span>
                  <span className="text-xs text-gray-500">05 de Dez, 2025</span>
                </div>
                <p className="text-sm text-gray-600 line-clamp-2">Exames dentro da normalidade. Orientada a manter dieta com restrição de sódio e iniciar atividade física regular.</p>
                <p className="text-xs text-gray-400 mt-2">Atendido por: Dr. Carlos (Cardiologista)</p>
              </div>
            </div>
          )}
          
        </div>
      </div>
    </div>
  );
}