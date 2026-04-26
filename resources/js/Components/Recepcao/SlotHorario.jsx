export default function SlotHorario({ hora, disponivel, selecionado, onClick }) {
    if (!disponivel) {
        return (
            <button
                disabled
                className="px-3 py-2 rounded text-sm bg-gray-100 text-gray-400 cursor-not-allowed"
            >
                {hora}
            </button>
        );
    }

    return (
        <button
            onClick={() => onClick(hora)}
            className={
                'px-3 py-2 rounded text-sm font-medium transition-colors ' +
                (selecionado
                    ? 'bg-teal-600 text-white'
                    : 'bg-white border border-teal-600 text-teal-700 hover:bg-teal-50')
            }
        >
            {hora}
        </button>
    );
}
