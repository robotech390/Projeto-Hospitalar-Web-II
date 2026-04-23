import { useState, useEffect } from 'react';
import axios from 'axios';

export default function GestaoNotas() {
  const [tipo, setTipo] = useState<'E' | 'S'>('E');
  const [nf, setNf] = useState({ numero: '', cpf_cnpj: '', data: new Date().toISOString().split('T')[0] });
  const [medicamentos, setMedicamentos] = useState<any[]>([]);
  const [lotesDisponiveis, setLotesDisponiveis] = useState<any[]>([]);
  
  const [itens, setItens] = useState<any[]>([]);
  const [itemAtual, setItemAtual] = useState({ id_medicamento: '', id_lote: '', numero_lote: '', data_validade: '', quantidade: '', cfop: '5102' });
  const [processando, setProcessando] = useState(false);

  // 1. ADICIONADO AQUI: Estado da busca
  const [busca, setBusca] = useState('');

  useEffect(() => {
    axios.get('http://localhost:8000/api/medicamentos').then(res => setMedicamentos(res.data));
    axios.get('http://localhost:8000/api/lotes-disponiveis').then(res => setLotesDisponiveis(res.data));
  }, []);

  // 2. ADICIONADO AQUI: Lógica de filtro (Fica fora do return, logo antes dele)
  const lotesFiltrados = lotesDisponiveis.filter(l => 
    l.medicamento?.nome.toLowerCase().includes(busca.toLowerCase()) || 
    l.numero.toLowerCase().includes(busca.toLowerCase()) ||
    l.id_medicamento.toString() === busca
  );

  const adicionarNaLista = () => {
    if (!itemAtual.quantidade) return alert("Informe a quantidade");
    setItens([...itens, itemAtual]);
    setItemAtual({ id_medicamento: '', id_lote: '', numero_lote: '', data_validade: '', quantidade: '', cfop: '5102' });
    setBusca(''); // Limpa a busca ao adicionar
  };

  const processarNota = async () => {
    setProcessando(true);
    try {
      await axios.post('http://localhost:8000/api/notas-fiscais', { ...nf, tipo, itens });
      alert("Nota Fiscal e Estoque processados com sucesso!");
      setItens([]);
      setNf({ numero: '', cpf_cnpj: '', data: new Date().toISOString().split('T')[0] });
    } catch (err) {
      alert("Erro ao processar a movimentação.");
    } finally {
      setProcessando(false);
    }
  };

  return (
    <div className="bg-white rounded-lg shadow-sm p-6">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-semibold text-gray-700">Entrada/Saída por Nota Fiscal</h2>
        <div className="bg-gray-100 p-1 rounded flex gap-2">
          <button onClick={() => {setTipo('E'); setItens([]);}} className={`px-4 py-1 rounded ${tipo === 'E' ? 'bg-white shadow text-green-600 font-bold' : ''}`}>Entrada</button>
          <button onClick={() => {setTipo('S'); setItens([]);}} className={`px-4 py-1 rounded ${tipo === 'S' ? 'bg-white shadow text-red-600 font-bold' : ''}`}>Saída</button>
        </div>
      </div>

      {/* Cabeçalho da Nota */}
      <div className="grid grid-cols-3 gap-4 mb-8 bg-gray-50 p-4 rounded border">
        <input placeholder="Nº da Nota" className="border p-2 rounded" value={nf.numero} onChange={e => setNf({...nf, numero: e.target.value})} />
        <input placeholder="CPF/CNPJ" className="border p-2 rounded" value={nf.cpf_cnpj} onChange={e => setNf({...nf, cpf_cnpj: e.target.value})} />
        <input type="date" className="border p-2 rounded" value={nf.data} onChange={e => setNf({...nf, data: e.target.value})} />
      </div>

      {/* 3. SUBSTITUÍDO AQUI: Bloco Adicionar Item com Busca Inteligente */}
      <div className="border p-4 rounded mb-6 bg-blue-50/30">
        <h3 className="font-bold mb-4 text-blue-700">Adicionar Item à Nota</h3>
        
        <div className="flex flex-col gap-4">
          <div className="flex gap-2">
            <div className="relative flex-1">
              <input 
                type="text" 
                placeholder="🔍 Filtre por nome do remédio ou número do lote..." 
                className="w-full border p-3 rounded-lg shadow-sm outline-none focus:ring-2 focus:ring-blue-400"
                value={busca}
                onChange={e => setBusca(e.target.value)}
              />
              {busca && (
                <button onClick={() => setBusca('')} className="absolute right-3 top-3 text-gray-400 hover:text-gray-600">✕</button>
              )}
            </div>
          </div>

          <div className="grid grid-cols-4 gap-4">
            {tipo === 'E' ? (
              <>
                <select className="border p-2 rounded bg-white" value={itemAtual.id_medicamento} onChange={e => setItemAtual({...itemAtual, id_medicamento: e.target.value})}>
                  <option value="">Escolha o Medicamento...</option>
                  {medicamentos.map(m => <option key={m.id} value={m.id}>{m.nome}</option>)}
                </select>
                <input placeholder="Nº Lote" className="border p-2 rounded" value={itemAtual.numero_lote} onChange={e => setItemAtual({...itemAtual, numero_lote: e.target.value})} />
                <input type="date" className="border p-2 rounded" value={itemAtual.data_validade} onChange={e => setItemAtual({...itemAtual, data_validade: e.target.value})} />
              </>
            ) : (
              <select className="border p-2 rounded bg-white col-span-2" value={itemAtual.id_lote} onChange={e => setItemAtual({...itemAtual, id_lote: e.target.value})}>
                <option value="">{lotesFiltrados.length} lote(s) encontrado(s)...</option>
                {lotesFiltrados.map(l => (
                  <option key={l.id} value={l.id}>
                    {l.medicamento?.nome} (Lote: {l.numero} | Qtd: {l.quantidade_produtos})
                  </option>
                ))}
              </select>
            )}
            <input type="number" placeholder="Qtd" className="border p-2 rounded" value={itemAtual.quantidade} onChange={e => setItemAtual({...itemAtual, quantidade: e.target.value})} />
            <button onClick={adicionarNaLista} className="bg-blue-600 text-white rounded font-bold py-2 hover:bg-blue-700 transition">
              + Incluir na Nota
            </button>
          </div>
        </div>
      </div>

      {/* Tabela de Itens Temporários */}
      <table className="w-full mb-6 border-collapse">
        <thead><tr className="text-left border-b text-gray-500"><th>Item/Lote</th><th>Qtd</th><th>CFOP</th></tr></thead>
        <tbody>
          {itens.map((it, i) => (
            <tr key={i} className="border-b">
              <td className="py-2">{it.numero_lote || 'ID Lote: '+it.id_lote}</td>
              <td>{it.quantidade}</td>
              <td>{it.cfop}</td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* Botão de Finalização */}
      <button 
        onClick={processarNota} 
        disabled={itens.length === 0 || processando} 
        className={`w-full py-3 rounded font-bold shadow-lg transition flex items-center justify-center gap-3 ${
          processando ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 text-white'
        }`}
      >
        {processando ? (
          <>
            <svg className="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
              <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none"></circle>
              <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            LANÇANDO NO BANCO...
          </>
        ) : (
          'FINALIZAR MOVIMENTAÇÃO DE ESTOQUE'
        )}
      </button>
    </div>
  );
}