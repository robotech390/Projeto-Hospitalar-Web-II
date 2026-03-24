// Adapted from lovable/components/StatusBadge.tsx
import React from 'react';

const statusMap = {
  Pendente: 'bg-yellow-100 text-yellow-800',
  Coletado: 'bg-blue-100 text-blue-800',
  'Em Análise': 'bg-purple-100 text-purple-800',
  Concluído: 'bg-green-100 text-green-800',
};

export default function StatusBadge({ status }) {
  return (
    <span
      className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ${statusMap[status] || 'bg-gray-100 text-gray-800'}`}
    >
      {status}
    </span>
  );
}
