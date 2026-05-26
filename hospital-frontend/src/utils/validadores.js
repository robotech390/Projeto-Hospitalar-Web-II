/**
 * Validações de campos comuns. Use estas funções no submit dos formulários,
 * em conjunto com as máscaras de `utils/mascaras.js`.
 */

import { removerMascara } from './mascaras'

/**
 * Valida CPF (formato, tamanho e dígitos verificadores).
 * Aceita CPF com ou sem máscara.
 */
export function cpfValido(valor) {
  const cpf = removerMascara(valor)
  if (cpf.length !== 11) return false
  if (/^(\d)\1{10}$/.test(cpf)) return false

  for (let pos = 9; pos < 11; pos++) {
    let soma = 0
    for (let i = 0; i < pos; i++) {
      soma += Number(cpf[i]) * ((pos + 1) - i)
    }
    const digito = ((10 * soma) % 11) % 10
    if (Number(cpf[pos]) !== digito) return false
  }
  return true
}

export function emailValido(valor) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test((valor || '').trim())
}

/**
 * Valida força mínima de senha:
 *  - Pelo menos 8 caracteres
 *  - Pelo menos uma letra e um número
 */
export function senhaForte(valor) {
  const v = valor || ''
  return v.length >= 8 && /[A-Za-z]/.test(v) && /\d/.test(v)
}

export function telefoneValido(valor) {
  const tel = removerMascara(valor)
  return tel.length === 10 || tel.length === 11
}

export function cepValido(valor) {
  return removerMascara(valor).length === 8
}
