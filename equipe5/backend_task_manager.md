# 📋 Gerenciador de Tarefas - Equipe 5 (Hospitalar)

## 🧩 Fase 1: Desenvolvimento dos Models e Relacionamentos (Concluído ✅)
- [x] **Model TipoExame:** Mapeamento da tabela e relacionamento `hasMany` com itens.
- [x] **Model SolicitacaoExame:** Accessors/Mutators de data, Enums de Status e relacionamento `hasMany` com itens.
- [x] **Model ItemExame:** Relacionamentos `belongsTo` com Solicitação e Tipo de Exame.
- [x] **Migrações:** Tabelas criadas e migradas com chaves estrangeiras corretas.

## 🛡️ Fase 2: Validações e APIs Base (Concluído ✅)
- [x] **Requests:** Implementação de `StoreSolicitacaoExameRequest` e `UpdateSolicitacaoExameRequest`.
- [x] **Regras de Negócio:** Validação de no mínimo 1 item, evitar duplicidade de exames e fluxo de status.
- [x] **Service Providers:** Configuração de Providers e injeção de dependência para Services/Repositories.
- [x] **Controllers API:** Endpoints iniciais para Solicitações e Tipos de Exame.

## 🚀 Fase 3: CRUD de Itens (Integração Front-end ↔ Back-end)
*Objetivo: Implementar a gestão individualizada de itens de exame dentro do fluxo de solicitações.*

### 3.1. Back-end: Endpoints de Itens
- [x] **ItemExameController:** Criar controlador específico para operações em itens individuais.
- [x] **Update de Item:** Endpoint para alterar status do item (`pendente` -> `coletado` -> `concluido`) e adicionar laudos/resultados.
- [x] **Exclusão de Item:** Endpoint para remover um item de uma solicitação (respeitando integridade de negócio).
- [x] **Upload de Arquivos:** Implementar lógica de storage para anexar PDFs/Imagens aos itens.

### 3.2. Front-end: Integração React (Inertia)
- [ ] **Service API:** Criar funções de chamada para o CRUD de itens no front-end.
- [ ] **Gestão de Itens na Tela:** Permitir adicionar/remover itens dinamicamente na tela de edição/criação de solicitação.
- [ ] **Formulário de Resultado:** Componente para preenchimento de laudo e upload de arquivo por item.
- [ ] **Feedback UI:** Toasts de sucesso/erro e modais de confirmação para ações destrutivas.

## 🧪 Fase 4: Mock de Dados, Testes e Refinamento
- [ ] **Seeder:** Popular `tipo_exame` com dados reais (Hemograma, RX, etc).
- [ ] **Factories:** Gerar dados de teste para solicitações complexas.
- [ ] **Testes de Integração:** Validar fluxo completo desde a criação no React até a persistência no DB.
- [ ] **Transições de Status:** Garantir que o front-end respeite as travas de status do back-end.

## 🔮 Fase 5: Boas Práticas e Melhorias Futuras
- [ ] **Soft Deletes:** Implementar exclusão lógica para histórico hospitalar.
- [ ] **Database Transactions:** Garantir atomicidade em operações que envolvam múltiplos itens.
- [ ] **Logs de Auditoria:** Rastrear quem alterou cada item e quando.
- [ ] **Paginação:** Otimizar listagens de solicitações no Front-end.