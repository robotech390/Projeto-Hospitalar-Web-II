<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\SolicitacaoExame;

class ValidStatusTransition implements ValidationRule
{
    protected $currentStatus;

    public function __construct($currentStatus)
    {
        $this->currentStatus = $currentStatus;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->currentStatus === $value) {
            return;
        }

        $transitions = [
            SolicitacaoExame::STATUS_PENDENTE => [
                SolicitacaoExame::STATUS_EM_ANDAMENTO
            ],
            SolicitacaoExame::STATUS_EM_ANDAMENTO => [
                SolicitacaoExame::STATUS_COLETADO
            ],
            SolicitacaoExame::STATUS_COLETADO => [
                SolicitacaoExame::STATUS_EM_ANALISE
            ],
            SolicitacaoExame::STATUS_EM_ANALISE => [
                SolicitacaoExame::STATUS_CONCLUIDO
            ],
            SolicitacaoExame::STATUS_CONCLUIDO => []
        ];

        if (!isset($transitions[$this->currentStatus]) || !in_array($value, $transitions[$this->currentStatus])) {
            $fail('A transição do status "'.$this->currentStatus.'" para "'.$value.'" não é permitida. Siga o fluxo lógico de status.');
        }
    }
}
