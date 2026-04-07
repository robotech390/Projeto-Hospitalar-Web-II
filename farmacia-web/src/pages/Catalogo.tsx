export default function Catalogo() {
    return (
        <div className="bg-white rounded-lg shadow-sm p-6">
            <div className="flex justify-between items-center mb-6">
                <h2 className="text-2xl font-semibold text-gray-700">Catálogo de Produtos</h2>
                <button className="bg-[var(--color-brand-primary)] text-white px-4 py-2 rounded shadow hover:bg-[var(--color-brand-dark)]">
                    + Novo Medicamento
                </button>
            </div>
            <p>A lista de medicamentos aparecerá aqui.</p>
        </div>
    );
}