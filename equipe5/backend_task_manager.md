## 🧩 Fase 1: Desenvolvimento dos Models e Relacionamentos
*Objetivo: Mapear as tabelas para orientaçao a objetos com a lógica correta de relacionamentos no seu framework.*

### 1.1. Model `TipoExame`
- [x] Criar o arquivo/classe do model `TipoExame`.
- [x] Definir a propriedade que aponta para a tabela `tipo_exame`.
- [x] Declarar as propriedades "mass assignables" (atributos preenchíveis).
- [x] Criar relacionamento genérico `1:N` (hasMany) informando que **um tipo de exame pertence a vários itens de exame**.

### 1.2. Model `SolicitacaoExame`
- [x] Criar o arquivo/classe do model `SolicitacaoExame`.
- [x] Configurar formatação contínua de datas (Accessors/Mutators).
- [x] Definir de maneira estática ou em arquivo externo as constantes/Enums de **STATUS permitidos** para validações no código.
- [x] Criar relacionamento `1:N` (hasMany): **Uma solicitação possui vários itens de exame**.

### 1.3. Model `ItensExame`
- [x] Criar o arquivo/classe do model `ItensExame`.
- [x] Criar relacionamento `N:1` (belongsTo): **Este item pertence a UMA solicitação de exame**.
- [x] Criar relacionamento `N:1` (belongsTo): **Este item referencia UM tipo de exame**.

---

## 🛡️ Fase 2: Validações e Regras de Negócio (Camada de Serviço ou Requests)
*Antes dos dados entrarem no banco a partir da integração com o front de React, precisamos garanti-los.*

- [x] Implementar regra: Uma solicitação precisa de **no mínimo 1 tipo de exame**.
- [x] Implementar regra: Não permitir inserir duas vezes o mesmo `tipo_exame` dentro da mesma `solicitacao_exame`.
- [x] Implementar validação: Checar fluxo lógico de transição do fluxo de status (Não permitir pular de `pendente` direto para `concluido` caso seja necessário passar pelo `em_andamento`, etc).
- [x] Bloquear a inclusão de solicitações com `data_solicitacao` no passado se o requisito de negócio não permitir retroatividade de dados.

---

## 🧪 Fase 3: Mock de Dados e Testes Iniciais
*Objetivo: Garantir que o trabalho está robusto antes de integrar ao front-end.*

- [ ] Criar um **Seeder** para popular a tabela `tipo_exame` com dados primários (Ex: "Hemograma", "Raio-X de Tórax", "Ecocardiograma").
- [ ] Realizar inserts ou rodar **Factories** (Scripts de Mock) para gerar algumas solicitações e itens de brincadeira com status variados.
- [ ] (Opcional) Implementar testes unitários para assegurar as regras de relacionamento nos models.
- [ ] Testar uma requisição de listagem manual extraindo todas as solicitações, trazendo **os itens associados (eager loading)** para garantir que as queries geradas estão corretas.

---

## 🔮 Fase 4: Boas Práticas e Melhorias Futuras (Opcional)

- [ ] **Soft Deletes:** Implementar exclusão lógica. No setor de saúde (projetos hospitalares), os registros raramente são deletados do banco via `DELETE FROM`, mas sim ocultados. 
- [ ] **Database Transactions:** Garantir que o Controller que for salvar a solicitação e seus exames use *Transações*. Assim, se a gravação do item falhar no banco, a solicitação em si sofre *Rollback*, não deixando registros incompletos.
- [ ] **Logs de Auditoria:** Arquitetar um observer ou tracking de eventos para salvar no banco *QUEM* e *QUANDO* o status mudou (ex: de Pendente para Concluído).
- [ ] **Paginação de Resultados:** Configurar limite e paginação no método de busca para solicitações, visando performar e não travar o Frontend React com listas imensamente extensas.
