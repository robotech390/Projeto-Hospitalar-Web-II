/**
 * Regras de acesso por função.
 *
 * Cada rota lista as funções autorizadas. Funções não listadas em uma rota
 * são automaticamente bloqueadas (exceto rotas sem entrada aqui, que são públicas).
 */
const PERMISSOES_POR_ROTA = {
  // Rotas do administrador
  '/usuarios': ['administrador'],
  '/medicos':  ['administrador'],
  '/agenda':   ['administrador'],

  // Rotas do médico (perfil próprio)
  '/minha-agenda':    ['medico'],
  '/meu-historico':   ['medico'],

  // Rotas compartilhadas
  '/':            ['administrador', 'medico', 'farmaceutico', 'recepcionista', 'paciente'],
  '/meu-perfil':  ['administrador', 'medico', 'farmaceutico', 'recepcionista', 'paciente'],
}

export function podeAcessarRota(usuario, caminho) {
  if (!usuario) return false

  // Procura a regra mais específica que casa com o caminho
  const rotaCorrespondente = Object.keys(PERMISSOES_POR_ROTA)
    .sort((a, b) => b.length - a.length)
    .find((rota) => caminho === rota || caminho.startsWith(rota + '/'))

  if (!rotaCorrespondente) return true

  return PERMISSOES_POR_ROTA[rotaCorrespondente].includes(usuario.funcao)
}

export function filtrarMenus(usuario, menus) {
  return menus
    .map((secao) => ({
      ...secao,
      items: secao.items.filter((item) => podeAcessarRota(usuario, item.to)),
    }))
    .filter((secao) => secao.items.length > 0)
}

export function ehAdministrador(usuario) {
  return usuario?.funcao === 'administrador'
}

export function ehMedico(usuario) {
  return usuario?.funcao === 'medico'
}
