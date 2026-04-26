/**
 * Tooltip simples via CSS — sem dependência externa.
 * Uso: <Tooltip text="Remover usuário"><button>...</button></Tooltip>
 */
export default function Tooltip({ text, children, position = 'top' }) {
  const positions = {
    top:    'bottom-full left-1/2 -translate-x-1/2 mb-2',
    bottom: 'top-full left-1/2 -translate-x-1/2 mt-2',
    left:   'right-full top-1/2 -translate-y-1/2 mr-2',
    right:  'left-full top-1/2 -translate-y-1/2 ml-2',
  }

  return (
    <div className="relative group inline-flex">
      {children}
      <span className={`
        pointer-events-none absolute z-50 whitespace-nowrap
        bg-slate-800 text-white text-xs rounded-md px-2 py-1
        opacity-0 group-hover:opacity-100 transition-opacity duration-150
        ${positions[position]}
      `}>
        {text}
      </span>
    </div>
  )
}
