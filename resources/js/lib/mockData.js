// Estrutura de dados para as telas de faturamento

export const tiposCobrancaData = [
  // Exemplo de dados - começar vazio
];

export const conveniosData = [
  // Exemplo de dados - começar vazio
];

export const planosData = [
  // Exemplo de dados - começar vazio
];

// Tipos de dados (para referência)
export const TipoCobrancaStructure = {
  id: 'int - PK',
  descricao: 'varchar'
};

export const ConvenioStructure = {
  id: 'int - PK',
  nome: 'varchar',
  cnpj: 'varchar',
  telefone: 'varchar',
  email: 'varchar',
  endereco: {
    rua: 'varchar',
    numero: 'varchar',
    cidade: 'varchar',
    estado: 'varchar',
    cep: 'varchar'
  },
  id_endereco: 'int - FK'
};

export const PlanoStructure = {
  id: 'int - PK',
  descricao: 'varchar',
  id_tipo_cobranca: 'int - FK',
  id_convenio: 'int - FK'
};
