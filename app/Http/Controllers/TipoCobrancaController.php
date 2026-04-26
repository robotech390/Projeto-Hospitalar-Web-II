<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TipoCobranca;

class TipoCobrancaController extends Controller
{
    public function index()
    {
        return Inertia::render('Faturamento/TipoCobranca', [
            'tiposCobranca' => TipoCobranca::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $data['descricao'] = strtolower($data['email']);

        TipoCobranca::create($data);

        return redirect()->back();
    }

    public function update(Request $request, TipoCobranca $tipoCobranca){
        $data = $request->all();
        $data['descricao'] = preg_replace('/\D/', '', $data['descricao']);

        $tipoCobranca->update($request->all());
        return redirect()->back();
    }

    public function destroy(TipoCobranca $tipoCobranca){
        $tipoCobranca->delete();
        return redirect()->back();
    }
}