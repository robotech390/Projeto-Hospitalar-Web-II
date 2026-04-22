import { useState, useEffect } from 'react';
import axios from 'axios';

export default function Dispensacao() {
  const [prescricao, setPrescricao] = useState('');
  const [idLoteSelecionado, setIdLoteSelecionado] = useState('');
  const [quantidadeDesejada, setQuantidadeDesejada] = useState('');
  const [lotes, setLotes] = useState<any[]>([]);
  const [carregando, setCarregando] = useState(true);
  const [processando, setProcessando] = useState(false);

  const buscarLotes = () => {
    setCarregando(true);
    axios.get('http://localhost:8000/api/lotes-disponiveis')
      .then(res => {
        setLotes(res.data);
        setCarregando(false);
      })
      .catch(() => {
        setLotes([]);
        setCarregando(false);
      });
  };

  useEffect(() => { buscarLotes(); }, []);

  const handleDispensar = async (e: React.FormEvent) => {
    e.preventDefault();
    setProcessando(true);
    
    try {
      const response = await axios.post('http://localhost:8000/api/dispensacao', {
        id_lote: idLoteSelecionado,
        quantidade: Number(quantidadeDesejada)
      });
      
      alert(response.data.mensagem);
      setPrescricao('');
      setIdLoteSelecionado('');
      setQuantidadeDesejada('');
      buscarLotes(); // Recarrega a lista (o lote zerado sumirá daqui pois o backend filtrará o 'ativo=0')
    } catch (err: any) {
      alert(err.response?.data?.erro || "Falha na comunicação");
    } finally {
      setProcessando(false);
    }
  };

  return (
    <div className="bg-white rounded-lg shadow-sm p-6">
      <h2 className="text-2xl font-semibold text-gray-700 mb-6">Dispensação de Medicamentos</h2>
      
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <form onSubmit={handleDispensar} className="space-y-4 bg-gray-50 p-6 rounded border">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">ID da Prescrição Médica</label>
            <input type="text" required value={prescricao} onChange={e => setPrescricao(e.target.value)} className="w-full border rounded px-3 py-2 outline-none focus:ring-2 focus:ring-[var(--color-brand-light)]" placeholder="Ex: PRES-99812" />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Medicamento (Lote Ativo)</label>
            <select 
              required 
              value={idLoteSelecionado} 
              onChange={e => setIdLoteSelecionado(e.target.value)} 
              className="w-full border rounded px-3 py-2 bg-white outline-none"
            >
              <option value="">{carregando ? 'Carregando lotes...' : 'Selecione um lote...'}</option>
              {lotes.map(lote => (
                <option key={lote.id} value={lote.id}>
                  {/* Mostra o ID do Medicamento + Nome + Lote para facilitar a conferência */}
                  ID {lote.id_medicamento} - {lote.medicamento?.nome || 'ERRO: Med. não encontrado'} - Lote: {lote.numero} (Qtd: {lote.quantidade_produtos})
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
            <input type="number" required min="1" value={quantidadeDesejada} onChange={e => setQuantidadeDesejada(e.target.value)} className="w-full border rounded px-3 py-2 outline-none" />
          </div>

          <button 
            type="submit" 
            disabled={processando || lotes.length === 0}
            className="w-full bg-[var(--color-brand-dark)] text-white px-4 py-3 rounded shadow hover:bg-opacity-90 transition font-semibold disabled:bg-gray-400 flex items-center justify-center"
          >
            {processando ? 'Processando baixa...' : 'Confirmar Saída de Estoque'}
          </button>
        </form>

        <div className="bg-blue-50 p-6 rounded border border-blue-100">
          <h3 className="text-lg font-medium text-blue-800 mb-2">Regra de Negócio Automática</h3>
          <p className="text-sm text-blue-700 leading-relaxed">
            Ao confirmar a dispensação, o sistema subtrai a quantidade do banco de dados. Caso o saldo chegue a zero, o lote é automaticamente marcado como <strong>Inativo (ativo = 0)</strong> e não aparecerá mais nesta listagem.
          </p>
        </div>
      </div>
    </div>
  );
}