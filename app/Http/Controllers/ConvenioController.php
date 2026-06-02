<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use Illuminate\Http\Request;
use Inertia\Inertia;
use OpenApi\Attributes as OA;

class ConvenioController extends Controller
{
    #[OA\Get(
        path: "/usuarios",
        summary: "Lista todos os usuários",
        description: "Retorna uma lista paginada de usuários.",
        tags: ["Usuários"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Sucesso",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Usuario")
        )
    )]
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