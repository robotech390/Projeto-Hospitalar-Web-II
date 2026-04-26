const VARIANTS = {
  consulting: 'bg-green-100 text-green-700',
  waiting:    'bg-orange-100 text-orange-700',
  checkin:    'bg-teal-100 text-teal-700',
  scheduled:  'bg-slate-100 text-slate-600',
  active:     'bg-green-100 text-green-700',
  inactive:   'bg-red-100 text-red-600',
  admin:      'bg-purple-100 text-purple-700',
  medico:     'bg-teal-100 text-teal-700',
  paciente:   'bg-blue-100 text-blue-700',
  farmaceutico: 'bg-amber-100 text-amber-700',
  recepcionista:'bg-indigo-100 text-indigo-700',
}

export default function Badge({ variant = 'scheduled', children }) {
  return (
    <span className={`inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium ${VARIANTS[variant] || VARIANTS.scheduled}`}>
      {children}
    </span>
  )
}
