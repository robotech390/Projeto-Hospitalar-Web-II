import client from './client'

/**
 * Endpoints do próprio usuário logado sobre si mesmo (sem privilégios de admin).
 */
export const meuPerfilApi = {
  buscar: () =>
    client.get('/meu-perfil'),

  atualizar: (dados) =>
    client.put('/meu-perfil', dados),

  minhaAgenda: (params) =>
    client.get('/meu-perfil/agenda', { params }),

  meuHistorico: (params) =>
    client.get('/meu-perfil/historico', { params }),
}
