<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Regra de validação de CPF.
 * Valida formato, tamanho e os dois dígitos verificadores.
 */
class Cpf implements ValidationRule
{
    public function validate(string $atributo, mixed $valor, Closure $falha): void
    {
        $cpf = preg_replace('/\D/', '', (string) $valor);

        if (strlen($cpf) !== 11) {
            $falha('O CPF deve conter 11 dígitos.');
            return;
        }

        // Rejeita CPFs com todos os dígitos iguais (000.000.000-00, 111.111.111-11, etc.)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            $falha('CPF inválido.');
            return;
        }

        // Calcula os dois dígitos verificadores
        for ($pos = 9; $pos < 11; $pos++) {
            $soma = 0;
            for ($i = 0; $i < $pos; $i++) {
                $soma += (int) $cpf[$i] * (($pos + 1) - $i);
            }
            $digito = ((10 * $soma) % 11) % 10;
            if ((int) $cpf[$pos] !== $digito) {
                $falha('CPF inválido.');
                return;
            }
        }
    }
}
