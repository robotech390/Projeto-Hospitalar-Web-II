<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Convenio;

class ConvenioController extends Controller
{
    public function index()
    {
        return Inertia::render('Faturamento/Convenio', [
            'convenios' => Convenio::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $data['cnpj'] = preg_replace('/\D/', '', $data['cnpj']);
        $data['telefone'] = preg_replace('/\D/', '', $data['telefone']);
        $data['email'] = strtolower($data['email']);

        Convenio::create($data);

        return redirect()->back();
    }

    public function update(Request $request, Convenio $convenio){
        $data = $request->all();
        $data['cnpj'] = preg_replace('/\D/', '', $data['cnpj']);

        $convenio->updated($request->all());
        return redirect()->back();
    }

    public function destroy(Convenio $convenio){
        $convenio->delete();
        return redirect()->back();
    }
}
