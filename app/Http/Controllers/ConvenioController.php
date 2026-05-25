<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConvenioController extends Controller
{
    public function index()
    {
        return Inertia::render('Faturamento/Convenio', [
            'convenios' => Convenio::orderBy('nome')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20',
            'telefone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
        ]);

        Convenio::create($dados);

        return redirect()
            ->route('convenio')
            ->with('success', 'Convênio cadastrado com sucesso.');
    }

    public function update(Request $request, Convenio $convenio)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20',
            'telefone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
        ]);

        $convenio->update($dados);

        return redirect()
            ->route('convenio')
            ->with('success', 'Convênio atualizado com sucesso.');
    }

    public function destroy(Convenio $convenio)
    {
        $convenio->delete();

        return redirect()
            ->route('convenio')
            ->with('success', 'Convênio excluído com sucesso.');
    }
}