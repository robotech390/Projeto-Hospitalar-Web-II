import client from './client'

export const usuariosApi = {
  listar: (funcao) =>
    client.get('/usuarios', { params: funcao ? { funcao } : {} }),

  buscar: (id) =>
    client.get(`/usuarios/${id}`),

  registrar: (data) =>
    client.post('/usuarios/registrar', data),

  atualizar: (id, data) =>
    client.put(`/usuarios/${id}`, data),

  remover: (id) =>
    client.delete(`/usuarios/${id}`),
}
