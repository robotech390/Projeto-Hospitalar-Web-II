import client from './client'

export const authApi = {
  login: (email, senha) =>
    client.post('/auth/login', { email, senha }),

  logout: () =>
    client.post('/auth/logout'),

  me: () =>
    client.get('/auth/me'),

  alterarSenha: (dados) =>
    client.post('/auth/alterar-senha', dados),

  alterarSenhaPrimeiroAcesso: (dados) =>
    client.post('/auth/alterar-senha-primeiro-acesso', dados),

  esqueciSenha: (email) =>
    client.post('/auth/esqueci-senha', { email }),

  redefinirSenha: (dados) =>
    client.post('/auth/redefinir-senha', dados),
}
