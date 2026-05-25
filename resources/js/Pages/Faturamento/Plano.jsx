import { useState } from 'react';
import { Head } from '@inertiajs/react';
import FaturamentoLayout from '@/Components/Faturamento/FaturamentoLayout';
import Modal from '@/Components/Faturamento/Modal';

export default function Plano() {
  const [planos, setPlanos] = useState([]);
  const [tiposCobranca, setTiposCobranca] = useState([]);
  const [convenios, setConvenios] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [formData, setFormData] = useState({
    descricao: '',
    id_tipo_cobranca: '',
    id_convenio: '',
  });
  const [nextId, setNextId] = useState(1);
  const [editingId, setEditingId] = useState(null);

  const handleOpenModal = (id = null) => {
    if (id) {
      const plano = planos.find((p) => p.id === id);
      if (plano) {
        setFormData({
          descricao: plano.descricao,
          id_tipo_cobranca: plano.id_tipo_cobranca.toString(),
          id_convenio: plano.id_convenio.toString(),
        });
        setEditingId(id);
      }
    } else {
      setFormData({
        descricao: '',
        id_tipo_cobranca: '',
        id_convenio: '',
      });
      setEditingId(null);
    }
    setIsModalOpen(true);
  };

  const handleCloseModal = () => {
    setIsModalOpen(false);
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (
      !formData.descricao.trim() ||
      !formData.id_tipo_cobranca ||
      !formData.id_convenio
    ) {
      alert('Por favor, preencha todos os campos');
      return;
    }

    const tipoCobranca = tiposCobranca.find(
      (t) => t.id === parseInt(formData.id_tipo_cobranca)
    );
    const convenio = convenios.find(
      (c) => c.id === parseInt(formData.id_convenio)
    );

    if (editingId) {
      setPlanos(
        planos.map((plano) =>
          plano.id === editingId
            ? {
                ...plano,
                descricao: formData.descricao,
                id_tipo_cobranca: parseInt(formData.id_tipo_cobranca),
                id_convenio: parseInt(formData.id_convenio),
                tipoCobranca,
                convenio,
              }
            : plano
        )
      );
    } else {
      const novoPlano = {
        id: nextId,
        descricao: formData.descricao,
        id_tipo_cobranca: parseInt(formData.id_tipo_cobranca),
        id_convenio: parseInt(formData.id_convenio),
        tipoCobranca,
        convenio,
      };
      setPlanos([...planos, novoPlano]);
      setNextId(nextId + 1);
    }

    setFormData({
      descricao: '',
      id_tipo_cobranca: '',
      id_convenio: '',
    });
    setEditingId(null);
    setIsModalOpen(false);
  };

  const handleDelete = (id) => {
    if (confirm('Tem certeza que deseja deletar este plano?')) {
      setPlanos(planos.filter((plano) => plano.id !== id));
    }
  };

  return (
    <FaturamentoLayout currentPage="plano">
      <Head title="Planos" />

      <div className="space-y-6">
        
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-gray-800 mb-1">
              Planos
            </h1>
            <p className="text-gray-500 text-sm">
              Gerencie os planos de saúde e suas coberturas
            </p>
          </div>
          <button
            onClick={() => handleOpenModal()}
            className="px-4 py-2 bg-[#00767F] text-white rounded-lg hover:bg-[#00989F] transition-colors font-medium shadow-sm"
          >
            + Novo Plano
          </button>
        </div>

        {planos.length === 0 ? (
          <div className="p-12 bg-white rounded-xl shadow-sm border border-gray-100 text-center">
            <div className="flex justify-center mb-4">
              <div className="p-4 bg-gray-50 rounded-full">
                <svg
                  className="w-10 h-10 text-gray-400"
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
            <h3 className="text-lg font-semibold text-gray-800 mb-2">
              Nenhum plano cadastrado
            </h3>
            <p className="text-gray-500 text-sm">
              Comece criando um novo plano clicando no botão acima.
            </p>
          </div>
        ) : (
          <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-50 border-b border-gray-100">
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                    ID
                  </th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                    Descrição
                  </th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                    Convênio
                  </th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                    Tipo de Cobrança
                  </th>
                  <th className="px-6 py-4 text-right text-sm font-semibold text-gray-700">
                    Ações
                  </th>
                </tr>
              </thead>
              <tbody>
                {planos.map((plano, index) => (
                  <tr
                    key={plano.id}
                    className={`border-b border-gray-50 hover:bg-gray-50 transition-colors ${
                      index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'
                    }`}
                  >
                    <td className="px-6 py-4 text-sm text-gray-900 font-medium">
                      #{plano.id}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-900">
                      {plano.descricao}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-900">
                      {plano.convenio?.nome || 'N/A'}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-900">
                      {plano.tipoCobranca?.descricao || 'N/A'}
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <button
                          onClick={() => handleOpenModal(plano.id)}
                          className="p-1.5 hover:bg-gray-200 rounded-md transition-colors text-blue-600"
                          title="Editar"
                        >
                          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                          </svg>
                        </button>
                        <button
                          onClick={() => handleDelete(plano.id)}
                          className="p-1.5 hover:bg-red-50 rounded-md transition-colors text-red-600"
                          title="Deletar"
                        >
                          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      <Modal
        isOpen={isModalOpen}
        onClose={handleCloseModal}
        title={editingId ? 'Editar Plano' : 'Novo Plano'}
      >
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-900 mb-2">
              Descrição *
            </label>
            <input
              type="text"
              value={formData.descricao}
              onChange={(e) =>
                setFormData({ ...formData, descricao: e.target.value })
              }
              className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-900 mb-2">
              Tipo de Cobrança *
            </label>
            {tiposCobranca.length === 0 ? (
              <div className="p-3 bg-gray-100 rounded-lg text-sm text-gray-600">
                Nenhum tipo de cobrança cadastrado. Crie um primeiro.
              </div>
            ) : (
              <select
                value={formData.id_tipo_cobranca}
                onChange={(e) =>
                  setFormData({
                    ...formData,
                    id_tipo_cobranca: e.target.value,
                  })
                }
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
              >
                <option value="">Selecione um tipo de cobrança</option>
                {tiposCobranca.map((tipo) => (
                  <option key={tipo.id} value={tipo.id}>
                    {tipo.descricao}
                  </option>
                ))}
              </select>
            )}
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-900 mb-2">
              Convênio *
            </label>
            {convenios.length === 0 ? (
              <div className="p-3 bg-gray-100 rounded-lg text-sm text-gray-600">
                Nenhum convênio cadastrado. Crie um primeiro.
              </div>
            ) : (
              <select
                value={formData.id_convenio}
                onChange={(e) =>
                  setFormData({ ...formData, id_convenio: e.target.value })
                }
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
              >
                <option value="">Selecione um convênio</option>
                {convenios.map((convenio) => (
                  <option key={convenio.id} value={convenio.id}>
                    {convenio.nome}
                  </option>
                ))}
              </select>
            )}
          </div>

          <div className="flex gap-3 pt-4">
            <button
              type="button"
              onClick={handleCloseModal}
              className="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-900 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              type="submit"
              className="flex-1 px-4 py-2 bg-[#00767F] text-white rounded-lg hover:bg-[#00989F] transition-colors font-medium"
            >
              Salvar
            </button>
          </div>
        </form>
      </Modal>
    </FaturamentoLayout>
  );
}