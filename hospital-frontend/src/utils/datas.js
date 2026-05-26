/**
 * Utilitários de data — formatação consistente para qualquer formato da API.
 *
 * A API pode devolver datas em três formatos possíveis:
 *  - "2026-05-26"                          (YYYY-MM-DD)
 *  - "2026-05-26 00:00:00"                 (datetime sem timezone)
 *  - "2026-05-26T00:00:00.000000Z"         (ISO 8601, padrão Eloquent)
 *
 * Estas funções aceitam qualquer um e devolvem o formato correto.
 */

/**
 * Converte qualquer string de data da API em um objeto Date local válido.
 * Retorna `null` se a entrada for inválida.
 *
 * IMPORTANTE: usa apenas a parte da data (YYYY-MM-DD), ignorando hora/timezone.
 * Isso evita o "bug do fuso horário": um plantão marcado para "2026-05-26"
 * não pode virar "2026-05-25" só porque o servidor manda em UTC.
 */
function paraDate(valor) {
  if (!valor) return null
  if (valor instanceof Date) {
    return isNaN(valor.getTime()) ? null : valor
  }

  const match = String(valor).match(/^(\d{4})-(\d{2})-(\d{2})/)
  if (!match) return null

  const [, ano, mes, dia] = match
  const data = new Date(Number(ano), Number(mes) - 1, Number(dia))
  return isNaN(data.getTime()) ? null : data
}

/**
 * Formata uma data para exibição em pt-BR: DD/MM/YYYY.
 * Exibe '—' se for inválida.
 */
export function formatarData(valor) {
  const data = paraDate(valor)
  if (!data) return '—'
  return data.toLocaleDateString('pt-BR')
}

/**
 * Converte qualquer formato de data para 'YYYY-MM-DD', formato esperado por <input type="date">.
 * Retorna string vazia se for inválida.
 */
export function paraInputDate(valor) {
  const data = paraDate(valor)
  if (!data) return ''
  const ano = data.getFullYear()
  const mes = String(data.getMonth() + 1).padStart(2, '0')
  const dia = String(data.getDate()).padStart(2, '0')
  return `${ano}-${mes}-${dia}`
}

/**
 * Formata uma data com hora: "26/05/2026 às 14:30".
 * Para logs e timestamps.
 */
export function formatarDataHora(valor) {
  if (!valor) return '—'
  const data = new Date(valor)
  if (isNaN(data.getTime())) return '—'
  return data.toLocaleDateString('pt-BR') + ' às ' +
    data.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
}
