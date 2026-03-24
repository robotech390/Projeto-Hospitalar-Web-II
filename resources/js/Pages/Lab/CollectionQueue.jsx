import React, { useState } from 'react';
import CollectionQueueTable from '../../Components/Lab/CollectionQueueTable';

export default function CollectionQueue() {
  const [queue, setQueue] = useState([
    {
      id: 101,
      patient: 'Maria Silva',
      exams: ['Hemograma', 'Glicose'],
      priority: 'Urgência',
      status: 'Pendente',
    },
    {
      id: 102,
      patient: 'João Souza',
      exams: ['Raio-X Tórax'],
      priority: '',
      status: 'Pendente',
    },
  ]);

  const handleCollect = (id) => {
    setQueue(queue.map(item =>
      item.id === id ? { ...item, status: 'Coletado' } : item
    ));
  };

  return (
    <div className="p-6 bg-slate-50 min-h-screen">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-2xl font-bold mb-4">Fila de Coleta</h1>
        <CollectionQueueTable queue={queue} onCollect={handleCollect} />
      </div>
    </div>
  );
}
