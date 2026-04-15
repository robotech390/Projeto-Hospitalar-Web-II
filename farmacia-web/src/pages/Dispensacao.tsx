import { useState, useEffect } from 'react';
import axios from 'axios';

export default function Dispensacao() {
  const [prescricao, setPrescricao] = useState('');
  const [idLoteSelecionado, setIdLoteSelecionado] = useState('');
  const [quantidadeDesejada, setQuantidadeDesejada] = useState('');
  
  const [lotes, setLotes] = useState<any[]>([]);
  const [carregando, setCarregando] = useState(true);

  const buscarLotes = () => {
    axios.get('http://localhost:8000/api/lotes-disponiveis')
      .then(res => {
        setLotes(res.data);
        setCarregando(false);
      })
      .catch(err => {
        console.error("Erro ao buscar lotes (Dispensação):", err);
        // Tratamento de erro: Zera os lotes para liberar a tela
        setLotes([]);
        setCarregando(false);
      });
  };

  useEffect(() => {
    buscarLotes();
  }, []);

  const handleDispensar = (e: React.FormEvent) => {
    e.preventDefault();
    
    axios.post('http://localhost:8000/api/dispensacao', {
      prescricao: prescricao, 
      id_lote: idLoteSelecionado,
      quantidade: Number(quantidadeDesejada)
    })
    .then(res => {
      alert("Sucesso: " + res.data.mensagem);
      setPrescricao('');
      setIdLoteSelecionado('');
      setQuantidadeDesejada('');
      buscarLotes(); 
    })
    .catch(err => {
      alert("Erro na dispensação: " + (err.response?.data?.erro || "Falha de comunicação com o servidor."));
    });
  };

  if (carregando) return <div className="p-8 text-center text-gray-500 font-medium">Carregando lotes disponíveis...</div>;

  return (
    <div className="bg-white rounded-lg shadow-sm p-6">
      <h2 className="text-2xl font-semibold text-gray-700 mb-6">Dispensação de Medicamentos</h2>
      
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
          <form onSubmit={handleDispensar} className="space-y-4 bg-gray-50 p-6 rounded border">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">ID da Prescrição Médica</label>
              <input type="text" required value={prescricao} onChange={e => setPrescricao(e.target.value)} className="w-full border rounded px-3 py-2 outline-none focus:ring-2 focus:ring-[var(--color-brand-light)]" placeholder="Ex: PRES-99812" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Medicamento em Estoque (Lote)</label>
              <select required value={idLoteSelecionado} onChange={e => setIdLoteSelecionado(e.target.value)} className="w-full border rounded px-3 py-2 bg-white outline-none focus:ring-2 focus:ring-[var(--color-brand-light)]">
                <option value="">Selecione o lote...</option>
                {lotes.map(lote => (
                  <option key={lote.id} value={lote.id}>
                    {lote.medicamento ? lote.medicamento.nome : 'Sem nome'} - Lote: {lote.numero} (Qtd: {lote.quantidade_produtos})
                  </option>
                ))}
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Quantidade a Dispensar</label>
              <input type="number" required min="1" value={quantidadeDesejada} onChange={e => setQuantidadeDesejada(e.target.value)} className="w-full border rounded px-3 py-2 outline-none focus:ring-2 focus:ring-[var(--color-brand-light)]" placeholder="Ex: 2" />
            </div>

            <div className="pt-4">
              <button type="submit" disabled={lotes.length === 0} className="w-full bg-[var(--color-brand-dark)] text-white px-4 py-3 rounded shadow hover:bg-opacity-90 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                Confirmar Dispensação
              </button>
            </div>
          </form>
        </div>

        <div className="bg-blue-50 p-6 rounded border border-blue-100">
          <h3 className="text-lg font-medium text-blue-800 mb-2">Aviso de Risco Arquitetural</h3>
          <ul className="text-sm text-blue-700 space-y-3 list-disc pl-5">
            <li>O sistema está conectado. Se não houver lotes no select ao lado, a tabela na AWS está vazia.</li>
            <li>Aviso: Como não há tabela de Histórico, a confirmação vai alterar a quantidade do lote silenciosamente.</li>
            <li>Dependência: A equipe de Entrada precisa inserir lotes antes de você poder testar a Dispensação.</li>
          </ul>
        </div>
      </div>
    </div>
  );
}