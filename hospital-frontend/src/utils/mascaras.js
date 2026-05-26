/**
 * Máscaras de formatação para inputs.
 * Todas as funções recebem o valor "sujo" e devolvem a versão formatada.
 * Os componentes devem sempre enviar para a API o valor SEM formatação (use `removerMascara`).
 */

export function mascararCpf(valor) {
  const apenasNumeros = (valor || '').replace(/\D/g, '').slice(0, 11)
  return apenasNumeros
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d{1,2})$/, '$1-$2')
}

export function mascararCnpj(valor) {
  const apenasNumeros = (valor || '').replace(/\D/g, '').slice(0, 14)
  return apenasNumeros
    .replace(/(\d{2})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1.$2')
    .replace(/(\d{3})(\d)/, '$1/$2')
    .replace(/(\d{4})(\d{1,2})$/, '$1-$2')
}

export function mascararTelefone(valor) {
  const apenasNumeros = (valor || '').replace(/\D/g, '').slice(0, 11)
  if (apenasNumeros.length <= 10) {
    // Fixo: (XX) XXXX-XXXX
    return apenasNumeros
      .replace(/(\d{2})(\d)/, '($1) $2')
      .replace(/(\d{4})(\d{1,4})$/, '$1-$2')
  }
  // Celular: (XX) XXXXX-XXXX
  return apenasNumeros
    .replace(/(\d{2})(\d)/, '($1) $2')
    .replace(/(\d{5})(\d{1,4})$/, '$1-$2')
}

export function mascararCep(valor) {
  const apenasNumeros = (valor || '').replace(/\D/g, '').slice(0, 8)
  return apenasNumeros.replace(/(\d{5})(\d{1,3})$/, '$1-$2')
}

export function removerMascara(valor) {
  return (valor || '').replace(/\D/g, '')
}
