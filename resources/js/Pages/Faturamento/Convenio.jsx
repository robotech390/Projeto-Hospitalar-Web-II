import { useState } from 'react';
import FaturamentoLayout from '@/Components/Faturamento/FaturamentoLayout';
import Modal from '@/Components/Faturamento/Modal';
import { maskCNPJ, maskPhone, maskCEP, maskState } from '@/lib/masks';
import { Head, router } from '@inertiajs/react';

export default function Convenio({ convenios = [] }) {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [formData, setFormData] = useState({
    nome: '',
    cnpj: '',
    telefone: '',
    email: '',
    rua: '',
    numero: '',
    cidade: '',
    estado: '',
    cep: '',
  });
  
  const [editingId, setEditingId] = useState(null);

  const handleOpenModal = (id = null) => {
    if (id) {
      const convenio = convenios.find((c) => c.id === id);
      if (convenio) {
        setFormData({
          nome: convenio.nome || '',
          cnpj: convenio.cnpj || '',
          telefone: convenio.telefone || '',
          email: convenio.email || '',
          rua: convenio.endereco?.logradouro || '',
          numero: convenio.endereco?.numero || '',
          cidade: convenio.endereco?.cidade || '',
          estado: convenio.endereco?.estado || '',
          cep: convenio.endereco?.cep || '',
        });
        setEditingId(id);
      }
    } else {
      setFormData({
        nome: '',
        cnpj: '',
        telefone: '',
        email: '',
        rua: '',
        numero: '',
        cidade: '',
        estado: '',
        cep: '',
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

    if (!formData.nome.trim() || !formData.cnpj.trim()) {
      alert('Por favor, preencha os campos obrigatórios');
      return;
    }
    if (editingId) {
      router.put(`/faturamento/convenio/${editingId}`, formData, {
        onSuccess: () => setIsModalOpen(false),
      });
    } else {
      router.post('/faturamento/convenio', formData, {
        onSuccess: () => setIsModalOpen(false),
      });
    }
  }

    const handleDelete = (id) => {
      if (confirm('Tem certeza que deseja deletar este convênio?')) {
        router.delete(`/faturamento/convenio/${id}`);
      }
    };

    return (
      <FaturamentoLayout
        currentPage="convenio"
        header={
          <h2 className="text-xl font-semibold leading-tight text-gray-800">
            Convênios
          </h2>
        }
      >
        <Head title="Convênios" />

        <div className="space-y-6">

          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-3xl font-bold text-gray-900 mb-2">
                Convênios
              </h1>
              <p className="text-gray-600">
                Gerencie os convênios de saúde cadastrados no sistema
              </p>
            </div>
            <button
              onClick={() => handleOpenModal()}
              className="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors font-medium"
            >
              + Novo Convênio
            </button>
          </div>


          {convenios.length === 0 ? (
            <div className="p-12 bg-white rounded-lg border border-gray-200 text-center">
              <div className="flex justify-center mb-4">
                <div className="p-4 bg-gray-100 rounded-full">
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
              <h3 className="text-lg font-semibold text-gray-900 mb-2">
                Nenhum convênio cadastrado
              </h3>
              <p className="text-gray-600">
                Comece criando um novo convênio clicando no botão acima.
              </p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {convenios.map((convenio) => (
                <div
                  key={convenio.id}
                  className="p-6 bg-white rounded-lg border border-gray-200 hover:shadow-lg transition-all"
                >
                  <div className="flex items-start justify-between mb-4">
                    <div className="flex gap-2">
                      <button
                        onClick={() => handleOpenModal(convenio.id)}
                        className="p-2 hover:bg-gray-200 rounded-lg transition-colors text-blue-600"
                        title="Editar"
                      >
                        Editar
                      </button>
                      <button
                        onClick={() => handleDelete(convenio.id)}
                        className="p-2 hover:bg-red-100 rounded-lg transition-colors text-red-600"
                        title="Deletar"
                      >
                        X
                      </button>
                    </div>
                  </div>

                  <h3 className="text-lg font-semibold text-gray-900 mb-1">
                    {convenio.nome}
                  </h3>
                  <p className="text-xs text-gray-500 mb-4 font-mono">
                    {convenio.cnpj}
                  </p>

                  <div className="space-y-2 mb-4 text-sm text-gray-600">
                    <p>{convenio.telefone}</p>
                    <p className="truncate">{convenio.email}</p>
                    {convenio.endereco && (
                      <p>
                        {convenio.endereco.logradouro}, {convenio.endereco.numero}
                        <br />
                        {convenio.endereco.cidade} - {convenio.endereco.estado}
                      </p>
                    )}
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Modal de Cadastro/Edição */}
        <Modal
          isOpen={isModalOpen}
          onClose={handleCloseModal}
          title={editingId ? 'Editar Convênio' : 'Novo Convênio'}
        >
          <form onSubmit={handleSubmit} className="space-y-4 max-h-96 overflow-y-auto">
            <div>
              <label className="block text-sm font-medium text-gray-900 mb-2">
                Nome *
              </label>
              <input
                type="text"
                value={formData.nome}
                onChange={(e) =>
                  setFormData({ ...formData, nome: e.target.value })
                }
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-600"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-900 mb-2">
                CNPJ *
              </label>
              <input
                type="text"
                value={formData.cnpj}
                onChange={(e) =>
                  setFormData({ ...formData, cnpj: maskCNPJ(e.target.value) })
                }
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-teal-600"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-900 mb-2">
                Telefone
              </label>
              <input
                type="text"
                value={formData.telefone}
                onChange={(e) =>
                  setFormData({
                    ...formData,
                    telefone: maskPhone(e.target.value),
                  })
                }
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-teal-600"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-900 mb-2">
                Email
              </label>
              <input
                type="email"
                value={formData.email}
                onChange={(e) =>
                  setFormData({ ...formData, email: e.target.value })
                }
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-600"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-900 mb-2">
                Rua
              </label>
              <input
                type="text"
                value={formData.rua}
                onChange={(e) =>
                  setFormData({ ...formData, rua: e.target.value })
                }
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-600"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-900 mb-2">
                Número
              </label>
              <input
                type="text"
                value={formData.numero}
                onChange={(e) =>
                  setFormData({ ...formData, numero: e.target.value })
                }
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-600"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-900 mb-2">
                Cidade
              </label>
              <input
                type="text"
                value={formData.cidade}
                onChange={(e) =>
                  setFormData({ ...formData, cidade: e.target.value })
                }
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-600"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-900 mb-2">
                Estado
              </label>
              <input
                type="text"
                value={formData.estado}
                onChange={(e) =>
                  setFormData({ ...formData, estado: maskState(e.target.value) })
                }
                maxLength="2"
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-teal-600"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-900 mb-2">
                CEP
              </label>
              <input
                type="text"
                value={formData.cep}
                onChange={(e) =>
                  setFormData({ ...formData, cep: maskCEP(e.target.value) })
                }
                className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-teal-600"
              />
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
                className="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors font-medium"
              >
                Salvar
              </button>
            </div>
          </form>
        </Modal>
      </FaturamentoLayout>
    );
  }
