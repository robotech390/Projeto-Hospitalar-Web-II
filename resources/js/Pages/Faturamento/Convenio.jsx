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
    <FaturamentoLayout currentPage="convenio">
      <Head title="Convênios" />

      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-gray-800 mb-1">
              Convênios
            </h1>
            <p className="text-gray-500 text-sm">
              Gerencie os convênios de saúde cadastrados no sistema
            </p>
          </div>
          <button
            onClick={() => handleOpenModal()}
            className="px-4 py-2 bg-[#00767F] text-white rounded-lg hover:bg-[#00989F] transition-colors font-medium shadow-sm"
          >
            + Novo Convênio
          </button>
        </div>

        {convenios.length === 0 ? (
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
              Nenhum convênio cadastrado
            </h3>
            <p className="text-gray-500 text-sm">
              Comece criando um novo convênio clicando no botão acima.
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {convenios.map((convenio) => (
              <div
                key={convenio.id}
                className="p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-all"
              >
                <div className="flex items-start justify-between mb-4">
                  <h3 className="text-lg font-bold text-gray-800">
                    {convenio.nome}
                  </h3>
                  <div className="flex gap-2">
                    <button
                      onClick={() => handleOpenModal(convenio.id)}
                      className="p-1.5 hover:bg-gray-100 rounded-md transition-colors text-blue-600"
                      title="Editar"
                    >
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                    </button>
                    <button
                      onClick={() => handleDelete(convenio.id)}
                      className="p-1.5 hover:bg-red-50 rounded-md transition-colors text-red-600"
                      title="Deletar"
                    >
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>

                <p className="text-xs text-[#00767F] mb-4 font-mono font-semibold bg-teal-50 inline-block px-2 py-1 rounded">
                  {convenio.cnpj}
                </p>

                <div className="space-y-1 mb-4 text-sm text-gray-600">
                  <div className="flex items-center gap-2">
                    <svg className="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span>{convenio.telefone}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <svg className="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span className="truncate">{convenio.email}</span>
                  </div>
                  {convenio.endereco && (
                    <div className="flex items-start gap-2 pt-2 mt-2 border-t border-gray-100 text-xs">
                      <svg className="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <span>
                        {convenio.endereco.logradouro}, {convenio.endereco.numero}
                        <br />
                        {convenio.endereco.cidade} - {convenio.endereco.estado}
                      </span>
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>

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
              className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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
              className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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
              className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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
              className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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
              className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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
              className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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
              className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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
              className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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
              className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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