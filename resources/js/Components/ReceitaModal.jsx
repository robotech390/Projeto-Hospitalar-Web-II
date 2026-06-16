import { useState, useEffect } from 'react';
import { Plus } from 'lucide-react';
import Modal from './Modal';

function getTodayDateString() {
  const today = new Date();
  return today.toISOString().split('T')[0];
}

export default function ReceitaModal({ show, onClose, consultaId, medicamentos }) {
  const [observacoes, setObservacoes] = useState('');
  const [farmacia, setFarmacia] = useState('');
  const [dataEmissao, setDataEmissao] = useState(getTodayDateString());
  const [medicamentoSelecionado, setMedicamentoSelecionado] = useState('');
  const [quantidade, setQuantidade] = useState(1);
  const [posologia, setPosologia] = useState('');
  const [itens, setItens] = useState([]);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');

  useEffect(() => {
    if (!show) {
      return;
    }

    setObservacoes('');
    setFarmacia('');
    setDataEmissao(getTodayDateString());
    setMedicamentoSelecionado('');
    setQuantidade(1);
    setPosologia('');
    setItens([]);
    setErrorMessage('');
  }, [show]);

  const handleAddItem = () => {
    if (!medicamentoSelecionado) {
      setErrorMessage('Selecione um medicamento antes de adicionar.');
      return;
    }

    const existingItem = itens.find((item) => item.id_medicamento === medicamentoSelecionado);
    if (existingItem) {
      setErrorMessage('Este medicamento já foi adicionado à receita.');
      return;
    }

    setItens((current) => [
      ...current,
      {
        id_medicamento: medicamentoSelecionado,
        quantidade: Number(quantidade) || 1,
        posologia: posologia.trim() || null,
      },
    ]);
    setMedicamentoSelecionado('');
    setQuantidade(1);
    setPosologia('');
    setErrorMessage('');
  };

  const handleRemoveItem = (id_medicamento) => {
    setItens((current) => current.filter((item) => item.id_medicamento !== id_medicamento));
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    setErrorMessage('');

    if (!consultaId) {
      setErrorMessage('Selecione uma consulta ativa antes de criar a receita.');
      return;
    }

    if (!dataEmissao) {
      setErrorMessage('Informe a data de emissão da receita.');
      return;
    }

    setIsSubmitting(true);

    try {
      const response = await fetch('/receitas', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
          Accept: 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          id_consulta: consultaId,
          observacoes: observacoes.trim() || null,
          farmacia: farmacia.trim() || null,
          data_emissao: dataEmissao,
          medicamentos: itens,
        }),
      });

      if (!response.ok) {
        const result = await response.json().catch(() => null);
        throw new Error(result?.message || 'Erro ao criar a receita.');
      }

      window.location.reload();
    } catch (error) {
      setErrorMessage(error.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleClose = () => {
    setObservacoes('');
    setFarmacia('');
    setMedicamentoSelecionado('');
    setQuantidade(1);
    setPosologia('');
    setItens([]);
    setErrorMessage('');
    onClose();
  };

  return (
    <Modal show={show} onClose={handleClose} maxWidth="lg">
      <div className="bg-white rounded-lg p-6">
        <h3 className="text-lg font-bold text-gray-800 mb-4">Criar Receita</h3>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid gap-4 sm:grid-cols-2">
            <div>
              <label className="block text-sm font-bold text-gray-700 mb-1">Data de emissão</label>
              <input
                type="date"
                value={dataEmissao}
                onChange={(event) => setDataEmissao(event.target.value)}
                className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm"
              />
            </div>
            <div>
              <label className="block text-sm font-bold text-gray-700 mb-1">Farmácia</label>
              <input
                type="text"
                value={farmacia}
                onChange={(event) => setFarmacia(event.target.value)}
                placeholder="Nome da farmácia ou local"
                className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm"
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-bold text-gray-700 mb-1">Observações</label>
            <textarea
              value={observacoes}
              onChange={(event) => setObservacoes(event.target.value)}
              placeholder="Instruções adicionais para o paciente..."
              className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand resize-none h-20 text-sm"
            />
          </div>

          <div className="border border-gray-100 rounded-lg p-4 bg-gray-50">
            <div className="flex items-center justify-between mb-3">
              <div>
                <h4 className="text-sm font-semibold text-gray-800">Itens da receita</h4>
                <p className="text-xs text-gray-500">Adicione medicamentos e posologia antes de salvar.</p>
              </div>
              <button
                type="button"
                onClick={handleAddItem}
                className="flex items-center px-3 py-2 bg-brand text-white rounded-lg hover:bg-brand-dark transition-colors text-sm font-medium"
              >
                <Plus size={14} className="mr-2" />
                Adicionar medicamento
              </button>
            </div>
            <div className="grid gap-4 sm:grid-cols-3 mb-4">
              <div className="sm:col-span-2">
                <label className="block text-sm font-bold text-gray-700 mb-1">Medicamento</label>
                <select
                  value={medicamentoSelecionado}
                  onChange={(event) => setMedicamentoSelecionado(Number(event.target.value))}
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm"
                >
                  <option value="">Selecione um medicamento</option>
                  {Array.isArray(medicamentos) && medicamentos.length > 0 ? (
                    medicamentos.map((med) => (
                      <option key={med.id} value={med.id}>{med.nome}</option>
                    ))
                  ) : (
                    <option value="">Nenhum medicamento disponível</option>
                  )}
                </select>
              </div>
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Quantidade</label>
                <input
                  type="number"
                  min="1"
                  value={quantidade}
                  onChange={(event) => setQuantidade(Number(event.target.value) || 1)}
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm"
                />
              </div>
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Posologia</label>
                <input
                  type="text"
                  value={posologia}
                  onChange={(event) => setPosologia(event.target.value)}
                  placeholder="Ex: 1 comprimido 8/8h"
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm"
                />
              </div>
            </div>

            {itens.length > 0 ? (
              <div className="space-y-3">
                {itens.map((item) => {
                  const medicamento = medicamentos.find((med) => med.id === item.id_medicamento);
                  return (
                    <div key={item.id_medicamento} className="rounded-lg border border-gray-200 bg-white p-3 flex items-start justify-between gap-3">
                      <div>
                        <p className="text-sm font-semibold text-gray-700">{medicamento?.nome ?? `Medicamento #${item.id_medicamento}`}</p>
                        <p className="text-xs text-gray-500">Quantidade: {item.quantidade}</p>
                        <p className="text-xs text-gray-500">Posologia: {item.posologia ?? '—'}</p>
                      </div>
                      <button
                        type="button"
                        onClick={() => handleRemoveItem(item.id_medicamento)}
                        className="text-sm font-medium text-red-600 hover:text-red-800"
                      >
                        Remover
                      </button>
                    </div>
                  );
                })}
              </div>
            ) : (
              <div className="rounded-lg border border-dashed border-gray-200 bg-white p-4 text-sm text-gray-500">
                Nenhum medicamento adicionado ainda.
              </div>
            )}
          </div>

          {errorMessage && (
            <p className="text-sm text-red-600">{errorMessage}</p>
          )}

          <div className="flex justify-end gap-2 pt-2">
            <button
              type="button"
              onClick={handleClose}
              disabled={isSubmitting}
              className="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors disabled:opacity-50"
            >
              Cancelar
            </button>
            <button
              type="submit"
              disabled={isSubmitting}
              className="px-4 py-2 text-sm font-medium bg-brand text-white rounded-lg hover:bg-brand-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {isSubmitting ? 'Salvando...' : 'Salvar Receita'}
            </button>
          </div>
        </form>
      </div>
    </Modal>
  );
}
