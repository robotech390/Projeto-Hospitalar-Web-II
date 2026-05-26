import { useState, useEffect } from 'react';
import axios from 'axios';

export default function GestaoNotas() {
  const [nf, setNf] = useState({ numero: '', cpf_cnpj: '', data: new Date().toISOString().split('T')[0] });
  const [medicamentos, setMedicamentos] = useState<any[]>([]);
  const [itens, setItens] = useState<any[]>([]);
  const [itemAtual, setItemAtual] = useState({ id_medicamento: '', numero_lote: '', data_validade: '', quantidade: '', cfop: '1002' });
  const [processando, setProcessando] = useState(false);

  useEffect(() => {
    axios.get('http://localhost:8000/api/medicamentos').then(res => setMedicamentos(res.data));
  }, []);

  const adicionarNaLista = () => {
    if (!itemAtual.id_medicamento || !itemAtual.quantidade) return alert("Preencha todos os campos do item");
    setItens([...itens, itemAtual]);
    setItemAtual({ id_medicamento: '', numero_lote: '', data_validade: '', quantidade: '', cfop: '1002' });
  };

  const processarNota = async () => {
    setProcessando(true);
    try {
      await axios.post('http://localhost:8000/api/notas-fiscais', { ...nf, tipo: 'E', itens });
      alert("Estoque alimentado com sucesso!");
      setItens([]);
      setNf({ numero: '', cpf_cnpj: '', data: new Date().toISOString().split('T')[0] });
    } catch (err) {
      alert("Erro ao processar entrada.");
    } finally { setProcessando(false); }
  };

  return (
    <div className="bg-white rounded-lg shadow-sm p-6">
      <h2 className="text-2xl font-semibold text-gray-700 mb-6">Entrada de Estoque (Nota Fiscal)</h2>

      <div className="grid grid-cols-3 gap-4 mb-8 bg-gray-50 p-4 rounded border">
        <input placeholder="Nº da Nota" className="border p-2 rounded" value={nf.numero} onChange={e => setNf({...nf, numero: e.target.value})} />
        <input 
          placeholder="CPF/CNPJ do Fornecedor" 
          className="border p-2 rounded" 
          value={nf.cpf_cnpj} 
          maxLength={18}
          onChange={e => {
            let v = e.target.value.replace(/\D/g, '');
            
            if (v.length <= 11) { // Máscara de CPF
              v = v.replace(/(\d{3})(\d)/, '$1.$2');
              v = v.replace(/(\d{3})(\d)/, '$1.$2');
              v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else { // Máscara de CNPJ
              v = v.replace(/^(\d{2})(\d)/, '$1.$2');
              v = v.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
              v = v.replace(/\.(\d{3})(\d)/, '.$1/$2');
              v = v.replace(/(\d{4})(\d)/, '$1-$2');
            }
            
            setNf({...nf, cpf_cnpj: v});
          }} 
        />
        <input type="date" className="border p-2 rounded" value={nf.data} onChange={e => setNf({...nf, data: e.target.value})} />
      </div>

      <div className="border p-4 rounded mb-6 bg-green-50/20">
        <h3 className="font-bold mb-4 text-green-700">Dados do Medicamento</h3>
        <div className="grid grid-cols-4 gap-4">
          <select className="border p-2 rounded bg-white" value={itemAtual.id_medicamento} onChange={e => setItemAtual({...itemAtual, id_medicamento: e.target.value})}>
            <option value="">Selecione o Medicamento...</option>
            {medicamentos.map(m => <option key={m.id} value={m.id}>{m.nome}</option>)}
          </select>
          <input type="number" placeholder="Nº Lote" className="border p-2 rounded" value={itemAtual.numero_lote} onChange={e => setItemAtual({...itemAtual, numero_lote: e.target.value.replace(/\D/g, '')})} />
          <input type="date" className="border p-2 rounded" value={itemAtual.data_validade} onChange={e => setItemAtual({...itemAtual, data_validade: e.target.value})} />
          <input type="number" placeholder="Qtd" className="border p-2 rounded" value={itemAtual.quantidade} onChange={e => setItemAtual({...itemAtual, quantidade: e.target.value})} />
        </div>
        <button onClick={adicionarNaLista} className="w-full mt-4 bg-[var(--color-brand-primary)] text-white p-2 rounded font-bold">Incluir Item</button>
      </div>

      <table className="w-full mb-6 border-collapse">
        <thead><tr className="text-left border-b text-gray-500"><th>Medicamento ID</th><th>Lote</th><th>Qtd</th></tr></thead>
        <tbody>
          {itens.map((it, i) => (
            <tr key={i} className="border-b text-sm"><td className="py-2">#{it.id_medicamento}</td><td>{it.numero_lote}</td><td>{it.quantidade}</td></tr>
          ))}
        </tbody>
      </table>

      <button onClick={processarNota} disabled={itens.length === 0 || processando} className="w-full bg-green-600 text-white py-3 rounded font-bold shadow-lg disabled:bg-gray-300">
        {processando ? 'LANÇANDO NO BANCO...' : 'FINALIZAR ENTRADA DE NOTA'}
      </button>
    </div>
  );
}