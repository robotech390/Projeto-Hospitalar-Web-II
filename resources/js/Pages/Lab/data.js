// Mock data for Lab pages
export const catalogoExames = [
  { id: '1', nome: 'Hemograma', tipo: 'Sangue', preco: 35.5, preparo: 'Jejum de 8 horas' },
  { id: '2', nome: 'Raio-X Tórax', tipo: 'Raio-X', preco: 120, preparo: 'Remover objetos metálicos' },
];

export const pedidosExames = [
  {
    id: '101',
    paciente: 'Maria Silva',
    exame: 'Hemograma',
    tipo: 'Sangue',
    horario: '08:00',
    status: 'Pendente',
    medico: 'Dr. João',
    dataSolicitacao: '2024-03-20',
    iniciais: 'MS',
  },
  {
    id: '102',
    paciente: 'João Souza',
    exame: 'Raio-X Tórax',
    tipo: 'Raio-X',
    horario: '09:00',
    status: 'Pendente',
    medico: 'Dra. Ana',
    dataSolicitacao: '2024-03-21',
    iniciais: 'JS',
  },
];
