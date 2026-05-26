import { AlertTriangle } from 'lucide-react'

/**
 * Diálogo de confirmação amigável. Substitui o `confirm()` nativo.
 *
 * Exemplo:
 *   <DialogoConfirmacao
 *     aberto={...}
 *     titulo="Inativar médico?"
 *     mensagem="Esta ação inativa o cadastro. Você poderá reativar depois."
 *     textoConfirmar="Inativar"
 *     onConfirmar={...}
 *     onCancelar={...}
 *   />
 */
export default function DialogoConfirmacao({
  aberto,
  titulo,
  mensagem,
  textoConfirmar = 'Confirmar',
  textoCancelar  = 'Cancelar',
  variante       = 'perigo',
  onConfirmar,
  onCancelar,
}) {
  if (!aberto) return null

  const classeBotao = variante === 'perigo'
    ? 'bg-red-500 hover:bg-red-600 text-white'
    : 'bg-brand hover:bg-brand-dark text-white'

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" onClick={onCancelar} />

      <div className="relative bg-white rounded-xl shadow-xl w-full max-w-md z-10 p-6">
        <div className="flex items-start gap-4">
          <div className={`p-2 rounded-full shrink-0 ${
            variante === 'perigo' ? 'bg-red-50' : 'bg-amber-50'
          }`}>
            <AlertTriangle size={20} className={
              variante === 'perigo' ? 'text-red-500' : 'text-amber-500'
            } />
          </div>
          <div className="flex-1">
            <h3 className="font-semibold text-slate-800 mb-1">{titulo}</h3>
            <p className="text-sm text-slate-500">{mensagem}</p>
          </div>
        </div>

        <div className="flex justify-end gap-2 mt-6">
          <button onClick={onCancelar} className="btn-secondary">{textoCancelar}</button>
          <button
            onClick={onConfirmar}
            className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${classeBotao}`}
          >
            {textoConfirmar}
          </button>
        </div>
      </div>
    </div>
  )
}
