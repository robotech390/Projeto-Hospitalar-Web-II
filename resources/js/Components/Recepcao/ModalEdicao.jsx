import { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';

export default function ModalEdicao({ isOpen, onClose, consulta, medicos, pacientes, tiposConsulta }) {
    const [medicoId, setMedicoId] = useState('');
    const [pacienteId, setPacienteId] = useState('');
    const [tipoConsultaId, setTipoConsultaId] = useState('');
    const [data, setData] = useState('');
    const [horaInicio, setHoraInicio] = useState('');
    const [descricao, setDescricao] = useState('');
    const [status, setStatus] = useState('agendado');
    const [erros, setErros] = useState({});

    useEffect(() => {
        if (consulta) {
            setMedicoId(String(consulta.id_medico ?? ''));
            setPacienteId(String(consulta.id_paciente ?? ''));
            setTipoConsultaId(String(consulta.id_tipo_consulta ?? ''));
            setData(consulta.data ?? '');
            setHoraInicio(consulta.hora_inicio ? consulta.hora_inicio.slice(0, 5) : '');
            setDescricao(consulta.descricao ?? '');
            setStatus(consulta.status ?? 'agendado');
            setErros({});
        }
    }, [consulta]);

    if (!isOpen || !consulta) return null;

    const horarios = [];
    for (let h = 7; h < 19; h++) {
        horarios.push(`${String(h).padStart(2, '0')}:00`);
        horarios.push(`${String(h).padStart(2, '0')}:30`);
    }

    const podeSalvar = medicoId && pacienteId && tipoConsultaId && data && horaInicio;

    function handleSubmit(e) {
        e.preventDefault();
        setErros({});

        router.put(
            route('recepcao.agendamento.update', consulta.id),
            {
                id_paciente: pacienteId,
                id_medico: medicoId,
                id_tipo_consulta: tipoConsultaId,
                data,
                hora_inicio: horaInicio,
                descricao,
                status,
            },
            {
                onError: (errs) => setErros(errs),
                onSuccess: () => onClose(),
            }
        );
    }

    return (
        <>
            <div className="fixed inset-0 bg-black/50 z-40" onClick={onClose} />
            <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div className="bg-white rounded-lg shadow-lg max-w-lg w-full max-h-[90vh] overflow-y-auto">
                    <div className="flex items-center justify-between p-6 border-b border-gray-200">
                        <h2 className="text-lg font-semibold text-gray-900">
                            Editar Consulta
                        </h2>
                        <button
                            onClick={onClose}
                            className="text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            ✕
                        </button>
                    </div>

                    <form onSubmit={handleSubmit} className="p-6 space-y-4">
                        {/* Médico */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Médico
                            </label>
                            <select
                                value={medicoId}
                                onChange={(e) => setMedicoId(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                            >
                                <option value="">Selecione o médico</option>
                                {medicos.map((m) => (
                                    <option key={m.id} value={m.id}>
                                        {m.nome} — {m.especialidade}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Paciente */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Paciente
                            </label>
                            <select
                                value={pacienteId}
                                onChange={(e) => setPacienteId(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                            >
                                <option value="">Selecione o paciente</option>
                                {pacientes.map((p) => (
                                    <option key={p.id} value={p.id}>
                                        {p.nome} ({p.cpf})
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Data */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Data
                            </label>
                            <input
                                type="date"
                                value={data}
                                onChange={(e) => setData(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                            />
                        </div>

                        {/* Hora início */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Hora de início
                            </label>
                            <select
                                value={horaInicio}
                                onChange={(e) => setHoraInicio(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                            >
                                <option value="">Selecione o horário</option>
                                {horarios.map((h) => (
                                    <option key={h} value={h}>
                                        {h}
                                    </option>
                                ))}
                            </select>
                            {erros.hora_inicio && (
                                <p className="mt-1 text-xs text-red-600">{erros.hora_inicio}</p>
                            )}
                        </div>

                        {/* Tipo de consulta */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Tipo de Consulta
                            </label>
                            <select
                                value={tipoConsultaId}
                                onChange={(e) => setTipoConsultaId(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                            >
                                <option value="">Selecione o tipo</option>
                                {tiposConsulta.map((t) => (
                                    <option key={t.id} value={t.id}>
                                        {t.descricao} — R$ {t.valor}
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Status */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Status
                            </label>
                            <select
                                value={status}
                                onChange={(e) => setStatus(e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500"
                            >
                                <option value="agendado">Agendado</option>
                                <option value="realizado">Realizado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>

                        {/* Descrição */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Descrição (opcional)
                            </label>
                            <textarea
                                value={descricao}
                                onChange={(e) => setDescricao(e.target.value)}
                                rows={3}
                                className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 resize-none"
                                placeholder="Observações sobre a consulta..."
                            />
                        </div>

                        {/* Botões */}
                        <div className="flex gap-3 pt-2">
                            <button
                                type="button"
                                onClick={onClose}
                                className="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm hover:bg-gray-50 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                disabled={!podeSalvar}
                                className={
                                    'flex-1 px-4 py-2 rounded-lg font-medium text-sm transition-colors ' +
                                    (podeSalvar
                                        ? 'bg-teal-600 text-white hover:bg-teal-700'
                                        : 'bg-gray-100 text-gray-400 cursor-not-allowed')
                                }
                            >
                                Salvar alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
