# Hospital API — Equipe 1
### Sistema de Login e Gerenciamento de Acesso
**Instituto Federal de Santa Catarina**

---

## Visão geral

Esta API é o **centro de autenticação** do sistema hospitalar. Todas as outras equipes devem:

1. Chamar `POST /api/auth/login` para obter um **token JWT**
2. Incluir esse token em **todas** as demais requisições no header:
   ```
   Authorization: Bearer {token}
   ```
3. Registrar ações dos seus sistemas via `POST /api/logs`
4. Criar usuários para novas entidades via `POST /api/usuarios/registrar`

---

## Tecnologias

| Camada        | Tecnologia                          |
|---------------|-------------------------------------|
| Framework     | Laravel 11                          |
| Autenticação  | JWT via `tymon/jwt-auth`            |
| Banco de dados| MySQL 8                             |
| Documentação  | Swagger via `darkaonline/l5-swagger`|
| E-mail        | Laravel Mail (SMTP)                 |

---

## Instalação

### Pré-requisitos
- PHP 8.2+
- Composer
- Acesso ao banco MySQL do projeto

### Passo a passo

```bash
# 1. Clone o repositório
git clone https://github.com/ifsc-hospital/equipe1-auth-api.git
cd equipe1-auth-api

# 2. Instale as dependências
composer install

# 3. Configure o ambiente
nano .env

# 5. Gere a chave da aplicação
php artisan key:generate

# 6. Gere a chave JWT
php artisan jwt:secret

# 7. Execute as migrations (cria as tabelas no banco)
php artisan migrate

# 8. Gere a documentação Swagger
php artisan l5-swagger:generate

# 9. Suba o servidor
php artisan serve
```

A API estará em: `http://localhost:8000`
A documentação Swagger estará em: `http://localhost:8000/api/documentacao`

---

## Endpoints

### Autenticação (pública)

| Método | Endpoint                                | Descrição                          |
|--------|-----------------------------------------|------------------------------------|
| POST   | `/api/auth/login`                       | Login — retorna token JWT          |
| POST   | `/api/auth/alterar-senha-primeiro-acesso` | Troca senha temporária (1º acesso) |

### Autenticação (protegida — exige token)

| Método | Endpoint               | Descrição                |
|--------|------------------------|--------------------------|
| GET    | `/api/auth/me`         | Dados do usuário logado  |
| POST   | `/api/auth/logout`     | Logout (invalida token)  |
| POST   | `/api/auth/alterar-senha` | Trocar senha            |

### Usuários

| Método | Endpoint                   | Descrição                                    |
|--------|----------------------------|----------------------------------------------|
| POST   | `/api/usuarios/registrar`  | **Para outras equipes**: criar usuário       |
| GET    | `/api/usuarios`            | Listar usuários (filtro: `?funcao=medico`)   |
| GET    | `/api/usuarios/{id}`       | Buscar usuário por ID                        |
| PUT    | `/api/usuarios/{id}`       | Atualizar usuário                            |
| DELETE | `/api/usuarios/{id}`       | Remover usuário                              |

### Médicos

| Método | Endpoint                 | Descrição                                      |
|--------|--------------------------|------------------------------------------------|
| GET    | `/api/medicos`           | Listar médicos (consumido pelo Grupo 2)        |
| POST   | `/api/medicos`           | Cadastrar médico (cria usuário automaticamente)|
| GET    | `/api/medicos/{id}`      | Buscar médico por ID                           |
| PUT    | `/api/medicos/{id}`      | Atualizar médico                               |
| DELETE | `/api/medicos/{id}`      | Inativar médico                                |
| GET    | `/api/medicos/{id}/agenda` | Ver agenda de um médico (consumido Grupo 2)  |

### Agenda (Plantões)

| Método | Endpoint          | Descrição                                          |
|--------|-------------------|----------------------------------------------------|
| GET    | `/api/agenda`     | Listar plantões (filtros: `?id_medico=1&data=YYYY-MM-DD`) |
| POST   | `/api/agenda`     | Criar plantão                                      |
| GET    | `/api/agenda/{id}`| Buscar plantão por ID                              |
| PUT    | `/api/agenda/{id}`| Atualizar plantão                                  |
| DELETE | `/api/agenda/{id}`| Remover plantão                                    |

### Logs

| Método | Endpoint    | Descrição                                    |
|--------|-------------|----------------------------------------------|
| GET    | `/api/logs` | Listar logs (filtros: `?id_usuario=1&data_inicio=YYYY-MM-DD&data_fim=YYYY-MM-DD`) |
| POST   | `/api/logs` | **Para outras equipes**: registrar uma ação  |

---

## Fluxo de integração para outras equipes

### 1. Fazer login e obter token
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "recepcionista@hospital.com",
  "senha": "SuaSenha@123"
}
```
**Resposta:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "tipo": "Bearer",
  "expira_em": 86400,
  "primeiro_acesso": false,
  "usuario": {
    "id": 3,
    "nome": "Ana Recepcionista",
    "email": "recepcionista@hospital.com",
    "funcao": "recepcionista",
    "id_cadastro": 7
  }
}
```

### 2. Usar o token nas próximas requisições
```http
GET /api/medicos
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### 3. Criar um novo usuário (ex: Grupo 2 cria um paciente)
```http
POST /api/usuarios/registrar
Authorization: Bearer {token}
Content-Type: application/json

{
  "email": "paciente@email.com",
  "funcao": "paciente",
  "id_cadastro": 42,
  "nome": "Maria Santos"
}
```
O sistema gera uma senha aleatória e envia por e-mail para `paciente@email.com`.

### 4. Registrar uma ação no log
```http
POST /api/logs
Authorization: Bearer {token}
Content-Type: application/json

{
  "descricao": "Paciente Maria Santos (ID 42) fez check-in às 09:30 do dia 15/04/2026."
}
```

### 5. Fluxo de primeiro acesso do usuário
Quando `primeiro_acesso: true` no login:
```http
POST /api/auth/alterar-senha-primeiro-acesso
Content-Type: application/json

{
  "email": "paciente@email.com",
  "senha_atual": "SenhaTempGerada123!",
  "nova_senha": "MinhaSenhaDefinitiva@2026",
  "nova_senha_confirmation": "MinhaSenhaDefinitiva@2026"
}
```

---

## Funções (roles) disponíveis

| Valor          | Descrição                              |
|----------------|----------------------------------------|
| `administrador`| Acesso total ao sistema                |
| `medico`       | Médicos — gerenciados pelo Grupo 1     |
| `farmaceutico` | Farmacêuticos — criados pelo Grupo 4   |
| `recepcionista`| Recepcionistas — criados pelo Grupo 2  |
| `paciente`     | Pacientes — criados pelo Grupo 2       |

---

## Estrutura do banco de dados

```
usuario          — Usuários do sistema (autenticação)
medico           — Cadastro de médicos
endereco         — Endereços (usado por médicos)
agenda           — Plantões dos médicos
historico_log    — Log de ações (imutável)
```

**Observação:** As tabelas `medico`, `endereco` e `agenda` possuem
`data_criacao` e `data_alteracao`. A tabela `historico_log` possui apenas
`data_hora` (logs são imutáveis por natureza).

---

## Segurança

- Senhas armazenadas com **bcrypt** (nunca em texto puro)
- Tokens JWT com **expiração de 1 dia**
- Validação de dados de entrada em todos os endpoints
- Proteção contra SQL Injection via Eloquent ORM (queries parametrizadas)
- Campos sensíveis (`senha`) excluídos das respostas JSON

---

## Documentação Swagger

Acesse: `http://localhost:8000/api/documentacao`

Para regenerar a documentação após alterações:
```bash
php artisan l5-swagger:generate
```

---

## Padrão de commits

```
feat: adiciona endpoint de login
fix: corrige validação do CPF do médico
docs: atualiza README com exemplos de integração
refactor: extrai lógica de e-mail para SenhaService
```
