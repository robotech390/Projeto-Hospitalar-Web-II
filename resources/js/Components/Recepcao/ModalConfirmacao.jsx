export default function ModalConfirmacao({ isOpen, onClose, onConfirm, resumo }) {
    if (!isOpen) return null;

    return (
        <>
            <div className="fixed inset-0 bg-black/50 z-40" onClick={onClose} />
            <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div className="bg-white rounded-lg shadow-lg max-w-md w-full">
                    <div className="flex items-center justify-between p-6 border-b border-gray-200">
                        <h2 className="text-lg font-semibold text-gray-900">
                            Confirmar Agendamento
                        </h2>
                        <button
                            onClick={onClose}
                            className="text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            ✕
                        </button>
                    </div>

                    <div className="p-6 space-y-3 text-sm text-gray-700">
                        <div className="flex justify-between">
                            <span className="font-medium">Paciente:</span>
                            <span>{resumo.paciente}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="font-medium">Médico:</span>
                            <span>{resumo.medico}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="font-medium">Data:</span>
                            <span>{resumo.data}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="font-medium">Horário:</span>
                            <span>{resumo.hora}</span>
                        </div>
                        <div className="flex justify-between">
                            <span className="font-medium">Tipo:</span>
                            <span>{resumo.tipo}</span>
                        </div>
                    </div>

                    <div className="flex gap-3 p-6 pt-0">
                        <button
                            onClick={onClose}
                            className="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            onClick={onConfirm}
                            className="flex-1 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors font-medium"
                        >
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </>
    );
}
