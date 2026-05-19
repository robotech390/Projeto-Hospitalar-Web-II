<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Plano;

class PlanoController extends Controller{

    public function index(){
        return Inertia::render('Faturamento/Plano', [
            'plano' => Plano::all()
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $data['descricao'] = strtolower($data['descricao']);

        Plano::create($data);

        return redirect()->back();
    }

    public function update(Request $request, Plano $plano){
        $data = $request->all();
        $data['descricao'] = strtolower($data['descricao']);
        $plano->update($data);

        return redirect()->back();
    }

    public function destroy(Plano $plano){
        $plano->delete();
        return redirect()->back();
    }
}
