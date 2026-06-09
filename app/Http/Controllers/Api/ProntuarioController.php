<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use App\Models\Medico;
use App\Models\Consulta;
use App\Models\Receita;
use App\Models\SolicitacaoExame;

class ProntuarioController extends Controller
{
    public function index()
    {
        $medicoPessoaIds = Medico::pluck('id_pessoa');
        $pacientes = Pessoa::whereNotIn('id', $medicoPessoaIds)->get();
        $consultas = Consulta::with(['medico.pessoa', 'paciente', 'diagnosticos'])->get();
        $receitas = Receita::with(['consulta', 'medicamentos.medicamento'])->get();
        $solicitacoesExame = SolicitacaoExame::with(['consulta', 'itens.tipoExame'])->get();

        return view('app', [
            'page' => 'prontuario',
            'props' => [
                'pacientes' => $pacientes,
                'consultas' => $consultas,
                'receitas' => $receitas,
                'solicitacoesExame' => $solicitacoesExame,
            ],
        ]);
    }
}
