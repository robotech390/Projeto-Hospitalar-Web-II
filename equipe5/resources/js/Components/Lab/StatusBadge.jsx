import React from 'react';

const statusMap = {
  'Pendente': 'bg-yellow-100 text-yellow-800',
  'Coletado': 'bg-blue-100 text-blue-800',
  'Em Análise': 'bg-purple-100 text-purple-800',
  'Concluído': 'bg-emerald-100 text-emerald-800',
};

export default function StatusBadge({ status }) {
  return (
    <span className={`px-2 py-1 rounded text-xs font-semibold ${statusMap[status] || 'bg-gray-100 text-gray-800'}`}>
      {status}
    </span>
  );
}
