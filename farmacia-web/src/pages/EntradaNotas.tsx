import { useState } from 'react';

export default function EntradaNotas() {
  const [nf, setNf] = useState({ numero: '', cpf_cnpj: '', data: '' });
  const [lote, setLote] = useState({ produto_id: '', numero_lote: '', validade: '', quantidade: '', preco_unitario: '' });

  const handleSalvarEntrada = (e: React.FormEvent) => {
    e.preventDefault();
    alert(`Nota Fiscal ${nf.numero} e Lote ${lote.numero_lote} registrados com sucesso! (Simulação)`);
    // Limpar form
    setNf({ numero: '', cpf_cnpj: '', data: '' });
    setLote({ produto_id: '', numero_lote: '', validade: '', quantidade: '', preco_unitario: '' });
  };

  return (
    <div className="bg-white rounded-lg shadow-sm p-6">
      <h2 className="text-2xl font-semibold text-gray-700 mb-6">Entrada de Notas Fiscais e Lotes</h2>
      
      <form onSubmit={handleSalvarEntrada} className="space-y-6">
        {/* Dados da Nota Fiscal */}
        <div className="p-4 border rounded bg-gray-50">
          <h3 className="text-lg font-medium text-gray-700 mb-4">Dados da Nota Fiscal</h3>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Número da NF</label>
              <input type="text" required value={nf.numero} onChange={e => setNf({...nf, numero: e.target.value})} className="w-full border rounded px-3 py-2" placeholder="000.000.000" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">CPF/CNPJ do Fornecedor</label>
              <input type="text" required value={nf.cpf_cnpj} onChange={e => setNf({...nf, cpf_cnpj: e.target.value})} className="w-full border rounded px-3 py-2" placeholder="00.000.000/0001-00" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Data de Emissão</label>
              <input type="date" required value={nf.data} onChange={e => setNf({...nf, data: e.target.value})} className="w-full border rounded px-3 py-2" />
            </div>
          </div>
        </div>

        {/* Dados do Lote / Produto */}
        <div className="p-4 border rounded bg-gray-50">
          <h3 className="text-lg font-medium text-gray-700 mb-4">Itens da Nota (Lote)</h3>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Produto (Catálogo)</label>
              <select required value={lote.produto_id} onChange={e => setLote({...lote, produto_id: e.target.value})} className="w-full border rounded px-3 py-2 bg-white">
                <option value="">Selecione um produto...</option>
                <option value="1">Dipirona Sódica 500mg</option>
                <option value="2">Amoxicilina 875mg</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Número do Lote</label>
              <input type="text" required value={lote.numero_lote} onChange={e => setLote({...lote, numero_lote: e.target.value})} className="w-full border rounded px-3 py-2" placeholder="Ex: LT-2026" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Data de Validade</label>
              <input type="date" required value={lote.validade} onChange={e => setLote({...lote, validade: e.target.value})} className="w-full border rounded px-3 py-2" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Quantidade</label>
              <input type="number" required value={lote.quantidade} onChange={e => setLote({...lote, quantidade: e.target.value})} className="w-full border rounded px-3 py-2" placeholder="Ex: 500" />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Preço Unitário (R$)</label>
              <input type="number" step="0.01" required value={lote.preco_unitario} onChange={e => setLote({...lote, preco_unitario: e.target.value})} className="w-full border rounded px-3 py-2" placeholder="0.00" />
            </div>
          </div>
        </div>

        <div className="flex justify-end">
          <button type="submit" className="bg-[var(--color-brand-primary)] text-white px-6 py-2 rounded shadow hover:bg-[var(--color-brand-dark)] transition">
            Dar Entrada no Estoque
          </button>
        </div>
      </form>
    </div>
  );
}