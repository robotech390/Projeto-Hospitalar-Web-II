# Hospital Frontend — Equipe 1

Frontend React do sistema hospitalar, plugado na API da Equipe 1.

## Tecnologias
- React 18 + Vite
- Tailwind CSS
- React Router DOM
- TanStack Query (React Query)
- Axios
- Recharts
- Lucide React

## Pré-requisito
A API da Equipe 1 deve estar rodando em `http://localhost:8000`.

## Instalação e execução

```bash
npm install
npm run dev
```

Acesse: http://localhost:3000

Login padrão:
- E-mail: admin@hospital.com
- Senha: Admin@123456

## Estrutura

```
src/
├── api/          # Camada de comunicação com a API (axios)
├── components/
│   ├── layout/   # Sidebar, Header, Layout
│   └── ui/       # Badge, Modal (componentes reutilizáveis)
├── contexts/     # AuthContext (JWT)
├── pages/        # Uma página por rota
└── App.jsx       # Rotas
```

## Páginas implementadas
- Login + troca de senha (primeiro acesso)
- Dashboard com gráficos
- Acesso & Usuários (CRUD)
- Médicos (CRUD)
- Recepção & Agenda (plantões)
- Demais módulos: placeholder para outras equipes
