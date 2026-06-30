import { useMemo, useState } from 'react';
import {
  Search,
  FileText,
  Download,
  Lock,
  CheckCircle2,
  Building2,
  User,
  Receipt,
  AlertTriangle,
  X,
} from 'lucide-react';

// ----------------------------------------------------------------------------
// Dados de exemplo (Mock). Em produção viriam da API de Faturamento.
// Cada conta possui itens classificados por categoria (diárias, medicamentos,
// exames, procedimentos e materiais).
// ----------------------------------------------------------------------------
const CONTAS_INICIAIS = [
  {
    id: 1,
    numero: '2026-000145',
    paciente: 'Maria Silva',
    sexo: 'Feminino',
    idade: 45,
    convenio: 'Unimed',
    leito: 'Ala A - Apto 203',
    dataEntrada: '2026-06-20',
    status: 'aberta',
    itens: [
      { categoria: 'Diárias', descricao: 'Diária Apartamento', qtd: 9, valorUnit: 480.0 },
      { categoria: 'Medicamentos', descricao: 'Dipirona 1g IV', qtd: 12, valorUnit: 8.5 },
      { categoria: 'Medicamentos', descricao: 'Omeprazol 40mg', qtd: 9, valorUnit: 6.2 },
      { categoria: 'Exames', descricao: 'Hemograma Completo', qtd: 3, valorUnit: 45.0 },
      { categoria: 'Exames', descricao: 'Tomografia de Tórax', qtd: 1, valorUnit: 650.0 },
      { categoria: 'Procedimentos', descricao: 'Curativo Especial', qtd: 5, valorUnit: 70.0 },
      { categoria: 'Materiais', descricao: 'Kit Soro Fisiológico', qtd: 10, valorUnit: 12.0 },
    ],
  },
  {
    id: 2,
    numero: '2026-000146',
    paciente: 'João Santos',
    sexo: 'Masculino',
    idade: 32,
    convenio: 'Unimed',
    leito: 'Ala B - Enf. 110',
    dataEntrada: '2026-06-24',
    status: 'aberta',
    itens: [
      { categoria: 'Diárias', descricao: 'Diária Enfermaria', qtd: 5, valorUnit: 280.0 },
      { categoria: 'Medicamentos', descricao: 'Amoxicilina 500mg', qtd: 15, valorUnit: 4.3 },
      { categoria: 'Exames', descricao: 'Raio-X Tórax', qtd: 2, valorUnit: 90.0 },
      { categoria: 'Procedimentos', descricao: 'Inalação', qtd: 8, valorUnit: 25.0 },
    ],
  },
  {
    id: 3,
    numero: '2026-000147',
    paciente: 'Ana Oliveira',
    sexo: 'Feminino',
    idade: 28,
    convenio: 'Bradesco Saúde',
    leito: 'Ala A - Apto 207',
    dataEntrada: '2026-06-22',
    status: 'aberta',
    itens: [
      { categoria: 'Diárias', descricao: 'Diária Apartamento', qtd: 7, valorUnit: 480.0 },
      { categoria: 'Medicamentos', descricao: 'Tramadol 100mg', qtd: 6, valorUnit: 14.0 },
      { categoria: 'Exames', descricao: 'Ressonância Magnética', qtd: 1, valorUnit: 980.0 },
      { categoria: 'Materiais', descricao: 'Sonda Vesical', qtd: 1, valorUnit: 35.0 },
    ],
  },
  {
    id: 4,
    numero: '2026-000148',
    paciente: 'Carlos Pereira',
    sexo: 'Masculino',
    idade: 60,
    convenio: 'Bradesco Saúde',
    leito: 'UTI - Box 03',
    dataEntrada: '2026-06-18',
    status: 'fechada',
    itens: [
      { categoria: 'Diárias', descricao: 'Diária UTI', qtd: 11, valorUnit: 1850.0 },
      { categoria: 'Medicamentos', descricao: 'Noradrenalina', qtd: 22, valorUnit: 38.0 },
      { categoria: 'Procedimentos', descricao: 'Ventilação Mecânica', qtd: 11, valorUnit: 420.0 },
      { categoria: 'Exames', descricao: 'Gasometria Arterial', qtd: 18, valorUnit: 55.0 },
    ],
  },
  {
    id: 5,
    numero: '2026-000149',
    paciente: 'Paulo Mendes',
    sexo: 'Masculino',
    idade: 51,
    convenio: 'Particular',
    leito: 'Ala C - Apto 305',
    dataEntrada: '2026-06-26',
    status: 'aberta',
    itens: [
      { categoria: 'Diárias', descricao: 'Diária Apartamento', qtd: 3, valorUnit: 480.0 },
      { categoria: 'Exames', descricao: 'Endoscopia Digestiva', qtd: 1, valorUnit: 720.0 },
      { categoria: 'Procedimentos', descricao: 'Sedação', qtd: 1, valorUnit: 350.0 },
    ],
  },
];

// Formatação de moeda no padrão brasileiro.
const moeda = (valor) =>
  valor.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

const dataBR = (iso) => new Date(iso + 'T00:00:00').toLocaleDateString('pt-BR');

// Soma total dos itens de uma conta.
const totalConta = (conta) =>
  conta.itens.reduce((acc, item) => acc + item.qtd * item.valorUnit, 0);

// Dispara o download de um arquivo CSV no navegador.
const baixarCSV = (nomeArquivo, linhas) => {
  const conteudo = linhas
    .map((colunas) =>
      colunas
        .map((c) => `"${String(c).replace(/"/g, '""')}"`)
        .join(';')
    )
    .join('\n');
  // BOM para o Excel reconhecer acentuação UTF-8.
  const blob = new Blob(['﻿' + conteudo], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = nomeArquivo;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
};

export default function ContaHospitalar() {
  const [abaAtiva, setAbaAtiva] = useState('paciente'); // 'paciente' | 'convenio'
  const [contas, setContas] = useState(CONTAS_INICIAIS);

  // --- Aba Conta do Paciente ---
  const [busca, setBusca] = useState('');
  const [contaSelecionadaId, setContaSelecionadaId] = useState(null);
  const [confirmarFechamento, setConfirmarFechamento] = useState(false);

  // --- Aba Fatura por Convênio ---
  const [convenioSelecionado, setConvenioSelecionado] = useState('');

  const contaSelecionada = useMemo(
    () => contas.find((c) => c.id === contaSelecionadaId) || null,
    [contas, contaSelecionadaId]
  );

  // Filtro da busca de pacientes (nome ou número da conta).
  const resultados = useMemo(() => {
    const termo = busca.trim().toLowerCase();
    if (!termo) return contas;
    return contas.filter(
      (c) =>
        c.paciente.toLowerCase().includes(termo) ||
        c.numero.toLowerCase().includes(termo) ||
        c.convenio.toLowerCase().includes(termo)
    );
  }, [busca, contas]);

  // Lista de convênios distintos para o seletor.
  const convenios = useMemo(
    () => [...new Set(contas.map((c) => c.convenio))].sort(),
    [contas]
  );

  // Contas do convênio escolhido (agrupadas por pessoa = cada conta é de um paciente).
  const contasDoConvenio = useMemo(
    () => contas.filter((c) => c.convenio === convenioSelecionado),
    [contas, convenioSelecionado]
  );

  const totalConvenio = useMemo(
    () => contasDoConvenio.reduce((acc, c) => acc + totalConta(c), 0),
    [contasDoConvenio]
  );

  // ----- Ações -----
  function fecharConta() {
    if (!contaSelecionada) return;
    setContas((prev) =>
      prev.map((c) =>
        c.id === contaSelecionada.id ? { ...c, status: 'fechada' } : c
      )
    );
    setConfirmarFechamento(false);
  }

  function exportarContaPaciente() {
    if (!contaSelecionada) return;
    const linhas = [
      ['Conta Hospitalar', contaSelecionada.numero],
      ['Paciente', contaSelecionada.paciente],
      ['Convênio', contaSelecionada.convenio],
      ['Leito', contaSelecionada.leito],
      ['Entrada', dataBR(contaSelecionada.dataEntrada)],
      [],
      ['Categoria', 'Descrição', 'Qtd', 'Valor Unit.', 'Subtotal'],
      ...contaSelecionada.itens.map((i) => [
        i.categoria,
        i.descricao,
        i.qtd,
        moeda(i.valorUnit),
        moeda(i.qtd * i.valorUnit),
      ]),
      [],
      ['', '', '', 'TOTAL', moeda(totalConta(contaSelecionada))],
    ];
    baixarCSV(`conta-${contaSelecionada.numero}.csv`, linhas);
  }

  function exportarFaturaConvenio() {
    if (!convenioSelecionado) return;
    const linhas = [
      ['Fatura por Convênio', convenioSelecionado],
      ['Emitida em', new Date().toLocaleDateString('pt-BR')],
      [],
      ['Paciente', 'Conta', 'Categoria', 'Descrição', 'Qtd', 'Valor Unit.', 'Subtotal'],
    ];
    contasDoConvenio.forEach((c) => {
      c.itens.forEach((i) => {
        linhas.push([
          c.paciente,
          c.numero,
          i.categoria,
          i.descricao,
          i.qtd,
          moeda(i.valorUnit),
          moeda(i.qtd * i.valorUnit),
        ]);
      });
      linhas.push(['', '', '', '', '', 'Subtotal ' + c.paciente, moeda(totalConta(c))]);
    });
    linhas.push([]);
    linhas.push(['', '', '', '', '', 'TOTAL GERAL', moeda(totalConvenio)]);
    baixarCSV(`fatura-${convenioSelecionado.replace(/\s+/g, '-').toLowerCase()}.csv`, linhas);
  }

  return (
    <div className="animate-fade-in">
      {/* Cabeçalho da página */}
      <div className="mb-6">
        <h1 className="text-2xl font-bold text-gray-800 flex items-center gap-2">
          <Receipt className="text-brand" size={26} /> Conta Hospitalar
        </h1>
        <p className="text-sm text-gray-500">
          Busque a conta do paciente para conferir, exportar e efetuar o fechamento.
        </p>
      </div>

      {/* Abas */}
      <div className="flex border-b border-gray-200 mb-6">
        <button
          onClick={() => setAbaAtiva('paciente')}
          className={`flex items-center px-4 py-3 text-sm font-medium border-b-2 transition-colors ${
            abaAtiva === 'paciente'
              ? 'border-brand text-brand'
              : 'border-transparent text-gray-500 hover:text-gray-700'
          }`}
        >
          <User size={16} className="mr-2" /> Conta do Paciente
        </button>
        <button
          onClick={() => setAbaAtiva('convenio')}
          className={`flex items-center px-4 py-3 text-sm font-medium border-b-2 transition-colors ${
            abaAtiva === 'convenio'
              ? 'border-brand text-brand'
              : 'border-transparent text-gray-500 hover:text-gray-700'
          }`}
        >
          <Building2 size={16} className="mr-2" /> Fatura por Convênio
        </button>
      </div>

      {/* ===================== ABA: CONTA DO PACIENTE ===================== */}
      {abaAtiva === 'paciente' && (
        <div className="flex gap-6">
          {/* Coluna de busca / resultados */}
          <div className="w-1/3 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-[calc(100vh-14rem)]">
            <div className="p-4 border-b border-gray-100">
              <div className="flex items-center bg-gray-100 px-3 py-2 rounded-lg">
                <Search size={18} className="text-gray-400 mr-2" />
                <input
                  type="text"
                  value={busca}
                  onChange={(e) => setBusca(e.target.value)}
                  placeholder="Buscar por paciente, conta ou convênio..."
                  className="bg-transparent border-none outline-none w-full text-sm placeholder-gray-400"
                />
              </div>
            </div>

            <div className="overflow-y-auto flex-1 p-3 space-y-2">
              {resultados.length === 0 && (
                <p className="text-center text-sm text-gray-400 py-10">
                  Nenhuma conta encontrada.
                </p>
              )}
              {resultados.map((conta) => (
                <button
                  key={conta.id}
                  onClick={() => {
                    setContaSelecionadaId(conta.id);
                    setConfirmarFechamento(false);
                  }}
                  className={`w-full text-left p-4 rounded-lg border transition-colors ${
                    conta.id === contaSelecionadaId
                      ? 'bg-brand/5 border-brand/20'
                      : 'bg-white border-gray-50 hover:bg-gray-50'
                  }`}
                >
                  <div className="flex justify-between items-start mb-1">
                    <p className="font-bold text-gray-800">{conta.paciente}</p>
                    {conta.status === 'fechada' ? (
                      <span className="text-xs font-semibold px-2 py-0.5 rounded bg-gray-200 text-gray-600">
                        Fechada
                      </span>
                    ) : (
                      <span className="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-50 text-emerald-600">
                        Aberta
                      </span>
                    )}
                  </div>
                  <p className="text-xs text-gray-500">Conta {conta.numero}</p>
                  <p className="text-xs text-gray-400">{conta.convenio} • {conta.leito}</p>
                </button>
              ))}
            </div>
          </div>

          {/* Coluna de detalhe da conta */}
          <div className="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-[calc(100vh-14rem)]">
            {!contaSelecionada ? (
              <div className="flex-1 flex flex-col items-center justify-center text-gray-400">
                <FileText size={48} className="mb-3 opacity-50" />
                <p className="text-sm">Selecione uma conta para visualizar os detalhes.</p>
              </div>
            ) : (
              <>
                {/* Cabeçalho da conta */}
                <div className="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-xl">
                  <div className="flex items-center space-x-4">
                    <div className="w-12 h-12 rounded-full bg-brand text-white flex items-center justify-center font-bold text-lg">
                      {contaSelecionada.paciente
                        .split(' ')
                        .map((n) => n[0])
                        .slice(0, 2)
                        .join('')}
                    </div>
                    <div>
                      <h2 className="text-xl font-bold text-gray-800">
                        {contaSelecionada.paciente}
                      </h2>
                      <p className="text-sm text-gray-500">
                        {contaSelecionada.sexo} • {contaSelecionada.idade} anos • Conta{' '}
                        {contaSelecionada.numero}
                      </p>
                      <p className="text-xs text-gray-400">
                        {contaSelecionada.convenio} • {contaSelecionada.leito} • Entrada{' '}
                        {dataBR(contaSelecionada.dataEntrada)}
                      </p>
                    </div>
                  </div>

                  <div className="flex items-center gap-2">
                    <button
                      onClick={exportarContaPaciente}
                      className="flex items-center px-4 py-2 border border-brand text-brand font-bold rounded-lg text-sm hover:bg-brand/5 transition-colors"
                    >
                      <Download size={16} className="mr-2" /> Exportar
                    </button>
                    {contaSelecionada.status === 'aberta' ? (
                      <button
                        onClick={() => setConfirmarFechamento(true)}
                        className="flex items-center px-4 py-2 bg-brand text-white font-bold rounded-lg text-sm hover:bg-brand-dark transition-colors"
                      >
                        <Lock size={16} className="mr-2" /> Fechar Conta
                      </button>
                    ) : (
                      <span className="flex items-center px-4 py-2 bg-gray-100 text-gray-500 font-bold rounded-lg text-sm">
                        <CheckCircle2 size={16} className="mr-2" /> Conta Fechada
                      </span>
                    )}
                  </div>
                </div>

                {/* Itens da conta */}
                <div className="flex-1 overflow-y-auto p-6">
                  <div className="border border-gray-100 rounded-lg overflow-hidden">
                    <table className="w-full text-sm">
                      <thead>
                        <tr className="bg-gray-50 text-gray-600 text-left">
                          <th className="p-3 font-semibold">Categoria</th>
                          <th className="p-3 font-semibold">Descrição</th>
                          <th className="p-3 font-semibold text-center">Qtd</th>
                          <th className="p-3 font-semibold text-right">Valor Unit.</th>
                          <th className="p-3 font-semibold text-right">Subtotal</th>
                        </tr>
                      </thead>
                      <tbody>
                        {contaSelecionada.itens.map((item, idx) => (
                          <tr key={idx} className="border-t border-gray-50">
                            <td className="p-3">
                              <span className="text-xs font-medium px-2 py-1 rounded bg-brand/5 text-brand">
                                {item.categoria}
                              </span>
                            </td>
                            <td className="p-3 text-gray-700">{item.descricao}</td>
                            <td className="p-3 text-center text-gray-600">{item.qtd}</td>
                            <td className="p-3 text-right text-gray-600">
                              {moeda(item.valorUnit)}
                            </td>
                            <td className="p-3 text-right font-medium text-gray-800">
                              {moeda(item.qtd * item.valorUnit)}
                            </td>
                          </tr>
                        ))}
                      </tbody>
                      <tfoot>
                        <tr className="border-t border-gray-100 bg-gray-50/70">
                          <td colSpan={4} className="p-3 text-right font-bold text-gray-700">
                            Total da Conta
                          </td>
                          <td className="p-3 text-right font-bold text-brand text-base">
                            {moeda(totalConta(contaSelecionada))}
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </>
            )}
          </div>
        </div>
      )}

      {/* ===================== ABA: FATURA POR CONVÊNIO ===================== */}
      {abaAtiva === 'convenio' && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          {/* Seletor de convênio */}
          <div className="flex flex-wrap items-end gap-3 mb-6">
            <div>
              <label className="block text-sm font-bold text-gray-700 mb-1">
                Convênio
              </label>
              <select
                value={convenioSelecionado}
                onChange={(e) => setConvenioSelecionado(e.target.value)}
                className="p-2.5 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm min-w-64"
              >
                <option value="">Selecione um convênio...</option>
                {convenios.map((c) => (
                  <option key={c} value={c}>
                    {c}
                  </option>
                ))}
              </select>
            </div>
            {convenioSelecionado && (
              <button
                onClick={exportarFaturaConvenio}
                className="flex items-center px-4 py-2.5 border border-brand text-brand font-bold rounded-lg text-sm hover:bg-brand/5 transition-colors"
              >
                <Download size={16} className="mr-2" /> Exportar Fatura
              </button>
            )}
          </div>

          {!convenioSelecionado ? (
            <div className="text-center text-gray-400 py-16">
              <Building2 size={48} className="mx-auto mb-3 opacity-50" />
              <p className="text-sm">
                Selecione um convênio para ver os gastos detalhados, agrupados por paciente.
              </p>
            </div>
          ) : contasDoConvenio.length === 0 ? (
            <p className="text-center text-sm text-gray-400 py-16">
              Nenhuma conta encontrada para este convênio.
            </p>
          ) : (
            <div className="space-y-6">
              {/* Resumo */}
              <div className="flex flex-wrap gap-4">
                <div className="flex-1 min-w-40 bg-brand/5 rounded-lg p-4">
                  <p className="text-xs text-gray-500">Pacientes</p>
                  <p className="text-2xl font-bold text-brand">{contasDoConvenio.length}</p>
                </div>
                <div className="flex-1 min-w-40 bg-brand/5 rounded-lg p-4">
                  <p className="text-xs text-gray-500">Total da Fatura</p>
                  <p className="text-2xl font-bold text-brand">{moeda(totalConvenio)}</p>
                </div>
              </div>

              {/* Um bloco por pessoa, com seus gastos detalhados */}
              {contasDoConvenio.map((conta) => (
                <div
                  key={conta.id}
                  className="border border-gray-100 rounded-lg overflow-hidden"
                >
                  <div className="flex items-center justify-between bg-gray-50 px-4 py-3 border-b border-gray-100">
                    <div className="flex items-center gap-3">
                      <div className="w-9 h-9 rounded-full bg-brand text-white flex items-center justify-center font-bold text-sm">
                        {conta.paciente
                          .split(' ')
                          .map((n) => n[0])
                          .slice(0, 2)
                          .join('')}
                      </div>
                      <div>
                        <p className="font-bold text-gray-800">{conta.paciente}</p>
                        <p className="text-xs text-gray-500">
                          Conta {conta.numero} • {conta.leito}
                        </p>
                      </div>
                    </div>
                    <div className="text-right">
                      <p className="text-xs text-gray-500">Subtotal</p>
                      <p className="font-bold text-gray-800">{moeda(totalConta(conta))}</p>
                    </div>
                  </div>

                  <table className="w-full text-sm">
                    <thead>
                      <tr className="text-gray-500 text-left">
                        <th className="px-4 py-2 font-medium">Categoria</th>
                        <th className="px-4 py-2 font-medium">Descrição</th>
                        <th className="px-4 py-2 font-medium text-center">Qtd</th>
                        <th className="px-4 py-2 font-medium text-right">Valor Unit.</th>
                        <th className="px-4 py-2 font-medium text-right">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
                      {conta.itens.map((item, idx) => (
                        <tr key={idx} className="border-t border-gray-50">
                          <td className="px-4 py-2">
                            <span className="text-xs font-medium px-2 py-0.5 rounded bg-brand/5 text-brand">
                              {item.categoria}
                            </span>
                          </td>
                          <td className="px-4 py-2 text-gray-700">{item.descricao}</td>
                          <td className="px-4 py-2 text-center text-gray-600">{item.qtd}</td>
                          <td className="px-4 py-2 text-right text-gray-600">
                            {moeda(item.valorUnit)}
                          </td>
                          <td className="px-4 py-2 text-right font-medium text-gray-800">
                            {moeda(item.qtd * item.valorUnit)}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ))}

              {/* Total geral */}
              <div className="flex justify-end items-center gap-4 border-t border-gray-100 pt-4">
                <span className="font-bold text-gray-700">Total Geral do Convênio</span>
                <span className="text-2xl font-bold text-brand">{moeda(totalConvenio)}</span>
              </div>
            </div>
          )}
        </div>
      )}

      {/* ===================== MODAL: CONFIRMAR FECHAMENTO ===================== */}
      {confirmarFechamento && contaSelecionada && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
          <div className="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 overflow-hidden">
            <div className="flex items-center justify-between p-5 border-b border-gray-100">
              <h3 className="text-lg font-bold text-gray-800 flex items-center gap-2">
                <AlertTriangle size={20} className="text-orange-500" /> Fechar conta
              </h3>
              <button
                onClick={() => setConfirmarFechamento(false)}
                className="text-gray-400 hover:text-gray-600"
              >
                <X size={20} />
              </button>
            </div>
            <div className="p-5 text-sm text-gray-600">
              <p>
                Você está prestes a fechar a conta{' '}
                <span className="font-bold text-gray-800">{contaSelecionada.numero}</span> do
                paciente <span className="font-bold text-gray-800">{contaSelecionada.paciente}</span>.
              </p>
              <p className="mt-2">
                Após o fechamento, a conta não poderá mais receber novos lançamentos. Valor
                total:{' '}
                <span className="font-bold text-brand">
                  {moeda(totalConta(contaSelecionada))}
                </span>
                .
              </p>
            </div>
            <div className="flex justify-end gap-2 p-5 border-t border-gray-100">
              <button
                onClick={() => setConfirmarFechamento(false)}
                className="px-4 py-2 text-sm font-bold text-gray-600 rounded-lg hover:bg-gray-100 transition-colors"
              >
                Cancelar
              </button>
              <button
                onClick={fecharConta}
                className="flex items-center px-4 py-2 bg-brand text-white text-sm font-bold rounded-lg hover:bg-brand-dark transition-colors"
              >
                <Lock size={16} className="mr-2" /> Confirmar Fechamento
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
