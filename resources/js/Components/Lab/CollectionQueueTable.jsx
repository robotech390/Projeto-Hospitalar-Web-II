import React from 'react';
import StatusBadge from './StatusBadge';
import PriorityTag from './PriorityTag';

export default function CollectionQueueTable({ queue, onCollect }) {
  return (
    <table className="min-w-full bg-white rounded-xl shadow-sm">
      <thead>
        <tr>
          <th className="px-4 py-2 text-left">Paciente</th>
          <th className="px-4 py-2 text-left">Solicitação</th>
          <th className="px-4 py-2 text-left">Exames</th>
          <th className="px-4 py-2 text-left">Prioridade</th>
          <th className="px-4 py-2 text-left">Status</th>
          <th className="px-4 py-2 text-left">Ação</th>
        </tr>
      </thead>
      <tbody>
        {queue.map(item => (
          <tr key={item.id} className="border-b last:border-b-0">
            <td className="px-4 py-2">{item.patient}</td>
            <td className="px-4 py-2">{item.id}</td>
            <td className="px-4 py-2">
              {item.exams.map(exam => (
                <span key={exam} className="inline-block bg-slate-100 text-slate-800 rounded px-2 py-0.5 text-xs mr-1 mb-1">{exam}</span>
              ))}
            </td>
            <td className="px-4 py-2">
              {item.priority && <PriorityTag priority={item.priority} />}
            </td>
            <td className="px-4 py-2">
              <StatusBadge status={item.status} />
            </td>
            <td className="px-4 py-2">
              {item.status === 'Pendente' && (
                <button
                  className="bg-[#00767F] text-white px-3 py-1 rounded-xl shadow-sm hover:bg-[#005a61]"
                  onClick={() => onCollect(item.id)}
                >
                  Registrar Coleta
                </button>
              )}
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}
