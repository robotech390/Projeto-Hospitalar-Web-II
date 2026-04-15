import { useEffect, useState } from 'react';
import axios from 'axios';

export default function Dashboard() {
  const [dados, setDados] = useState({
    kpis: { total_produtos: 0, alertas_quantidade: 0, dispensacoes_hoje: 0 },
    alertas_detalhados: []
  });
  const [carregando, setCarregando] = useState(true);
  const [erro, setErro] = useState('');

  useEffect(() => {
    axios.get('http://localhost:8000/api/dashboard')
      .then(response => {
        setDados(response.data);
        setCarregando(false);
      })
      .catch(error => {
        console.error("Erro na API (Dashboard):", error);
        // Tratamento de erro: Zera os dados para destravar a tela
        setDados({
          kpis: { total_produtos: 0, alertas_quantidade: 0, dispensacoes_hoje: 0 },
          alertas_detalhados: []
        });
        setErro('Falha de conexão com o banco de dados. Exibindo painel zerado.');
        setCarregando(false);
      });
  }, []);

  if (carregando) return <div className="p-8 text-center text-gray-500 font-medium">Carregando dados da farmácia...</div>;
  
  return (
    <>
      {erro && (
        <div className="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
          {erro}
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="bg-white p-6 rounded-lg shadow-sm border-l-4 border-[var(--color-brand-primary)]">
          <p className="text-gray-500 text-sm">Medicamentos Cadastrados</p>
          <p className="text-3xl font-bold text-gray-800">{dados.kpis.total_produtos}</p>
        </div>
        <div className="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
          <p className="text-gray-500 text-sm">Alertas de Estoque Baixo</p>
          <p className="text-3xl font-bold text-red-600">{dados.kpis.alertas_quantidade}</p>
        </div>
        <div className="bg-white p-6 rounded-lg shadow-sm border-l-4 border-[var(--color-brand-dark)]">
          <p className="text-gray-500 text-sm">Dispensações Hoje</p>
          <p className="text-3xl font-bold text-gray-800">{dados.kpis.dispensacoes_hoje}</p>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow-sm p-6">
        <h3 className="text-lg font-semibold mb-4 text-gray-700">Itens em Atenção (Estoque / Validade)</h3>
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b text-sm text-gray-500">
              <th className="pb-3 font-medium">Cód. Produto</th>
              <th className="pb-3 font-medium">Medicamento (Princípio Ativo)</th>
              <th className="pb-3 font-medium">Lote</th>
              <th className="pb-3 font-medium">Qtd. Atual</th>
              <th className="pb-3 font-medium">Status</th>
            </tr>
          </thead>
          <tbody className="text-sm">
            {dados.alertas_detalhados.map((alerta: any) => (
              <tr key={alerta.id} className="border-b">
                <td className="py-3 text-gray-500">#{alerta.codigo}</td>
                <td className="py-3 font-semibold text-gray-800">{alerta.medicamento}</td>
                <td className="py-3">{alerta.lote}</td>
                <td className={`py-3 font-bold ${alerta.quantidade <= 50 ? 'text-red-500' : 'text-yellow-600'}`}>
                  {alerta.quantidade} un
                </td>
                <td className="py-3">
                  <span className={`px-2 py-1 rounded text-xs font-medium ${alerta.quantidade <= 50 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'}`}>
                    {alerta.status}
                  </span>
                </td>
              </tr>
            ))}
            {dados.alertas_detalhados.length === 0 && (
              <tr>
                <td colSpan={5} className="py-4 text-center text-gray-500">Nenhum alerta no momento.</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </>
  );
}