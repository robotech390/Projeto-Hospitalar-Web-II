<?php

namespace App\Http\Controllers;

use App\Models\TipoCobranca;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TipoCobrancaController extends Controller
{
    public function index()
    {
        return Inertia::render('Faturamento/TipoCobranca', [
            'tipoCobrancas' => TipoCobranca::orderBy('descricao')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

        TipoCobranca::create($dados);

        return redirect()
            ->route('tipo-cobranca')
            ->with('success', 'Tipo de cobrança cadastrado com sucesso.');
    }

    public function update(Request $request, TipoCobranca $tipoCobranca)
    {
        $dados = $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

        $tipoCobranca->update($dados);

        return redirect()
            ->route('tipo-cobranca')
            ->with('success', 'Tipo de cobrança atualizado com sucesso.');
    }

    public function destroy(TipoCobranca $tipoCobranca)
    {
        $tipoCobranca->delete();

        return redirect()
            ->route('tipo-cobranca')
            ->with('success', 'Tipo de cobrança excluído com sucesso.');
    }
}