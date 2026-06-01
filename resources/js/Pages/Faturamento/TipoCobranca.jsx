import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import FaturamentoLayout from '@/Components/Faturamento/FaturamentoLayout';
import Modal from '@/Components/Faturamento/Modal';

export default function TipoCobranca({ tipoCobrancas = [] }) {
  const [isModalOpen, setIsModalOpen] = useState(false);

  const [formData, setFormData] = useState({
    descricao: '',
  });

  const [editingId, setEditingId] = useState(null);

  const handleOpenModal = (id = null) => {
    if (id) {
      const tipo = tipoCobrancas.find((t) => t.id === id);

      if (tipo) {
        setFormData({
          descricao: tipo.descricao || '',
        });

        setEditingId(id);
      }
    } else {
      setFormData({
        descricao: '',
      });

      setEditingId(null);
    }

    setIsModalOpen(true);
  };

  const handleCloseModal = () => {
    setIsModalOpen(false);
    setFormData({ descricao: '' });
    setEditingId(null);
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!formData.descricao.trim()) {
      alert('Por favor, preencha a descrição.');
      return;
    }

    if (editingId) {
      router.put(`/faturamento/tipo-cobranca/${editingId}`, formData, {
        onSuccess: () => handleCloseModal(),
      });
    } else {
      router.post('/faturamento/tipo-cobranca', formData, {
        onSuccess: () => handleCloseModal(),
      });
    }
  };

  const handleDelete = (id) => {
    if (confirm('Tem certeza que deseja deletar este tipo de cobrança?')) {
      router.delete(`/faturamento/tipo-cobranca/${id}`);
    }
  };

  return (
    <FaturamentoLayout currentPage="tipo-cobranca">
      <Head title="Faturamento" />

      <div className="space-y-6">
        <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#e1f2ef] text-[#00767F] text-xs font-semibold mb-3">
              Faturamento
            </div>

            <h1 className="text-2xl font-bold text-slate-800">
              Tipos de Cobrança
            </h1>

            <p className="text-slate-500 text-sm mt-1">
              Cadastre as formas de cobrança utilizadas nos planos, contas hospitalares e faturas.
            </p>
          </div>

          <button
            onClick={() => handleOpenModal()}
            className="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#00767F] text-white rounded-xl hover:bg-[#00989F] transition-colors font-semibold shadow-sm"
          >
            <span className="text-lg leading-none">+</span>
            Novo Tipo
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <ResumoCard
            titulo="Tipos cadastrados"
            valor={tipoCobrancas.length}
            descricao="Total de formas de cobrança"
          />

          <ResumoCard
            titulo="Uso nos planos"
            valor="Planos"
            descricao="Define como o plano será faturado"
          />

          <ResumoCard
            titulo="Uso na fatura"
            valor="Conta"
            descricao="Apoia o fechamento da conta hospitalar"
          />
        </div>

        {tipoCobrancas.length === 0 ? (
          <div className="p-12 bg-white rounded-2xl shadow-sm border border-slate-100 text-center">
            <div className="flex justify-center mb-4">
              <div className="p-5 bg-[#e1f2ef] rounded-full">
                <svg
                  className="w-10 h-10 text-[#00767F]"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                  />
                </svg>
              </div>
            </div>

            <h3 className="text-lg font-semibold text-slate-800 mb-2">
              Nenhum tipo de cobrança cadastrado
            </h3>

            <p className="text-slate-500 text-sm mb-5">
              Comece criando tipos como convênio, particular ou coparticipação.
            </p>

            <button
              onClick={() => handleOpenModal()}
              className="px-5 py-3 bg-[#00767F] text-white rounded-xl hover:bg-[#00989F] transition-colors font-semibold"
            >
              Cadastrar primeiro tipo
            </button>
          </div>
        ) : (
          <div className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div className="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
              <div>
                <h2 className="font-semibold text-slate-800">
                  Lista de Tipos de Cobrança
                </h2>

                <p className="text-xs text-slate-500 mt-1">
                  Estes registros são utilizados para classificar planos e regras de faturamento.
                </p>
              </div>

              <span className="text-xs px-3 py-1 rounded-full bg-[#e1f2ef] text-[#00767F] font-semibold">
                {tipoCobrancas.length} registro(s)
              </span>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="bg-slate-50 border-b border-slate-100">
                    <th className="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                      ID
                    </th>

                    <th className="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                      Descrição
                    </th>

                    <th className="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                      Aplicação
                    </th>

                    <th className="px-6 py-4 text-right text-sm font-semibold text-slate-700">
                      Ações
                    </th>
                  </tr>
                </thead>

                <tbody>
                  {tipoCobrancas.map((tipo, index) => (
                    <tr
                      key={tipo.id}
                      className={`border-b border-slate-100 hover:bg-slate-50 transition-colors ${
                        index % 2 === 0 ? 'bg-white' : 'bg-slate-50/40'
                      }`}
                    >
                      <td className="px-6 py-4 text-sm text-slate-800 font-semibold">
                        #{tipo.id}
                      </td>

                      <td className="px-6 py-4">
                        <div className="font-semibold text-slate-800">
                          {tipo.descricao}
                        </div>

                        <div className="text-xs text-slate-400 mt-1">
                          Forma de cobrança cadastrada no sistema
                        </div>
                      </td>

                      <td className="px-6 py-4">
                        <span className="inline-flex px-3 py-1 rounded-full bg-[#e1f2ef] text-[#00767F] text-xs font-semibold">
                          Faturamento
                        </span>
                      </td>

                      <td className="px-6 py-4 text-right">
                        <div className="flex items-center justify-end gap-2">
                          <button
                            onClick={() => handleOpenModal(tipo.id)}
                            className="p-2 hover:bg-blue-50 rounded-lg transition-colors text-blue-600"
                            title="Editar"
                          >
                            <svg
                              className="w-5 h-5"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                              />
                            </svg>
                          </button>

                          <button
                            onClick={() => handleDelete(tipo.id)}
                            className="p-2 hover:bg-red-50 rounded-lg transition-colors text-red-600"
                            title="Deletar"
                          >
                            <svg
                              className="w-5 h-5"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                              />
                            </svg>
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>

      <Modal
        isOpen={isModalOpen}
        onClose={handleCloseModal}
        title={editingId ? 'Editar Tipo de Cobrança' : 'Novo Tipo de Cobrança'}
      >
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-slate-700 mb-2">
              Descrição *
            </label>

            <input
              type="text"
              value={formData.descricao}
              onChange={(e) =>
                setFormData({ ...formData, descricao: e.target.value })
              }
              className="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
              placeholder="Ex: Convênio, Particular, Coparticipação"
            />
          </div>

          <div className="p-4 rounded-xl bg-[#e1f2ef] border border-teal-100">
            <p className="text-sm text-[#00767F] font-medium">
              Este tipo será utilizado para classificar a forma de cobrança em planos e contas hospitalares.
            </p>
          </div>

          <div className="flex gap-3 pt-4">
            <button
              type="button"
              onClick={handleCloseModal}
              className="flex-1 px-4 py-3 border border-slate-300 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors font-medium"
            >
              Cancelar
            </button>

            <button
              type="submit"
              className="flex-1 px-4 py-3 bg-[#00767F] text-white rounded-xl hover:bg-[#00989F] transition-colors font-semibold"
            >
              Salvar
            </button>
          </div>
        </form>
      </Modal>
    </FaturamentoLayout>
  );
}

function ResumoCard({ titulo, valor, descricao }) {
  return (
    <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
      <div className="text-sm text-slate-500">
        {titulo}
      </div>

      <div className="text-2xl font-bold text-slate-800 mt-2">
        {valor}
      </div>

      <div className="text-xs text-slate-400 mt-1">
        {descricao}
      </div>
    </div>
  );
}