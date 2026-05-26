import axios from 'axios'

const client = axios.create({
  baseURL: '/api',
  headers: { 'Content-Type': 'application/json' },
})

// Injeta o token JWT em todas as requisições
client.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// Interceptor de resposta:
//  - Redireciona ao login se o token expirar (401)
//  - Normaliza a estrutura do erro para sempre ter `mensagem` e `erros`
//    independente do que a API retornou
client.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err.response?.status === 401 && !err.config?.url?.includes('/auth/')) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }

    // Padroniza o objeto de erro para o restante da aplicação
    const dados = err.response?.data || {}
    err.mensagemAmigavel =
         dados.mensagem
      || dados.message
      || (dados.erros && Object.values(dados.erros).flat()[0])
      || (dados.errors && Object.values(dados.errors).flat()[0])
      || 'Não foi possível concluir a operação. Tente novamente.'

    err.errosPorCampo = dados.erros || dados.errors || {}

    return Promise.reject(err)
  }
)

export default client

/**
 * Extrai a mensagem amigável de um erro da API.
 */
export function extrairMensagemErro(err, fallback = 'Erro ao executar a operação.') {
  return err?.mensagemAmigavel || fallback
}
