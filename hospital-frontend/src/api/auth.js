import client from './client'

export const authApi = {
  login: (email, senha) =>
    client.post('/auth/login', { email, senha }),

  logout: () =>
    client.post('/auth/logout'),

  me: () =>
    client.get('/auth/me'),

  alterarSenha: (data) =>
    client.post('/auth/alterar-senha', data),

  alterarSenhaPrimeiroAcesso: (data) =>
    client.post('/auth/alterar-senha-primeiro-acesso', data),
}
