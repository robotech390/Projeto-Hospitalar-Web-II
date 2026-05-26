import { forwardRef } from 'react'
import { mascararCpf, mascararCnpj, mascararTelefone, mascararCep } from '../../utils/mascaras'

const MASCARAS = {
  cpf:      mascararCpf,
  cnpj:     mascararCnpj,
  telefone: mascararTelefone,
  cep:      mascararCep,
}

/**
 * Input com máscara aplicada na digitação.
 * O valor recebido em `onChange` já vem formatado — use `removerMascara` antes de enviar à API.
 *
 * Exemplo:
 *   <InputMascarado mascara="cpf" value={cpf} onChange={(v) => setCpf(v)} />
 */
const InputMascarado = forwardRef(function InputMascarado(
  { mascara, value, onChange, className = 'input', ...resto },
  ref
) {
  const aplicarMascara = MASCARAS[mascara] || ((v) => v)

  const handleChange = (e) => {
    const valorFormatado = aplicarMascara(e.target.value)
    onChange?.(valorFormatado)
  }

  return (
    <input
      ref={ref}
      type="text"
      value={value || ''}
      onChange={handleChange}
      className={className}
      {...resto}
    />
  )
})

export default InputMascarado
