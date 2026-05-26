import client from './client'

export const usuariosApi = {
  listar: (funcao) =>
    client.get('/usuarios', { params: funcao ? { funcao } : {} }),

  buscar: (id) =>
    client.get(`/usuarios/${id}`),

  registrar: (dados) =>
    client.post('/usuarios/registrar', dados),

  atualizar: (id, dados) =>
    client.put(`/usuarios/${id}`, dados),

  remover: (id) =>
    client.delete(`/usuarios/${id}`),

  reenviarSenha: (id) =>
    client.post(`/usuarios/${id}/reenviar-senha`),
}
