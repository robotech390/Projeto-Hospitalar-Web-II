import client from './client'

export const agendaApi = {
  listar: (params) =>
    client.get('/agenda', { params }),

  buscar: (id) =>
    client.get(`/agenda/${id}`),

  criar: (data) =>
    client.post('/agenda', data),

  atualizar: (id, data) =>
    client.put(`/agenda/${id}`, data),

  remover: (id) =>
    client.delete(`/agenda/${id}`),
}
