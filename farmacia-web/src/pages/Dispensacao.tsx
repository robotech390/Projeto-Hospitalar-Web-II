import { useState } from 'react';

export default function Dispensacao() {
  const [prescricao, setPrescricao] = useState('');
  const [itemSelecionado, setItemSelecionado] = useState('');
  const [quantidadeDesejada, setQuantidadeDesejada] = useState('');

  const handleDispensar = (e: React.FormEvent) => {
    e.preventDefault();
    alert(`Dispensação registrada para a prescrição ${prescricao}. O Grupo 6 (Financeiro) seria notificado agora.`);
    setPrescricao('');
    setItemSelecionado('');
    setQuantidadeDesejada('');
  };

  return (
    <div className="bg-white rounded-lg shadow-sm p-6">
      <h2 className="text-2xl font-semibold text-gray-700 mb-6">Dispensação de Medicamentos</h2>
      
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Formulário de Saída */}
        <div>
          <form onSubmit={handleDispensar} className="space-y-4 bg-gray-50 p-6 rounded border">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">ID da Prescrição Médica (Grupo 3)</label>
              <input 
                type="text" 
                required 
                value={prescricao} 
                onChange={e => setPrescricao(e.target.value)} 
                className="w-full border rounded px-3 py-2 focus:ring-[var(--color-brand-light)]" 
                placeholder="Ex: PRES-99812" 
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Medicamento em Estoque (Lote)</label>
              <select required value={itemSelecionado} onChange={e => setItemSelecionado(e.target.value)} className="w-full border rounded px-3 py-2 bg-white">
                <option value="">Selecione o que será entregue...</option>
                <option value="1">Dipirona Sódica - Lote LT-8842 (Estoque: 50 un)</option>
                <option value="2">Amoxicilina - Lote LT-9910 (Estoque: 120 un)</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Quantidade a Dispensar</label>
              <input 
                type="number" 
                required 
                min="1"
                value={quantidadeDesejada} 
                onChange={e => setQuantidadeDesejada(e.target.value)} 
                className="w-full border rounded px-3 py-2" 
                placeholder="Ex: 2" 
              />
            </div>

            <div className="pt-4">
              <button type="submit" className="w-full bg-[var(--color-brand-dark)] text-white px-4 py-3 rounded shadow hover:bg-opacity-90 transition font-semibold">
                Confirmar Dispensação
              </button>
            </div>
          </form>
        </div>

        {/* Resumo da Integração */}
        <div className="bg-blue-50 p-6 rounded border border-blue-100">
          <h3 className="text-lg font-medium text-blue-800 mb-2">Atenção às Integrações</h3>
          <ul className="text-sm text-blue-700 space-y-3 list-disc pl-5">
            <li>Ao confirmar, o sistema deverá reduzir a quantidade do Lote selecionado no banco de dados.</li>
            <li>Se o estoque ficar baixo, um alerta deve ser disparado no Dashboard.</li>
            <li>Um JSON contendo o valor total (Qtd * Preço do Lote) deverá ser enviado para a API do Grupo Financeiro.</li>
          </ul>
        </div>
      </div>
    </div>
  );
}