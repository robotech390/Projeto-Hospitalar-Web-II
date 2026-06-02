import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import FaturamentoLayout from '@/Components/Faturamento/FaturamentoLayout';
import Modal from '@/Components/Faturamento/Modal';


export default function Plano({ planos = [], tiposCobranca = [], convenios = [], }) {
  const [isModalOpen, setIsModalOpen] = useState(null);
  const [editingId, setEditingId] = useState(null);
  const [processing, setProcessing] = useState(false);
/*export default function Plano() {
  const [tiposCobranca] = useState([
    { id: 1, descricao: 'Convênio' },
    { id: 2, descricao: 'Particular' },
    { id: 3, descricao: 'Coparticipação' },
  ]);

  const [convenios] = useState([
    { id: 1, nome: 'Unimed' },
    { id: 2, nome: 'Bradesco Saúde' },
    { id: 3, nome: 'Particular' },
  ]);

  const [planos, setPlanos] = useState([
    {
      id: 1,
      descricao: 'Unimed Básico',
      id_tipo_cobranca: 1,
      id_convenio: 1,
      tipoCobranca: { id: 1, descricao: 'Convênio' },
      convenio: { id: 1, nome: 'Unimed' },
      cobre_consulta: true,
      cobre_remedio: false,
      cobre_exame: true,
      percentual_cobertura: 100,
    },
    {
      id: 2,
      descricao: 'Bradesco Empresarial',
      id_tipo_cobranca: 1,
      id_convenio: 2,
      tipoCobranca: { id: 1, descricao: 'Convênio' },
      convenio: { id: 2, nome: 'Bradesco Saúde' },
      cobre_consulta: true,
      cobre_remedio: true,
      cobre_exame: true,
      percentual_cobertura: 80,
    },
  ]);
  

   const [isModalOpen, setIsModalOpen] = useState(false);
*/
  const [formData, setFormData] = useState({
    descricao: '',
    id_tipo_cobranca: '',
    id_convenio: '',
    cobre_consulta: true,
    cobre_remedio: false,
    cobre_exame: true,
    percentual_cobertura: 100,
  });

/*  const [nextId, setNextId] = useState(3);
  const [editingId, setEditingId] = useState(null);
*/
  const handleOpenModal = (id = null) => {
    if (id) {
      const plano = planos.find((p) => p.id === id);

      if (plano) {
        setFormData({
          descricao: plano.descricao,
          id_tipo_cobranca: plano.id_tipo_cobranca.toString(),
          id_convenio: plano.id_convenio.toString(),
          cobre_consulta: plano.cobre_consulta,
          cobre_remedio: plano.cobre_remedio,
          cobre_exame: plano.cobre_exame,
          percentual_cobertura: plano.percentual_cobertura,
        });

        setEditingId(id);
      }
    } else {
      setFormData({
        descricao: '',
        id_tipo_cobranca: '',
        id_convenio: '',
        cobre_consulta: true,
        cobre_remedio: false,
        cobre_exame: true,
        percentual_cobertura: 100,
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
      alert('Por favor, preencha os campos obrigatórios.');
      return;
    }

    const tipoCobranca = tiposCobranca.find(
      (t) => t.id === parseInt(formData.id_tipo_cobranca)
    );

    const convenio = convenios.find(
      (c) => c.id === parseInt(formData.id_convenio)
    );

    const dadosPlano = {
      descricao: formData.descricao,
      id_tipo_cobranca: parseInt(formData.id_tipo_cobranca),
      id_convenio: parseInt(formData.id_convenio),
      tipoCobranca,
      convenio,
      cobre_consulta: formData.cobre_consulta,
      cobre_remedio: formData.cobre_remedio,
      cobre_exame: formData.cobre_exame,
      percentual_cobertura: Number(formData.percentual_cobertura) || 0,
    };

    if (editingId) {
      setPlanos(
        planos.map((plano) =>
          plano.id === editingId
            ? {
                ...plano,
                ...dadosPlano,
              }
            : plano
        )
      );
    } else {
      const novoPlano = {
        id: nextId,
        ...dadosPlano,
      };

      setPlanos([...planos, novoPlano]);
      setNextId(nextId + 1);
    }

    setFormData({
      descricao: '',
      id_tipo_cobranca: '',
      id_convenio: '',
      cobre_consulta: true,
      cobre_remedio: false,
      cobre_exame: true,
      percentual_cobertura: 100,
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
        <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#e1f2ef] text-[#00767F] text-xs font-semibold mb-3">
              Gestão de Planos
            </div>

            <h1 className="text-2xl font-bold text-slate-800">
              Planos
            </h1>

            <p className="text-slate-500 text-sm mt-1">
              Cadastre planos de saúde, vincule ao convênio e defina regras de cobertura para faturamento.
            </p>
          </div>

          <button
            onClick={() => handleOpenModal()}
            className="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#00767F] text-white rounded-xl hover:bg-[#00989F] transition-colors font-semibold shadow-sm"
          >
            <span className="text-lg leading-none">+</span>
            Novo Plano
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <ResumoCard
            titulo="Planos cadastrados"
            valor={planos.length}
            descricao="Total de planos disponíveis"
          />

          <ResumoCard
            titulo="Cobrem consulta"
            valor={planos.filter((plano) => plano.cobre_consulta).length}
            descricao="Planos com consulta coberta"
          />

          <ResumoCard
            titulo="Cobrem remédio"
            valor={planos.filter((plano) => plano.cobre_remedio).length}
            descricao="Planos com medicamento coberto"
          />

          <ResumoCard
            titulo="Cobrem exame"
            valor={planos.filter((plano) => plano.cobre_exame).length}
            descricao="Planos com exame coberto"
          />
        </div>

        {planos.length === 0 ? (
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
              Nenhum plano cadastrado
            </h3>

            <p className="text-slate-500 text-sm mb-5">
              Comece criando um plano e definindo as regras de cobertura.
            </p>

            <button
              onClick={() => handleOpenModal()}
              className="px-5 py-3 bg-[#00767F] text-white rounded-xl hover:bg-[#00989F] transition-colors font-semibold"
            >
              Cadastrar primeiro plano
            </button>
          </div>
        ) : (
          <div className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div className="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
              <div>
                <h2 className="font-semibold text-slate-800">
                  Lista de Planos
                </h2>

                <p className="text-xs text-slate-500 mt-1">
                  As regras abaixo são utilizadas no cálculo da conta hospitalar.
                </p>
              </div>

              <span className="text-xs px-3 py-1 rounded-full bg-[#e1f2ef] text-[#00767F] font-semibold">
                {planos.length} registro(s)
              </span>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="bg-slate-50 border-b border-slate-100">
                    <th className="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                      Plano
                    </th>

                    <th className="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                      Convênio
                    </th>

                    <th className="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                      Tipo
                    </th>

                    <th className="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                      Coberturas
                    </th>

                    <th className="px-6 py-4 text-left text-sm font-semibold text-slate-700">
                      Percentual
                    </th>

                    <th className="px-6 py-4 text-right text-sm font-semibold text-slate-700">
                      Ações
                    </th>
                  </tr>
                </thead>

                <tbody>
                  {planos.map((plano, index) => (
                    <tr
                      key={plano.id}
                      className={`border-b border-slate-100 hover:bg-slate-50 transition-colors ${
                        index % 2 === 0 ? 'bg-white' : 'bg-slate-50/40'
                      }`}
                    >
                      <td className="px-6 py-4">
                        <div className="font-semibold text-slate-800">
                          {plano.descricao}
                        </div>

                        <div className="text-xs text-slate-400 mt-1">
                          ID #{plano.id}
                        </div>
                      </td>

                      <td className="px-6 py-4 text-sm text-slate-700">
                        {plano.convenio?.nome || 'N/A'}
                      </td>

                      <td className="px-6 py-4">
                        <span className="inline-flex px-3 py-1 rounded-full bg-[#e1f2ef] text-[#00767F] text-xs font-semibold">
                          {plano.tipoCobranca?.descricao || 'N/A'}
                        </span>
                      </td>

                      <td className="px-6 py-4">
                        <div className="flex flex-wrap gap-2">
                          <CoberturaBadge ativo={plano.cobre_consulta}>
                            Consulta
                          </CoberturaBadge>

                          <CoberturaBadge ativo={plano.cobre_remedio}>
                            Remédio
                          </CoberturaBadge>

                          <CoberturaBadge ativo={plano.cobre_exame}>
                            Exame
                          </CoberturaBadge>
                        </div>
                      </td>

                      <td className="px-6 py-4 text-sm font-semibold text-slate-700">
                        {plano.percentual_cobertura}%
                      </td>

                      <td className="px-6 py-4 text-right">
                        <div className="flex items-center justify-end gap-2">
                          <button
                            onClick={() => handleOpenModal(plano.id)}
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
                            onClick={() => handleDelete(plano.id)}
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
        title={editingId ? 'Editar Plano' : 'Novo Plano'}
      >
        <form onSubmit={handleSubmit} className="space-y-4 max-h-96 overflow-y-auto pr-1">
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
              placeholder="Ex: Unimed Básico"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-2">
              Tipo de Cobrança *
            </label>

            {tiposCobranca.length === 0 ? (
              <div className="p-3 bg-slate-100 rounded-xl text-sm text-slate-600">
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
                className="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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
            <label className="block text-sm font-medium text-slate-700 mb-2">
              Convênio *
            </label>

            {convenios.length === 0 ? (
              <div className="p-3 bg-slate-100 rounded-xl text-sm text-slate-600">
                Nenhum convênio cadastrado. Crie um primeiro.
              </div>
            ) : (
              <select
                value={formData.id_convenio}
                onChange={(e) =>
                  setFormData({ ...formData, id_convenio: e.target.value })
                }
                className="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
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

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-2">
              Percentual de cobertura
            </label>

            <input
              type="number"
              min="0"
              max="100"
              value={formData.percentual_cobertura}
              onChange={(e) =>
                setFormData({
                  ...formData,
                  percentual_cobertura: e.target.value,
                })
              }
              className="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#00767F]"
              placeholder="100"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-slate-700 mb-3">
              Regras de cobertura
            </label>

            <div className="grid grid-cols-1 gap-3">
              <CoberturaCheckbox
                label="Cobre consulta"
                checked={formData.cobre_consulta}
                onChange={(checked) =>
                  setFormData({ ...formData, cobre_consulta: checked })
                }
              />

              <CoberturaCheckbox
                label="Cobre remédio"
                checked={formData.cobre_remedio}
                onChange={(checked) =>
                  setFormData({ ...formData, cobre_remedio: checked })
                }
              />

              <CoberturaCheckbox
                label="Cobre exame"
                checked={formData.cobre_exame}
                onChange={(checked) =>
                  setFormData({ ...formData, cobre_exame: checked })
                }
              />
            </div>
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

function CoberturaBadge({ ativo, children }) {
  return (
    <span
      className={`inline-flex px-3 py-1 rounded-full text-xs font-semibold ${
        ativo
          ? 'bg-emerald-50 text-emerald-700 border border-emerald-100'
          : 'bg-slate-100 text-slate-400 border border-slate-200'
      }`}
    >
      {children}
    </span>
  );
}

function CoberturaCheckbox({ label, checked, onChange }) {
  return (
    <label className="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 cursor-pointer">
      <input
        type="checkbox"
        checked={checked}
        onChange={(e) => onChange(e.target.checked)}
        className="rounded border-slate-300 text-[#00767F] focus:ring-[#00767F]"
      />

      <span className="text-sm font-medium text-slate-700">
        {label}
      </span>
    </label>
  );
}