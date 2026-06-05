<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Endereco;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Convenio;
use OpenApi\Attributes as OA;

class ConvenioController extends Controller
{
    #[OA\Get(
        path: "/api/convenios",
        summary: "Listar todos os convênios",
        description: "Retorna uma lista de todos os convênios médicos cadastrados.",
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de convênios retornada com sucesso."
            ),
            new OA\Response(
                response: 500,
                description: "Erro interno do servidor."
            )
        ]
    )]
    public function index()
    {
        return Inertia::render('Faturamento/Convenio', [
            'convenios' => Convenio::with('endereco')->get()
        ]);
    }

    #[OA\Post(
        path: "/api/convenios",
        summary: "Criar um novo convênio",
        description: "Cria um novo convênio médico.",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Dados necessários para criar um convênio",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    // required: ["nome", "registro_ans"], // Campos obrigatórios
                    required: ["nome", "cnpj", "telefone", "email", "id_endereco", "data_criacao", "data_atualizacao"],
                    properties: [
                        new OA\Property(
                            property: "nome",
                            description: "Nome do convênio médico",
                            type: "string",
                            example: "Amil Saúde"
                        ),
                        new OA\Property(
                            property: "cnpj",
                            description: "CNPJ do convênio médico",
                            type: "string",
                            example: "123456"
                        ),
                        new OA\Property(
                            property: "telefone",
                            description: "Telefone do convênio médico",
                            type: "string",
                            example: "(11) 91234-5678"
                        ),
                        new OA\Property(
                            property: "email",
                            description: "Email do convênio médico",
                            type: "string",
                            example: "contato@amil.com.br"
                        ),
                        new OA\Property(
                            property: "id_endereco",
                            description: "ID do endereço do convênio médico",
                            type: "string",
                            example: "1"
                        ),
                        new OA\Property(
                            property: "data_criacao",
                            description: "Data de criação do convênio médico",
                            type: "string",
                            format: "date-time",
                            example: "2023-01-01T00:00:00Z"
                        ),
                        new OA\Property(
                            property: "data_atualizacao",
                            description: "Data de atualização do convênio médico",
                            type: "string",
                            format: "date-time",
                            example: "2023-01-01T00:00:00Z"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Convênio criado com sucesso."
            ),
            new OA\Response(
                response: 500,
                description: "Erro interno do servidor."
            )
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $id_endereco = null;

        if ($request->filled('rua') || $request->filled('numero') || $request->filled('cidade') || $request->filled('estado') || $request->filled('cep')) {
            $endereco = Endereco::create([
                'logradouro' => $request->rua,
                'numero' => $request->numero,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'cep' => preg_replace('/\D/', '', $request->cep),
            ]);
            $id_endereco = $endereco->id;
        }

        Convenio::create([
            'nome' => $request->nome,
            'cnpj' => preg_replace('/\D/', '', $request->cnpj),
            'telefone' => preg_replace('/\D/', '', $request->telefone),
            'email' => strtolower($request->email),
            'id_endereco' => $endereco->id,
        ]);

        return redirect()->back();
    }

    #[OA\Put(
        path: "/api/convenios/{convenio}",
        summary: "Atualizar um convênio existente",
        description: "Atualiza as informações de um convênio médico existente.",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Dados necessários para criar um convênio",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    // required: ["nome", "registro_ans"], // Campos obrigatórios
                    required: ["nome", "cnpj", "telefone", "email", "id_endereco", "data_criacao", "data_atualizacao"],
                    properties: [
                        new OA\Property(
                            property: "nome",
                            description: "Nome do convênio médico",
                            type: "string",
                            example: "Amil Saúde"
                        ),
                        new OA\Property(
                            property: "cnpj",
                            description: "CNPJ do convênio médico",
                            type: "string",
                            example: "123456"
                        ),
                        new OA\Property(
                            property: "telefone",
                            description: "Telefone do convênio médico",
                            type: "string",
                            example: "(11) 91234-5678"
                        ),
                        new OA\Property(
                            property: "email",
                            description: "Email do convênio médico",
                            type: "string",
                            example: "contato@amil.com.br"
                        ),
                        new OA\Property(
                            property: "telefone",
                            description: "Telefone do convênio médico",
                            type: "string",
                            example: "(11) 91234-5678"
                        ),
                        new OA\Property(
                            property: "id_endereco",
                            description: "ID do endereço do convênio médico",
                            type: "string",
                            example: "1"
                        ),
                        new OA\Property(
                            property: "data_criacao",
                            description: "Data de criação do convênio médico",
                            type: "string",
                            format: "date-time",
                            example: "2023-01-01T00:00:00Z"
                        ),
                        new OA\Property(
                            property: "data_atualizacao",
                            description: "Data de atualização do convênio médico",
                            type: "string",
                            format: "date-time",
                            example: "2023-01-01T00:00:00Z"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Convênio atualizado com sucesso."
            ),
            new OA\Response(
                response: 500,
                description: "Erro interno do servidor."
            )
        ]
    )]
    public function update(Request $request, Convenio $convenio)
    {
        $data = $request->all();
        $data['cnpj'] = preg_replace('/\D/', '', $data['cnpj']);

        $convenio->update([
            'nome' => $request->nome,
            'cnpj' => preg_replace('/\D/', '', $request->cnpj),
            'telefone' => preg_replace('/\D/', '', $request->telefone),
            'email' => strtolower($request->email),
        ]);

        if($request->filled('rua') || $request->filled('numero') || $request->filled('cidade') || $request->filled('estado') || $request->filled('cep')) {
            if($convenio->id_endereco) {
                $convenio->endereco->update([
                    'logradouro' => $request->rua,
                    'numero' => $request->numero,
                    'bairro' => $request->bairro,
                    'cidade' => $request->cidade,
                    'estado' => $request->estado,
                    'cep' => preg_replace('/\D/', '', $request->cep),
                ]);
            } else {
                $endereco = Endereco::create([
                    'logradouro' => $request->rua,
                    'numero' => $request->numero,
                    'bairro' => $request->bairro,
                    'cidade' => $request->cidade,
                    'estado' => $request->estado,
                    'cep' => preg_replace('/\D/', '', $request->cep),
                ]);
                $convenio->update(['id_endereco' => $endereco->id]);
            }
        }

        return redirect()->back();
    }

    #[OA\Delete(
        path: "/api/convenios/{convenio}",
        summary: "Excluir um convênio existente",
        description: "Exclui um convênio médico existente.",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Dados necessários para criar um convênio",
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    // required: ["nome", "registro_ans"], // Campos obrigatórios
                    required: ["id_convenio"],
                    properties: [
                        new OA\Property(
                            property: "id_convenio",
                            description: "ID do convênio médico",
                            type: "string",
                            example: "1"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Convênio excluído com sucesso."
            ),
            new OA\Response(
                response: 500,
                description: "Erro interno do servidor."
            )
        ]
    )]
    public function destroy(Convenio $convenio)
    {
        $convenio->delete();
        return redirect()->back();
    }
}