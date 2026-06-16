import { useState, useEffect } from 'react';
import { Plus } from 'lucide-react';
import Modal from './Modal';

export default function SolicitacaoExameModal({ show, onClose, consultaId, solicitacao, tiposExame }) {
  const [justificativa, setJustificativa] = useState('');
  const [prioridade, setPrioridade] = useState('2');
  const [tipoExameSelecionado, setTipoExameSelecionado] = useState('');
  const [status, setStatus] = useState('pendente');
  const [arquivo, setArquivo] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  useEffect(() => {
    if (!show) {
      return;
    }

    if (solicitacao) {
      setTipoExameSelecionado('');
      setStatus('pendente');
      setArquivo('');
      setErrorMessage('');
    } else {
      setJustificativa('');
      setPrioridade('2');
      setErrorMessage('');
    }
  }, [show, solicitacao]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErrorMessage('');

    if (solicitacao && !tipoExameSelecionado) {
      setErrorMessage('Selecione o tipo de exame.');
      return;
    }

    setIsSubmitting(true);

    try {
      const url = solicitacao ? `/solicitacoes-exame/${solicitacao.id}` : '/solicitacoes-exame';
      const id_consulta = solicitacao?.id_consulta ?? consultaId;
      const payload = {
        id_consulta,
        justificativa: solicitacao ? solicitacao.justificativa ?? '' : justificativa.trim(),
        prioridade: solicitacao ? solicitacao.prioridade ?? 2 : parseInt(prioridade, 10),
      };

      if (solicitacao) {
        const itensExistentes = Array.isArray(solicitacao.itens)
          ? solicitacao.itens.map((item) => ({
              id_tipo_exame: item.id_tipo_exame,
              status: item.status ?? 'pendente',
              arquivo: item.arquivo ?? null,
            }))
          : [];

        payload.itens = [
          ...itensExistentes,
          {
            id_tipo_exame: tipoExameSelecionado,
            status: status.trim() || 'pendente',
            arquivo: arquivo.trim() || null,
          },
        ];
      }

      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          ...(solicitacao ? { 'X-HTTP-Method-Override': 'PUT' } : {}),
          Accept: 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      });

      if (!response.ok) {
        const result = await response.json().catch(() => null);
        throw new Error(result?.message || 'Erro ao salvar solicitação de exame.');
      }

      window.location.reload();
    } catch (error) {
      setErrorMessage(error.message);
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleClose = () => {
    setJustificativa('');
    setPrioridade('2');
    setTipoExameSelecionado('');
    setStatus('pendente');
    setArquivo('');
    setErrorMessage('');
    onClose();
  };

  return (
    <Modal show={show} onClose={handleClose} maxWidth="md">
      <div className="bg-white rounded-lg p-6">
        <h3 className="text-lg font-bold text-gray-800 mb-4">
          {solicitacao ? 'Adicionar item de exame' : 'Solicitar Exame'}
        </h3>

        <form onSubmit={handleSubmit} className="space-y-4">
          {solicitacao ? (
            <>
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-2">Tipo de Exame</label>
                <div className="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3">
                  {Array.isArray(tiposExame) && tiposExame.length > 0 ? (
                    tiposExame.map((tipo) => (
                      <label key={tipo.id} className="flex items-center">
                        <input
                          type="radio"
                          name="tipoExame"
                          checked={tipoExameSelecionado === tipo.id}
                          onChange={() => setTipoExameSelecionado(tipo.id)}
                          className="rounded border-gray-300"
                        />
                        <span className="ml-2 text-sm text-gray-700">{tipo.nome}</span>
                      </label>
                    ))
                  ) : (
                    <p className="text-sm text-gray-500">Nenhum tipo de exame disponível.</p>
                  )}
                </div>
              </div>

              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Status</label>
                <input
                  type="text"
                  value={status}
                  onChange={(e) => setStatus(e.target.value)}
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm"
                  placeholder="pendente"
                />
              </div>

              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Arquivo</label>
                <input
                  type="text"
                  value={arquivo}
                  onChange={(e) => setArquivo(e.target.value)}
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm"
                  placeholder="URL ou caminho do arquivo..."
                />
              </div>
            </>
          ) : (
            <>
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Justificativa</label>
                <textarea
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand resize-none h-20 text-sm"
                  placeholder="Motivo da solicitação de exame..."
                  value={justificativa}
                  onChange={(e) => setJustificativa(e.target.value)}
                />
              </div>

              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Prioridade</label>
                <select
                  value={prioridade}
                  onChange={(e) => setPrioridade(e.target.value)}
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm"
                >
                  <option value="1">Baixa</option>
                  <option value="2">Normal</option>
                  <option value="3">Alta</option>
                </select>
              </div>
            </>
          )}

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
              disabled={isSubmitting || (solicitacao ? !tipoExameSelecionado : false)}
              className="flex items-center px-4 py-2 text-sm font-medium bg-brand text-white rounded-lg hover:bg-brand-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Plus size={16} className="mr-2" />
              {isSubmitting
                ? 'Salvando...'
                : solicitacao
                  ? 'Adicionar item'
                  : 'Solicitar'}
            </button>
          </div>
        </form>
      </div>
    </Modal>
  );
}
