const DIAS_SEMANA = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
const MESES = [
    'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro',
];

const STATUS_COR = {
    agendado: 'bg-teal-500',
    cancelado: 'bg-red-500',
    realizado: 'bg-gray-400',
};

function pontosDodia(consultas, ano, mes, dia) {
    const data = `${ano}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
    return consultas
        .filter((c) => c.data === data)
        .map((c) => c.status)
        .filter((v, i, a) => a.indexOf(v) === i);
}

export default function Calendario({ ano, mes, consultas = [], diaSelecionado, onDiaClick, onMesChange }) {
    const primeiroDia = new Date(ano, mes, 1).getDay();
    const totalDias = new Date(ano, mes + 1, 0).getDate();

    const celulas = [];
    for (let i = 0; i < primeiroDia; i++) celulas.push(null);
    for (let d = 1; d <= totalDias; d++) celulas.push(d);

    const dataStr = (dia) =>
        `${ano}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;

    return (
        <div className="bg-white rounded-lg border border-gray-200 p-4 select-none">
            <div className="flex items-center justify-between mb-4">
                <button
                    onClick={() => onMesChange(-1)}
                    className="p-1 rounded hover:bg-gray-100 text-gray-600"
                >
                    ‹
                </button>
                <span className="font-semibold text-gray-800">
                    {MESES[mes]} {ano}
                </span>
                <button
                    onClick={() => onMesChange(1)}
                    className="p-1 rounded hover:bg-gray-100 text-gray-600"
                >
                    ›
                </button>
            </div>

            <div className="grid grid-cols-7 mb-1">
                {DIAS_SEMANA.map((d) => (
                    <div key={d} className="text-center text-xs font-medium text-gray-400 py-1">
                        {d}
                    </div>
                ))}
            </div>

            <div className="grid grid-cols-7 gap-y-1">
                {celulas.map((dia, idx) => {
                    if (!dia) return <div key={`v-${idx}`} />;

                    const pontos = pontosDodia(consultas, ano, mes, dia);
                    const selecionado = diaSelecionado === dataStr(dia);

                    return (
                        <button
                            key={dia}
                            onClick={() => onDiaClick(dataStr(dia))}
                            className={
                                'flex flex-col items-center rounded py-1 text-sm transition-colors ' +
                                (selecionado
                                    ? 'bg-teal-600 text-white font-semibold'
                                    : 'hover:bg-teal-50 text-gray-700')
                            }
                        >
                            {dia}
                            {pontos.length > 0 && (
                                <div className="flex gap-0.5 mt-0.5">
                                    {pontos.map((status) => (
                                        <span
                                            key={status}
                                            className={`w-1.5 h-1.5 rounded-full ${STATUS_COR[status] ?? 'bg-gray-300'} ${selecionado ? 'opacity-80' : ''}`}
                                        />
                                    ))}
                                </div>
                            )}
                        </button>
                    );
                })}
            </div>
        </div>
    );
}
