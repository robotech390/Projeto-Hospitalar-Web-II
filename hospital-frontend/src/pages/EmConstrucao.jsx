import { Construction } from 'lucide-react'

export default function EmConstrucao({ titulo }) {
  return (
    <div className="flex flex-col items-center justify-center h-80 text-center">
      <div className="p-4 rounded-2xl bg-slate-100 mb-4">
        <Construction size={28} className="text-slate-400" />
      </div>
      <h2 className="text-lg font-semibold text-slate-700">{titulo}</h2>
      <p className="text-sm text-slate-400 mt-1">Módulo sob responsabilidade de outra equipe.</p>
    </div>
  )
}
