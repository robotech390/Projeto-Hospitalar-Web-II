<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pessoa;
use App\Models\Medico;
use App\Models\Consulta;

class ProntuarioController extends Controller
{
    //leva á tela do prontuario js/Pages/Prontuario.jsx
    public function index()
    {
        $medicoPessoaIds = Medico::pluck('id_pessoa');
        $pacientes = Pessoa::whereNotIn('id', $medicoPessoaIds)->get();
        $consultas = Consulta::with(['medico.pessoa', 'paciente'])->get();
        //js/Pages/Prontuario.jsx
        return view('app', [
            'page' => 'prontuario',
            'props' => [
                'pacientes' => $pacientes,
                'consultas' => $consultas,
            ],
        ]);
    }
}