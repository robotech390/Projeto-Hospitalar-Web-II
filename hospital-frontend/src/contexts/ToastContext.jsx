import { createContext, useContext, useState, useCallback } from 'react'
import { CheckCircle2, AlertCircle, Info, X } from 'lucide-react'

const ToastContext = createContext(null)

const VARIANTES = {
  sucesso: { Icone: CheckCircle2, classe: 'bg-green-50  border-green-200  text-green-800',  iconeClasse: 'text-green-600'  },
  erro:    { Icone: AlertCircle,  classe: 'bg-red-50    border-red-200    text-red-800',    iconeClasse: 'text-red-600'    },
  aviso:   { Icone: AlertCircle,  classe: 'bg-amber-50  border-amber-200  text-amber-800',  iconeClasse: 'text-amber-600'  },
  info:    { Icone: Info,         classe: 'bg-sky-50    border-sky-200    text-sky-800',    iconeClasse: 'text-sky-600'    },
}

let proximoId = 1

/**
 * Provider de notificações (toasts).
 * Uso via hook: const { mostrar } = useToast(); mostrar('Salvo!', 'sucesso')
 */
export function ToastProvider({ children }) {
  const [toasts, setToasts] = useState([])

  const remover = useCallback((id) => {
    setToasts((atuais) => atuais.filter((t) => t.id !== id))
  }, [])

  const mostrar = useCallback((mensagem, tipo = 'info', duracao = 4000) => {
    const id = proximoId++
    setToasts((atuais) => [...atuais, { id, mensagem, tipo }])
    if (duracao > 0) {
      setTimeout(() => remover(id), duracao)
    }
    return id
  }, [remover])

  return (
    <ToastContext.Provider value={{ mostrar, remover }}>
      {children}
      <div className="fixed bottom-6 right-6 z-[100] flex flex-col gap-2 pointer-events-none">
        {toasts.map(({ id, mensagem, tipo }) => {
          const { Icone, classe, iconeClasse } = VARIANTES[tipo] || VARIANTES.info
          return (
            <div
              key={id}
              className={`pointer-events-auto flex items-start gap-3 min-w-[280px] max-w-md
                          border rounded-lg shadow-lg px-4 py-3 ${classe}
                          animate-[slideIn_0.2s_ease-out]`}
            >
              <Icone size={18} className={`mt-0.5 shrink-0 ${iconeClasse}`} />
              <p className="text-sm flex-1">{mensagem}</p>
              <button
                onClick={() => remover(id)}
                className="opacity-50 hover:opacity-100 transition-opacity shrink-0"
              >
                <X size={15} />
              </button>
            </div>
          )
        })}
      </div>
    </ToastContext.Provider>
  )
}

export const useToast = () => {
  const ctx = useContext(ToastContext)
  if (!ctx) throw new Error('useToast deve ser usado dentro de <ToastProvider>')
  return ctx
}
