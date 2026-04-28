const DIAS_SEMANA = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
const MESES = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
];

const STATUS_COR = {
    agendado: 'bg-teal-400',
    cancelado: 'bg-red-400',
    realizado: 'bg-gray-300',
};

function pontosDodia(consultas, ano, mes, dia) {
    const data = `${ano}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
    return consultas
        .filter((c) => c.data === data)
        .map((c) => c.status)
        .filter((v, i, a) => a.indexOf(v) === i);
}

export default function Calendario({ ano, mes, consultas = [], diaSelecionado, onDiaClick, onMesChange }) {
    const hoje = new Date();
    const primeiroDia = new Date(ano, mes, 1).getDay();
    const totalDias = new Date(ano, mes + 1, 0).getDate();

    const celulas = [];
    for (let i = 0; i < primeiroDia; i++) celulas.push(null);
    for (let d = 1; d <= totalDias; d++) celulas.push(d);

    const dataStr = (dia) =>
        `${ano}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;

    const ehHoje = (dia) =>
        dia === hoje.getDate() && mes === hoje.getMonth() && ano === hoje.getFullYear();

    return (
        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden select-none">
            {/* Cabeçalho */}
            <div className="bg-teal-600 px-4 py-4 flex items-center justify-between">
                <button
                    onClick={() => onMesChange(-1)}
                    className="w-8 h-8 flex items-center justify-center rounded-full hover:bg-teal-500 text-white text-lg font-light transition-colors"
                >
                    ‹
                </button>
                <span className="text-white font-semibold text-base tracking-wide">
                    {MESES[mes]} {ano}
                </span>
                <button
                    onClick={() => onMesChange(1)}
                    className="w-8 h-8 flex items-center justify-center rounded-full hover:bg-teal-500 text-white text-lg font-light transition-colors"
                >
                    ›
                </button>
            </div>

            <div className="p-4">
                {/* Dias da semana */}
                <div className="grid mb-2" style={{ gridTemplateColumns: 'repeat(7, minmax(0, 1fr))' }}>
                    {DIAS_SEMANA.map((d) => (
                        <div key={d} className="text-center text-xs font-semibold text-gray-400 py-1 uppercase tracking-wider">
                            {d}
                        </div>
                    ))}
                </div>

                {/* Dias */}
                <div className="grid" style={{ gridTemplateColumns: 'repeat(7, minmax(0, 1fr))' }}>
                    {celulas.map((dia, idx) => {
                        if (!dia) return <div key={`v-${idx}`} className="h-8" />;

                        const pontos = pontosDodia(consultas, ano, mes, dia);
                        const selecionado = diaSelecionado === dataStr(dia);
                        const today = ehHoje(dia);

                        return (
                            <button
                                key={dia}
                                onClick={() => onDiaClick(dataStr(dia))}
                                className={
                                    'h-8 w-full flex flex-col items-center justify-center rounded-lg text-xs transition-all ' +
                                    (selecionado
                                        ? 'bg-teal-600 text-white font-semibold shadow-md'
                                        : today
                                        ? 'bg-teal-50 text-teal-700 font-semibold ring-1 ring-teal-300'
                                        : 'hover:bg-gray-50 text-gray-700')
                                }
                            >
                                {dia}
                                {pontos.length > 0 && (
                                    <div className="flex gap-0.5">
                                        {pontos.map((status) => (
                                            <span
                                                key={status}
                                                className={`w-1 h-1 rounded-full ${STATUS_COR[status] ?? 'bg-gray-300'} ${selecionado ? 'opacity-70' : ''}`}
                                            />
                                        ))}
                                    </div>
                                )}
                            </button>
                        );
                    })}
                </div>
            </div>

            {/* Legenda */}
            <div className="px-4 pb-4 flex gap-3 text-xs text-gray-400">
                <span className="flex items-center gap-1">
                    <span className="w-2 h-2 rounded-full bg-teal-400 inline-block" /> Agendado
                </span>
                <span className="flex items-center gap-1">
                    <span className="w-2 h-2 rounded-full bg-red-400 inline-block" /> Cancelado
                </span>
                <span className="flex items-center gap-1">
                    <span className="w-2 h-2 rounded-full bg-gray-300 inline-block" /> Realizado
                </span>
            </div>
        </div>
    );
}
