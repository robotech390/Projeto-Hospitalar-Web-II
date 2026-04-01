import React from 'react';

export default function PriorityTag({ priority }) {
  if (priority === 'Urgência') {
    return <span className="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-semibold">Urgência</span>;
  }
  return <span className="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs font-semibold">{priority}</span>;
}
