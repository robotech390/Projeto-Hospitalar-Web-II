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
    status: 'Concluído',
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
    status: 'Coletado',
    medico: 'Dra. Ana',
    dataSolicitacao: '2024-03-21',
    iniciais: 'JS',
  },
  {
    id: '103',
    paciente: 'Carlos Lima',
    exame: 'Glicemia',
    tipo: 'Sangue',
    horario: '10:00',
    status: 'Em Análise',
    medico: 'Dr. Pedro',
    dataSolicitacao: '2024-03-22',
    iniciais: 'CL',
  },
  {
    id: '104',
    paciente: 'Ana Paula',
    exame: 'Ultrassom',
    tipo: 'Imagem',
    horario: '11:00',
    status: 'Pendente',
    medico: 'Dra. Beatriz',
    dataSolicitacao: '2024-03-23',
    iniciais: 'AP',
  },
];
