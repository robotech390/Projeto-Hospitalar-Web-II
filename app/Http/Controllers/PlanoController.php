<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Plano;

class PlanoController extends Controller{

    public function index(){
        return Inertia::render('Faturamento/Plano', [
            'planos' => Plano::with(['convenio', 'tipoCobranca'])->orderBy('descricao')->get(),
            'tiposCobranca' => \App\Models\TipoCobranca::orderBy('descricao')->get(),
            'convenios' => \App\Models\Convenio::orderBy('nome')->get(),
            'cobreConsulta' => true,
            'cobreRemedio' => true,
            'cobreExame' => true,
            'percentualCobertura' => 50
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'descricao' => 'required|string|max:255',
            'id_tipo_cobranca' => 'required|exists:App\Models\TipoCobranca,id',
            'id_convenio' => 'required|exists:App\Models\Convenio,id',
            'cobreConsulta' => 'int',
            'cobreRemedio' => 'int',
            'cobreExame' => 'int',
            'percentualCobertura' => 'int'
        ]);

        Plano::create($request->all());

        return redirect()->back();
    }

    public function update(Request $request, Plano $plano){
        $request->validate([
            'descricao' => 'required|string|max:255',
            'id_tipo_cobranca' => 'required|exists:App\Models\TipoCobranca,id',
            'id_convenio' => 'required|exists:App\Models\Convenio,id',
            'cobreConsulta' => 'int',
            'cobreRemedio' => 'int',
            'cobreExame' => 'int',
            'percentualCobertura' => 'int'
        ]);

        $plano->update($request->all());

        return redirect()->back()->with('success', 'Plano atualizado com sucesso.');
    }

    public function destroy(Plano $plano){
        $plano->delete();
        return redirect()->back()->with('success', 'Plano excluído com sucesso.');
    }
}