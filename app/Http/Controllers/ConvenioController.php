<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Endereco;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Convenio;

class ConvenioController extends Controller
{
    public function index()
    {
        return Inertia::render('Faturamento/Convenio', [
            'convenios' => Convenio::with('endereco')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $endereco = Endereco::create([
            'logradouro' => $request->logradouro,
            'numero' => $request->numero,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'cep' => preg_replace('/\D/', '', $request->cep),
        ]);

        Convenio::create([
            'nome' => $request->nome,
            'cnpj' => preg_replace('/\D/', '', $request->cnpj),
            'telefone' => preg_replace('/\D/', '', $request->telefone),
            'email' => strtolower($request->email),
            'id_endereco' => $endereco->id,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Convenio $convenio){
        $data = $request->all();
        $data['cnpj'] = preg_replace('/\D/', '', $data['cnpj']);

        $convenio ->update($data);
        
        if($convenio->id_endereco){
            $convenio->endereco()->update([
                'logradouro' => $request->logradouro,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'cep' => preg_replace('/\D/', '', $request->cep),
            ]);
        }
        
        return redirect()->back();
    }

    public function destroy(Convenio $convenio){
        $convenio->delete();
        return redirect()->back();
    }
}
