import client from './client'

export const logsApi = {
  listar: (params) =>
    client.get('/logs', { params }),

  registrar: (descricao) =>
    client.post('/logs', { descricao }),
}
