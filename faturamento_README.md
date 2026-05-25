# Documentação do Módulo de Faturamento — Saúde+VC

## 1. Visão geral

Este documento descreve as alterações realizadas no módulo de faturamento do sistema hospitalar **Saúde+VC**, desenvolvido para a disciplina de Projeto Web II.

O objetivo principal desta etapa foi estruturar e melhorar a interface das telas relacionadas ao faturamento, mantendo a identidade visual proposta para o sistema e preparando as telas para integração com o backend.

As telas contempladas foram:

- Convênios;
- Planos;
- Tipos de Cobrança.

---

## 2. Tecnologias utilizadas

O projeto utiliza as seguintes tecnologias:

### Backend

- Laravel;
- PHP;
- Migrations;
- Controllers;
- Models.

### Frontend

- React;
- Inertia.js;
- Tailwind CSS.

### Controle de versão

- Git;
- GitHub;
- Branch utilizada: `equipe6`.

---

## 3. Identidade visual

A interface foi ajustada para seguir a identidade visual do sistema **Saúde+VC**, conforme o material visual fornecido.

Foram aplicados os seguintes elementos:

- Menu lateral em verde escuro;
- Logo Saúde+VC no topo do menu;
- Cabeçalho branco com campo de busca;
- Área de usuário administrativo;
- Cards brancos com bordas arredondadas;
- Fundo cinza claro;
- Botões em tom verde-petróleo;
- Estilo limpo, profissional e compatível com sistema hospitalar.

O objetivo foi manter uma aparência consistente com o layout de referência entregue pelo professor.

---

## 4. Estrutura visual implementada

Foi criado/ajustado um layout compartilhado para as telas de faturamento:

```txt
resources/js/Components/Faturamento/FaturamentoLayout.jsx