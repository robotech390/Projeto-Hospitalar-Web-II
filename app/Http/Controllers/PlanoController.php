<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Plano;
use OpenApi\Attributes as OA;

class PlanoController extends Controller{
    #[OA\Get(
        path: "/api/planos",
        summary: "Listar todos os planos",
        description: "Retorna uma lista de todos os planos médicos cadastrados.",
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de planos retornada com sucesso."
            ),
            new OA\Response(
                response: 500,
                description: "Erro interno do servidor."
            )
        ]
    )]
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

    #[OA\Post(
        path: "/convenios",
        summary: "Criar um novo convênio",
        description: "Cria um novo convênio médico.",
        parameters: [
            new OA\Parameter(
                name: "convenio",
                in: "path", // Significa que vai substituir o {convenio} no link
                description: "ID do convênio que será atualizado",
                required: true,
                // schema: new OA\Schema(type: "integer", example: 1)
                schema: new OA\Schema(ref: "#/components/schemas/Convenio")
            )
        ],
        /*requestBody: new OA\RequestBody(
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
        ),*/
        requestBody: new OA\RequestBody(
            required: true,
            description: "Objeto Convênio a ser transportado",
            content: new OA\MediaType(
                mediaType: "application/json",
                // Aqui dizemos que o corpo da requisição REQUER o Schema 'Convenio'
                schema: new OA\Schema(ref: "#/components/schemas/Convenio")
            )
        ),
        responses: [
            /*new OA\Response(
                response: 200,
                description: "Convênio criado com sucesso."
            ),
            new OA\Response(
                response: 500,
                description: "Erro interno do servidor."
            ),
        responses: [*/
        new OA\Response(
            response: 201,
            description: "Convênio criado com sucesso!",
            content: new OA\JsonContent(ref: "#/components/schemas/Convenio") // Opcional: retorna o objeto criado
        ),
        new OA\Response(response: 422, description: "Erro de validação.")
        ]
    )]
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