import { useState } from 'react';
import axios from 'axios';

export default function Dispensacao() {
  const [idBusca, setIdBusca] = useState('');
  const [dados, setDados] = useState<any>(null);
  const [carregando, setCarregando] = useState(false);
  const [confirmando, setConfirmando] = useState(false);

  const buscarDadosDispensacao = async () => {
    if (!idBusca) return;
    setCarregando(true);
    try {
      const res = await axios.get(`http://localhost:8000/api/lote/${idBusca}`);
      setDados(res.data);
    } catch (err: any) {
      alert(err.response?.data?.erro || "ID não encontrado.");
      setDados(null);
    } finally { 
      setCarregando(false); 
    }
  };

  const confirmarSaida = async () => {
    setConfirmando(true);
    try {
      await axios.post('http://localhost:8000/api/dispensacao', {
        id_lote: dados.lote.id,
        quantidade: dados.qtd_receitada,
        id_item_receita: dados.id_item_receita // Passa o ID para invalidar a receita
      });
      alert("Dispensação confirmada com sucesso!");
      setDados(null);
      setIdBusca('');
    } catch (err: any) {
      alert(err.response?.data?.erro || "Erro ao confirmar saída.");
    } finally { 
      setConfirmando(false); 
    }
  };

  return (
    <div className="bg-white rounded-lg shadow-sm p-6 max-w-5xl mx-auto">
      <h2 className="text-2xl font-semibold text-gray-700 mb-6">Dispensação por Receita</h2>
      
      <div className="flex gap-4 mb-8 bg-gray-50 p-4 rounded border">
        <input 
          type="number" 
          placeholder="Digite o ID do Item da Receita..." 
          className="flex-1 border p-3 rounded shadow-sm outline-none focus:ring-2 focus:ring-blue-400"
          value={idBusca}
          onChange={e => setIdBusca(e.target.value.replace(/\D/g, ''))} // Apenas números
        />
        <button 
          onClick={buscarDadosDispensacao}
          disabled={carregando || !idBusca}
          className="bg-[var(--color-brand-dark)] text-white px-8 py-2 rounded font-bold shadow disabled:bg-gray-400"
        >
          {carregando ? 'Buscando...' : 'Buscar Receita'}
        </button>
      </div>

      {dados && (
        <div className="animate-in fade-in duration-500 border p-6 rounded-lg">
          <h3 className="font-bold text-lg mb-4 text-gray-700">Resumo da Liberação</h3>
          <table className="w-full border-collapse mb-8 text-left">
            <thead>
              <tr className="bg-gray-200 text-gray-700 text-sm">
                <th className="p-3">Medicamento</th>
                <th className="p-3">Qtd. Solicitada</th>
                <th className="p-3">Lote Alocado (Automático)</th>
              </tr>
            </thead>
            <tbody>
              <tr className="border-b text-sm">
                <td className="p-3">{dados.medicamento}</td>
                <td className="p-3 font-bold text-blue-600">{dados.qtd_receitada} und.</td>
                <td className="p-3">
                  <span className="block font-medium">Nº: {dados.lote.numero}</span>
                  <span className="text-xs text-gray-500">Estoque atual: {dados.lote.estoque_atual}</span>
                </td>
              </tr>
            </tbody>
          </table>

          <button 
            onClick={confirmarSaida}
            disabled={confirmando}
            className="w-full bg-green-600 text-white py-4 rounded font-bold shadow-lg hover:bg-green-700 transition uppercase disabled:bg-gray-400"
          >
            {confirmando ? 'BAIXANDO ESTOQUE...' : 'CONFIRMAR ENTREGA DO MEDICAMENTO'}
          </button>
        </div>
      )}
    </div>
  );
}