# Laboratório e Exames — Equipe 5

## Visão geral

Este repositório implementa o módulo de **laboratório**, com APIs para:
- Tipos de exame
- Solicitações de exame
- Itens de exame

## Tecnologias principais

| Camada | Tecnologia |
| --- | --- |
| Backend | Laravel 12 |
| Frontend | React + Inertia.js |
| Autenticação | Laravel Sanctum (API) |
| Comunicação | Axios + React Query |
| Estilização | Tailwind CSS |
| Documentação | L5-Swagger |

## Objetivo deste README

Este documento orienta outras equipes sobre como consumir as APIs da Equipe 5 e como integrar essas chamadas ao fluxo de autenticação centralizado.

## Instalação

### Pré-requisitos
- PHP 8.2+
- Composer
- Node.js + npm
- Banco de dados configurado (MySQL, SQLite ou outro suportado)

### Passo a passo

```bash
cd equipe5
composer install
cp .env.example .env
php artisan key:generate
npm install
php artisan migrate
npm run dev
php artisan serve
```

A aplicação ficará disponível em: `https://autorack.proxy.rlwy.net`

## Integração entre equipes

Esta API não fornece o endpoint de login principal. A autenticação deve ser feita através do serviço de auth da Equipe 1, que retorna um token JWT.

Após obter o token de autenticação da Equipe 1, envie-o em todas as requisições para os endpoints da Equipe 5.

### Header obrigatório

```http
Authorization: Bearer <TOKEN>
Accept: application/json
```

### Validação de token

Utilize o endpoint abaixo para validar o token e obter os dados do usuário autenticado:

| Método | Endpoint | Descrição |
| --- | --- | --- |
| `GET` | `/api/user` | Retorna o usuário autenticado |

> Se o token for inválido ou expirado, a API responderá com `401 Unauthorized`.

## Endpoints da Equipe 5

Todas as rotas abaixo estão protegidas por autenticação em `routes/api.php`.

| Método | Endpoint | Descrição |
| --- | --- | --- |
| `GET` | `/api/tipos-exame` | Lista todos os tipos de exame |
| `POST` | `/api/tipos-exame` | Cria um novo tipo de exame |
| `GET` | `/api/tipos-exame/{id}` | Retorna um tipo de exame pelo ID |
| `PUT` / `PATCH` | `/api/tipos-exame/{id}` | Atualiza um tipo de exame |
| `DELETE` | `/api/tipos-exame/{id}` | Remove um tipo de exame |
| `GET` | `/api/solicitacoes` | Lista todas as solicitações |
| `POST` | `/api/solicitacoes` | Cria uma nova solicitação de exame |
| `GET` | `/api/solicitacoes/{id}` | Retorna uma solicitação pelo ID |
| `PUT` / `PATCH` | `/api/solicitacoes/{id}` | Atualiza uma solicitação |
| `DELETE` | `/api/solicitacoes/{id}` | Remove uma solicitação |
| `GET` | `/api/itens-exame` | Lista todos os itens de exame |
| `POST` | `/api/itens-exame` | Cria um novo item de exame |
| `GET` | `/api/itens-exame/{id}` | Retorna um item de exame pelo ID |
| `PUT` / `PATCH` | `/api/itens-exame/{id}` | Atualiza um item de exame |
| `DELETE` | `/api/itens-exame/{id}` | Remove um item de exame |

## Documentação da API (Swagger)

A API do Laboratório conta com uma documentação interativa e completa gerada com o **L5-Swagger**. Esta documentação segue a especificação **OpenAPI** e permite que outras equipes visualizem, interajam e testem todos os endpoints diretamente do navegador.

### Como Acessar

1. Certifique-se de que a aplicação está rodando (`php artisan serve`).
2. Acesse a seguinte URL no seu navegador:
   ```
   http://127.0.0.1:8000/api/documentation
   ```

### Funcionalidades do Painel:
- **Visualização Completa dos Schemas:** Descrição detalhada dos parâmetros de entrada e dos modelos de resposta JSON para todos os 8 endpoints reais.
- **Teste de Endpoints ("Try it out"):** Envie requisições reais diretamente da documentação pelo navegador.
- **Autenticação com JWT:**
  1. Clique no botão **"Authorize"** (cadeado verde) no topo da tela.
  2. Insira o token JWT retornado pelo login da Equipe 1 no formato `Bearer <seu_token>`.
  3. Clique em *Authorize*. Pronto! Todas as chamadas de teste serão interceptadas e validadas de forma transparente pelo nosso middleware central `auth.jwt`.

## Fluxo de integração recomendado

### 1. Autenticar no serviço de auth da Equipe 1

A equipe consumidora deve chamar o endpoint de login da Equipe 1, por exemplo:

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "usuario@hospital.com",
  "senha": "SenhaSegura123"
}
```

### 2. Usar o token em todas as chamadas da Equipe 5

```http
GET /api/solicitacoes
Authorization: Bearer <TOKEN>
Accept: application/json
```

### 3. Validar o token contra `/api/user`

```http
GET /api/user
Authorization: Bearer <TOKEN>
Accept: application/json
```

### 4. Se disponível, enviar logs ao serviço centralizado

Se existir um serviço de logs compartilhado, registre ações importantes (criação de solicitação, alteração de exame, exclusão, etc.) nesse serviço.

## Exemplo de consumo com Axios

```js
import axios from 'axios';

const api = axios.create({
  baseURL: 'https://autorack.proxy.rlwy.net/api',
  headers: {
    Accept: 'application/json',
    Authorization: `Bearer ${token}`,
  },
});

const tipos = await api.get('/tipos-exame');
const solicitar = await api.post('/solicitacoes', {
  paciente_id: 1,
  tipo_exame_id: 2,
  data_prevista: '2026-06-01',
});
```

## Exemplo de requisição cURL

```bash
curl -X GET "https://autorack.proxy.rlwy.net/api/tipos-exame" \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Accept: application/json"
```

## Boas práticas entre equipes

- Sempre envie `Accept: application/json`.
- Valide o token chamando `GET /api/user` antes de chamar outros endpoints.
- Trate corretamente os códigos HTTP:
  - `200` / `201` — sucesso
  - `400` — requisição inválida
  - `401` — não autorizado
  - `404` — recurso não encontrado
- Mantenha a URL base configurável para não depender de `localhost` em produção.

## Observações

- Esta API foca no módulo de laboratório e exames.
- Para autenticação, usuários e logs, consulte a documentação da Equipe 1.
