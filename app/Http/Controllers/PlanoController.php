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
            /*'tiposConsulta' => \App\Models\PlanoCoberturaConsulta::orderBy('descricao')->get(),
            'tiposExame' => \App\Models\PlanoCoberturaExame::orderBy('descricao')->get(),
            'tiposMedicamento' => \App\Models\PlanoCoberturaMedicamento::orderBy('descricao')->get(),*/
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'descricao' => 'required|string|max:255',
            'id_tipo_cobranca' => 'required|exists:App\Models\TipoCobranca,id',
            'id_convenio' => 'required|exists:App\Models\Convenio,id',
        ]);

        Plano::create($request->all());

        return redirect()->back();
    }

    public function update(Request $request, Plano $plano){
        $request->validate([
            'descricao' => 'required|string|max:255',
            'id_tipo_cobranca' => 'required|exists:App\Models\TipoCobranca,id',
            'id_convenio' => 'required|exists:App\Models\Convenio,id',
        ]);

        $plano->update($request->all());

        return redirect()->back()->with('success', 'Plano atualizado com sucesso.');
    }

    public function destroy(Plano $plano){
        $plano->delete();
        return redirect()->back()->with('success', 'Plano excluído com sucesso.');
    }
}