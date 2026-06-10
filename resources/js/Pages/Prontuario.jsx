import { useState, useEffect } from 'react';
import {
  ClipboardType,
  Pill,
  Stethoscope,
  History,
  Clock
} from 'lucide-react';

function parseDate(dateString) {
  if (!dateString) {
    return null;
  }

  const date = new Date(dateString);
  return Number.isNaN(date.getTime()) ? null : date;
}

function formatTime(dateString) {
  const date = parseDate(dateString);
  if (!date) {
    return 'Horário não disponível';
  }

  return `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
}

function calculateAge(birthDate) {
  const date = parseDate(birthDate);
  if (!date) {
    return null;
  }

  const today = new Date();
  let age = today.getFullYear() - date.getFullYear();
  const monthDiff = today.getMonth() - date.getMonth();
  const dayDiff = today.getDate() - date.getDate();

  if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
    age -= 1;
  }

  return age;
}

export default function Prontuario() {
  const [abaAtiva, setAbaAtiva] = useState('consulta');

  const props = window.APP_PROPS ?? {};
  const consultas = Array.isArray(props.consultas) ? props.consultas : [];
  const pacientes = Array.isArray(props.pacientes) ? props.pacientes : [];
  const receitas = Array.isArray(props.receitas) ? props.receitas : [];
  const solicitacoesExame = Array.isArray(props.solicitacoesExame) ? props.solicitacoesExame : [];

  const filaConsultas = consultas
    .filter((consulta) => consulta.paciente)
    .sort((a, b) => {
      const aTime = parseDate(a.data_check_in ?? a.data ?? a.hora_inicio)?.getTime() ?? 0;
      const bTime = parseDate(b.data_check_in ?? b.data ?? b.hora_inicio)?.getTime() ?? 0;
      return aTime - bTime;
    });

  const filaPacientes = filaConsultas.length > 0
    ? filaConsultas.map((consulta) => ({
        id: consulta.id,
        nome: consulta.paciente.nome,
        hora: formatTime(consulta.data_check_in ?? consulta.hora_inicio),
        status: consulta.status ?? 'Pendente',
        idade: calculateAge(consulta.paciente.data_nascimento),
      }))
    : pacientes.map((paciente) => ({
        id: paciente.id,
        nome: paciente.nome,
        hora: '—',
        status: 'Sem consulta agendada',
        idade: calculateAge(paciente.data_nascimento),
      }));

  const consultaAtiva = filaConsultas[0] ?? null;
  const pacienteAtivo = consultaAtiva?.paciente ?? null;
  const medicoAtivo = consultaAtiva?.medico?.pessoa ?? null;
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  const [motivo, setMotivo] = useState('');
  const [cid, setCid] = useState('');
  const [diagnosticoDescricao, setDiagnosticoDescricao] = useState('');
  const [evolucao, setEvolucao] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');

  useEffect(() => {
    setMotivo('');
    setCid('');
    setDiagnosticoDescricao('');
    setEvolucao('');
    setErrorMessage('');
  }, [consultaAtiva?.id]);

  const receitasDaConsulta = consultaAtiva
    ? receitas.filter((receita) => receita.id_consulta === consultaAtiva.id)
    : [];

  const examesDaConsulta = consultaAtiva
    ? solicitacoesExame.filter((solicitacao) => solicitacao.id_consulta === consultaAtiva.id)
    : [];

  const diagnosticosDaConsulta = consultaAtiva?.diagnosticos ?? [];

  //Ordena diagnósticos do mais recente para o mais antigo
  const diagnosticosOrdenados = [...diagnosticosDaConsulta].sort((a, b) => {
    const aDate = new Date(a.data_criacao ?? a.data_alteracao ?? 0).getTime() || 0;
    const bDate = new Date(b.data_criacao ?? b.data_alteracao ?? 0).getTime() || 0;
    if (bDate !== aDate) return bDate - aDate;
    return (b.id ?? 0) - (a.id ?? 0);
  });
  const diagnosticoMaisRecente = diagnosticosOrdenados[0] ?? null;
  const placeholderCid = diagnosticoMaisRecente?.cid ?? 'Ex: J03.9 - Amigdalite aguda não especificada';
  const placeholderDiagnosticoDescricao = diagnosticoMaisRecente?.descricao ?? 'Descreva o diagnóstico para o CID informado...';

  const historicoConsultas = consultas
    .filter((consulta) => consulta.paciente?.id === pacienteAtivo?.id && consulta.id !== consultaAtiva?.id)
    .sort((a, b) => {
      const aTime = parseDate(a.data)?.getTime() ?? 0;
      const bTime = parseDate(b.data)?.getTime() ?? 0;
      return bTime - aTime;
    });

  const hasFormContent = motivo.trim() || cid.trim() || diagnosticoDescricao.trim() || evolucao.trim();

  async function handleSalvarEvolucao() {
    if (!consultaAtiva) {
      return;
    }

    const descricao = [motivo.trim(), evolucao.trim()].filter(Boolean).join('\n\n');
    const wantsDiagnostico = cid.trim() !== '' || diagnosticoDescricao.trim() !== '';

    if (!descricao && !wantsDiagnostico) {
      return;
    }

    //Determina o CID a ser usado com base na entrada do usuário ou no diagnóstico mais recente
    const cidInput = cid.trim();
    const cidToUse = cidInput || (diagnosticoMaisRecente?.cid ?? '');

    if (diagnosticoDescricao.trim() && !cidToUse) {
      setErrorMessage('Forneça um CID ou tenha um diagnóstico existente para atualizar.');
      return;
    }

    setErrorMessage('');
    setIsSubmitting(true);

    try {
      const requests = [];

      //Atualiza a descrição da consulta se houver conteúdo relevante
      if (descricao) {
        requests.push(
          fetch(`/consultas/${consultaAtiva.id}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'X-HTTP-Method-Override': 'PUT',
              Accept: 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ descricao }),
          }),
        );
      }

      //Se o usuário forneceu uma descrição de diagnóstico, tentamos atualizar o diagnóstico mais recente ou criar um novo se o CID for diferente
      if (diagnosticoDescricao.trim()) {
        //Verifica se já existe um diagnóstico com o CID a ser usado (considerando entrada do usuário, diagnóstico existente ou placeholder)
        const existing = diagnosticosOrdenados.find((d) => ((d.cid || '').trim().toLowerCase()) === cidToUse.trim().toLowerCase());

        if (existing) {
          //Atualiza o diagnóstico existente com a nova descrição (mantendo o CID atual ou usando o CID de entrada se o existente não tiver CID)
          requests.push(
            fetch(`/diagnosticos/${existing.id}`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-HTTP-Method-Override': 'PUT',
                Accept: 'application/json',
              },
              credentials: 'same-origin',
              body: JSON.stringify({
                cid: existing.cid ?? cidToUse,
                descricao: diagnosticoDescricao.trim(),
                id_consulta: consultaAtiva.id,
              }),
            }),
          );
        } else {
          //Cria um novo diagnóstico se o CID for diferente do diagnóstico mais recente ou do placeholder (considerando entrada do usuário, diagnóstico existente ou placeholder)
          requests.push(
            fetch('/diagnosticos', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
              },
              credentials: 'same-origin',
              body: JSON.stringify({
                cid: cidToUse.trim(),
                descricao: diagnosticoDescricao.trim(),
                id_consulta: consultaAtiva.id,
              }),
            }),
          );
        }
      }

      const responses = await Promise.all(requests);
      for (const response of responses) {
        if (!response.ok) {
          const result = await response.json().catch(() => null);
          throw new Error(result?.message || 'Erro ao salvar. Verifique os dados e tente novamente.');
        }
      }

      window.location.reload();
    } catch (error) {
      setErrorMessage(error.message);
    } finally {
      setIsSubmitting(false);
    }
  }

  return (
    <div className="flex h-full gap-6 animate-fade-in">
      <div className="w-1/3 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-[calc(100vh-8rem)]">
        <div className="p-5 border-b border-gray-100">
          <h2 className="text-lg font-bold text-gray-800">Fila de Atendimento</h2>
          <p className="text-xs text-gray-500">Consultas com check-in realizado</p>
        </div>

        <div className="overflow-y-auto flex-1 p-3 space-y-2">
          {filaPacientes.length === 0 ? (
            <div className="p-4 rounded-lg bg-white border border-gray-100 text-sm text-gray-500">
              Nenhum paciente disponível no momento.
            </div>
          ) : (
            filaPacientes.map((paciente, index) => (
              <div
                key={paciente.id}
                className={`p-4 rounded-lg cursor-pointer transition-colors border ${
                  index === 0 ? 'bg-brand/5 border-brand/20' : 'bg-white border-gray-50 hover:bg-gray-50'
                }`}
              >
                <div className="flex justify-between items-start mb-2">
                  <p className="font-bold text-gray-800">{paciente.nome}</p>
                  <span className="flex items-center text-xs font-medium text-gray-500">
                    <Clock size={12} className="mr-1" /> {paciente.hora}
                  </span>
                </div>
                <div className="flex items-center justify-between gap-3 flex-wrap">
                  <span className="text-xs font-semibold px-2 py-1 rounded bg-orange-50 text-orange-600">
                    {paciente.status}
                  </span>
                  {typeof paciente.idade === 'number' && (
                    <span className="text-xs text-gray-500">{paciente.idade} anos</span>
                  )}
                </div>
              </div>
            ))
          )}
        </div>
      </div>

      <div className="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-[calc(100vh-8rem)]">
        <div className="p-5 border-b border-gray-100 flex items-center space-x-4 bg-gray-50/50 rounded-t-xl">
          <div className="w-12 h-12 rounded-full bg-brand text-white flex items-center justify-center font-bold text-lg">
            {pacienteAtivo ? pacienteAtivo.nome.split(' ').map((part) => part[0]).join('') : '—'}
          </div>
          <div>
            <h2 className="text-xl font-bold text-gray-800">
              {pacienteAtivo?.nome ?? 'Paciente não selecionado'}
            </h2>
            <p className="text-sm text-gray-500">
              {pacienteAtivo
                ? `${calculateAge(pacienteAtivo.data_nascimento) ?? 'Idade indisponível'} anos • ${medicoAtivo ? `Médico: ${medicoAtivo.nome}` : 'Médico não informado'}`
                : 'Selecione uma consulta para visualizar os detalhes.'}
            </p>
            <p className="text-xs text-gray-400 mt-1">
              {consultaAtiva?.status ? `Status: ${consultaAtiva.status}` : ''}
            </p>
          </div>
        </div>

        {/* Abas de navegação */}
        <div className="flex border-b border-gray-100 px-2">
          <button
            onClick={() => setAbaAtiva('consulta')}
            className={`flex items-center px-4 py-3 text-sm font-medium border-b-2 transition-colors ${abaAtiva === 'consulta' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
          >
            <ClipboardType size={16} className="mr-2" /> Evolução
          </button>
          <button
            onClick={() => setAbaAtiva('prescricao')}
            className={`flex items-center px-4 py-3 text-sm font-medium border-b-2 transition-colors ${abaAtiva === 'prescricao' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
          >
            <Pill size={16} className="mr-2" /> Prescrição
          </button>
          <button
            onClick={() => setAbaAtiva('exames')}
            className={`flex items-center px-4 py-3 text-sm font-medium border-b-2 transition-colors ${abaAtiva === 'exames' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
          >
            <Stethoscope size={16} className="mr-2" /> Exames
          </button>
          <button
            onClick={() => setAbaAtiva('historico')}
            className={`flex items-center px-4 py-3 text-sm font-medium border-b-2 transition-colors ${abaAtiva === 'historico' ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700'}`}
          >
            <History size={16} className="mr-2" /> Histórico
          </button>
        </div>

        <div className="flex-1 overflow-y-auto p-6">
          {abaAtiva === 'consulta' && (
            <div className="space-y-4">
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Motivo da Consulta / Histórico Médico</label>
                <textarea
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand resize-none h-24 text-sm"
                  placeholder={consultaAtiva?.descricao ?? 'Descreva os sintomas e o histórico do paciente...'}
                  value={motivo}
                  onChange={(event) => setMotivo(event.target.value)}
                />
              </div>
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Diagnóstico (CID)</label>
                <input
                  type="text"
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand text-sm"
                  placeholder={placeholderCid}
                  value={cid}
                  onChange={(event) => setCid(event.target.value)}
                />
              </div>
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Descrição do Diagnóstico</label>
                <textarea
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand resize-none h-20 text-sm"
                  placeholder={placeholderDiagnosticoDescricao}
                  value={diagnosticoDescricao}
                  onChange={(event) => setDiagnosticoDescricao(event.target.value)}
                />
              </div>
              {diagnosticosOrdenados.length > 0 && (
                <div className="mt-3 space-y-2">
                  {diagnosticosOrdenados.map((diagnostico) => (
                    <div key={diagnostico.id} className="rounded-lg border border-gray-100 p-3 bg-gray-50">
                      <p className="text-sm font-semibold text-gray-700">{diagnostico.cid ?? 'CID não informado'}</p>
                      <p className="text-xs text-gray-500">{diagnostico.descricao ?? 'Sem descrição de diagnóstico.'}</p>
                    </div>
                  ))}
                </div>
              )}
              <div>
                <label className="block text-sm font-bold text-gray-700 mb-1">Evolução / Conduta Clínica</label>
                <textarea
                  className="w-full p-3 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-1 focus:ring-brand resize-none h-32 text-sm"
                  placeholder="Anote a conduta, orientações e evolução do quadro..."
                  value={evolucao}
                  onChange={(event) => setEvolucao(event.target.value)}
                />
              </div>
              <div className="flex flex-col items-end gap-2 pt-2">
                {errorMessage && (
                  <p className="text-sm text-red-600">{errorMessage}</p>
                )}
                <button
                  type="button"
                  onClick={handleSalvarEvolucao}
                  disabled={!hasFormContent || isSubmitting}
                  className={`px-6 py-2 font-bold rounded-lg transition-colors ${(!hasFormContent || isSubmitting) ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-brand text-white hover:bg-brand-dark'}`}
                >
                  {isSubmitting ? 'Salvando...' : 'Salvar Evolução'}
                </button>
              </div>
            </div>
          )}

          {abaAtiva === 'prescricao' && (
            <div>
              <div className="border border-gray-100 rounded-lg overflow-hidden">
                <div className="bg-gray-50 p-3 border-b border-gray-100 font-bold text-sm text-gray-700">Receituário Atual</div>
                {receitasDaConsulta.length === 0 ? (
                  <div className="p-4 text-center text-sm text-gray-400">Nenhuma receita encontrada para esta consulta.</div>
                ) : (
                  receitasDaConsulta.map((receita) => (
                    <div key={receita.id} className="p-4 border-b border-gray-100 last:border-b-0">
                      <div className="flex justify-between items-center mb-3">
                        <span className="font-semibold text-sm text-gray-800">Receita #{receita.id}</span>
                        <span className="text-xs text-gray-500">{parseDate(receita.data_emissao)?.toLocaleDateString('pt-BR') ?? 'Data não disponível'}</span>
                      </div>
                      {Array.isArray(receita.medicamentos) && receita.medicamentos.length > 0 ? (
                        <div className="space-y-3">
                          {receita.medicamentos.map((item) => (
                            <div key={item.id} className="rounded-lg border border-gray-100 p-3 bg-gray-50">
                              <p className="text-sm font-semibold text-gray-700">
                                {item.medicamento?.nome ? item.medicamento.nome : `Medicamento #${item.id_medicamento}`}
                              </p>
                              <p className="text-xs text-gray-500">Quantidade: {item.quantidade ?? '—'}</p>
                              <p className="text-xs text-gray-500">Posologia: {item.posologia ?? '—'}</p>
                            </div>
                          ))}
                        </div>
                      ) : (
                        <div className="text-sm text-gray-500">Nenhum item de medicamento cadastrado nesta receita.</div>
                      )}
                    </div>
                  ))
                )}
              </div>
            </div>
          )}

          {abaAtiva === 'exames' && (
            <div>
              {examesDaConsulta.length === 0 ? (
                <div className="border border-gray-100 rounded-lg p-4 text-sm text-gray-500">
                  Nenhuma solicitação de exame encontrada para esta consulta.
                </div>
              ) : (
                examesDaConsulta.map((solicitacao) => (
                  <div key={solicitacao.id} className="border border-gray-100 rounded-lg mb-4">
                    <div className="bg-gray-50 p-3 border-b border-gray-100 flex justify-between items-center">
                      <div>
                        <p className="font-semibold text-gray-800">Solicitação #{solicitacao.id}</p>
                        <p className="text-xs text-gray-500">Prioridade: {solicitacao.prioridade ?? '—'}</p>
                      </div>
                      <span className="text-xs text-gray-500">{parseDate(solicitacao.data)?.toLocaleDateString('pt-BR') ?? 'Data não disponível'}</span>
                    </div>
                    <div className="p-4 space-y-3">
                      <p className="text-sm text-gray-600">{solicitacao.justificativa ?? 'Sem justificativa registrada.'}</p>
                      {Array.isArray(solicitacao.itens) && solicitacao.itens.length > 0 ? (
                        <div className="space-y-2">
                          {solicitacao.itens.map((item) => (
                            <div key={item.id} className="rounded-lg border border-gray-100 p-3 bg-gray-50">
                              <p className="text-sm font-semibold text-gray-700">
                                Tipo de exame: {item.tipoExame?.nome ?? (item.id_tipo_exame ? `#${item.id_tipo_exame}` : 'Não disponível')}
                              </p>
                              <p className="text-xs text-gray-500">Status: {item.status ?? '—'}</p>
                              {item.laudo && <p className="text-xs text-gray-500">Laudo: {item.laudo}</p>}
                              {item.data_resultado && (
                                <p className="text-xs text-gray-500">Resultado: {parseDate(item.data_resultado)?.toLocaleDateString('pt-BR')}</p>
                              )}
                            </div>
                          ))}
                        </div>
                      ) : (
                        <div className="text-sm text-gray-500">Nenhum item de exame cadastrado nesta solicitação.</div>
                      )}
                    </div>
                  </div>
                ))
              )}
            </div>
          )}

          {abaAtiva === 'historico' && (
            <div className="space-y-4">
              {historicoConsultas.length === 0 ? (
                <div className="p-4 rounded-lg border border-gray-100 text-sm text-gray-500">
                  Histórico de consultas não disponível para o paciente atual.
                </div>
              ) : (
                historicoConsultas.map((consulta) => (
                  <div key={consulta.id} className="p-4 border border-gray-100 rounded-lg hover:shadow-sm transition-shadow">
                    <div className="flex justify-between items-center mb-2">
                      <span className="font-bold text-brand">Consulta {consulta.id}</span>
                      <span className="text-xs text-gray-500">{parseDate(consulta.data)?.toLocaleDateString('pt-BR') ?? 'Data não disponível'}</span>
                    </div>
                    <p className="text-sm text-gray-600 line-clamp-2">
                      {consulta.descricao ?? 'Nenhuma descrição registrada para esta consulta.'}
                    </p>
                    <p className="text-xs text-gray-400 mt-2">
                      Atendido por: Dr. {consulta.medico?.pessoa?.nome ?? 'Não informado'}{consulta.medico?.especialidade ? ` (${consulta.medico.especialidade})` : ''}
                    </p>
                  </div>
                ))
              )}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
