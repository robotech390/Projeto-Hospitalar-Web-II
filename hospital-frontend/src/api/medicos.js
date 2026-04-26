import client from './client'

export const medicosApi = {
  listar: (params) =>
    client.get('/medicos', { params }),

  buscar: (id) =>
    client.get(`/medicos/${id}`),

  criar: (data) =>
    client.post('/medicos', data),

  atualizar: (id, data) =>
    client.put(`/medicos/${id}`, data),

  inativar: (id) =>
    client.delete(`/medicos/${id}`),

  agenda: (id, data) =>
    client.get(`/medicos/${id}/agenda`, { params: data ? { data } : {} }),
}
