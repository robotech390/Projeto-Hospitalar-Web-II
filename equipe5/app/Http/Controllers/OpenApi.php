<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "API de Laboratório e Exames - Equipe 5",
    version: "1.0.0",
    description: "Documentação interativa das APIs para controle de solicitações de exames, tipos de exames e laudos do módulo de Laboratório."
)]
#[OA\Server(
    url: "/api",
    description: "Servidor Base da API"
)]
#[OA\SecurityScheme(
    securityScheme: "sanctum",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Insira o Token JWT recebido no login da Equipe 1 no formato: Bearer {seu_token}"
)]
class OpenApi
{
    // ==========================================
    // TIPOS DE EXAME
    // ==========================================

    #[OA\Get(
        path: "/tipos-exame",
        summary: "Listar todos os tipos de exame",
        tags: ["TipoExame"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de tipos de exame retornada com sucesso.",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "nome", type: "string", example: "Hemograma Completo"),
                            new OA\Property(property: "tipo", type: "string", example: "Sangue"),
                            new OA\Property(property: "preco", type: "number", format: "float", example: 45.50),
                            new OA\Property(property: "preparo", type: "string", example: "Jejum de 8 horas")
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: "Não autorizado.")
        ]
    )]
    public function getTiposExame() {}

    #[OA\Post(
        path: "/tipos-exame",
        summary: "Criar um novo tipo de exame",
        tags: ["TipoExame"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nome", "tipo", "preco"],
                properties: [
                    new OA\Property(property: "nome", type: "string", example: "Eletrocardiograma"),
                    new OA\Property(property: "tipo", type: "string", example: "Cardio"),
                    new OA\Property(property: "preco", type: "number", format: "float", example: 120.00),
                    new OA\Property(property: "preparo", type: "string", example: "Não tomar café antes do exame")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Tipo de exame criado com sucesso.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 10),
                        new OA\Property(property: "nome", type: "string", example: "Eletrocardiograma"),
                        new OA\Property(property: "tipo", type: "string", example: "Cardio"),
                        new OA\Property(property: "preco", type: "number", format: "float", example: 120.00),
                        new OA\Property(property: "preparo", type: "string", example: "Não tomar café antes do exame")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Dados inválidos."),
            new OA\Response(response: 401, description: "Não autorizado.")
        ]
    )]
    public function postTipoExame() {}

    // ==========================================
    // SOLICITAÇÕES DE EXAME
    // ==========================================

    #[OA\Get(
        path: "/solicitacoes",
        summary: "Listar todas as solicitações de exame",
        tags: ["SolicitacaoExame"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de solicitações retornada com sucesso.",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 5),
                            new OA\Property(property: "data", type: "string", format: "date-time", example: "2026-05-24T16:00:00Z"),
                            new OA\Property(property: "justificativa", type: "string", example: "Suspeita de anemia severa"),
                            new OA\Property(property: "prioridade", type: "integer", example: 2),
                            new OA\Property(property: "id_consulta", type: "integer", example: 12),
                            new OA\Property(property: "status", type: "string", example: "Pendente")
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: "Não autorizado.")
        ]
    )]
    public function getSolicitacoes() {}

    #[OA\Get(
        path: "/solicitacoes/{id}",
        summary: "Mostrar detalhes de uma solicitação",
        tags: ["SolicitacaoExame"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID da solicitação de exame",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detalhes da solicitação retornados com sucesso.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 5),
                        new OA\Property(property: "data", type: "string", format: "date-time", example: "2026-05-24T16:00:00Z"),
                        new OA\Property(property: "justificativa", type: "string", example: "Suspeita de anemia severa"),
                        new OA\Property(property: "prioridade", type: "integer", example: 2),
                        new OA\Property(property: "id_consulta", type: "integer", example: 12),
                        new OA\Property(property: "status", type: "string", example: "Pendente"),
                        new OA\Property(
                            property: "tipos_exame",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "nome", type: "string", example: "Hemograma Completo")
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Solicitação não encontrada."),
            new OA\Response(response: 401, description: "Não autorizado.")
        ]
    )]
    public function getSolicitacaoById() {}

    #[OA\Post(
        path: "/solicitacoes",
        summary: "Criar uma nova solicitação de exame",
        tags: ["SolicitacaoExame"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["data", "justificativa", "prioridade", "id_consulta", "tipos_exame"],
                properties: [
                    new OA\Property(property: "data", type: "string", format: "date-time", example: "2026-05-24T16:00:00Z"),
                    new OA\Property(property: "justificativa", type: "string", example: "Avaliação pré-operatória"),
                    new OA\Property(property: "prioridade", type: "integer", example: 1),
                    new OA\Property(property: "id_consulta", type: "integer", example: 15),
                    new OA\Property(
                        property: "tipos_exame",
                        type: "array",
                        items: new OA\Items(type: "integer"),
                        example: [1, 2]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Solicitação criada com sucesso.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 6),
                        new OA\Property(property: "data", type: "string", format: "date-time", example: "2026-05-24T16:00:00Z"),
                        new OA\Property(property: "justificativa", type: "string", example: "Avaliação pré-operatória"),
                        new OA\Property(property: "prioridade", type: "integer", example: 1),
                        new OA\Property(property: "id_consulta", type: "integer", example: 15),
                        new OA\Property(property: "status", type: "string", example: "Pendente")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Dados inválidos."),
            new OA\Response(response: 401, description: "Não autorizado.")
        ]
    )]
    public function postSolicitacao() {}

    // ==========================================
    // ITENS DE EXAME
    // ==========================================

    #[OA\Get(
        path: "/itens-exame",
        summary: "Listar todos os itens de exame",
        tags: ["ItemExame"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de itens de exame retornada com sucesso.",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 20),
                            new OA\Property(property: "id_solicitacao", type: "integer", example: 5),
                            new OA\Property(property: "id_tipo_exame", type: "integer", example: 1),
                            new OA\Property(property: "status", type: "string", example: "Pendente"),
                            new OA\Property(property: "laudo", type: "string", nullable: true, example: null),
                            new OA\Property(property: "data_resultado", type: "string", format: "date-time", nullable: true, example: null)
                        ]
                    )
                )
            ),
            new OA\Response(response: 401, description: "Não autorizado.")
        ]
    )]
    public function getItensExame() {}

    #[OA\Get(
        path: "/itens-exame/{id}",
        summary: "Mostrar detalhes de um item de exame",
        tags: ["ItemExame"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID do item de exame",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detalhes do item de exame retornados com sucesso.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 20),
                        new OA\Property(property: "id_solicitacao", type: "integer", example: 5),
                        new OA\Property(property: "id_tipo_exame", type: "integer", example: 1),
                        new OA\Property(property: "status", type: "string", example: "Coletado"),
                        new OA\Property(property: "laudo", type: "string", nullable: true, example: "Níveis normais de glicose."),
                        new OA\Property(property: "data_resultado", type: "string", format: "date-time", nullable: true, example: "2026-05-24T17:00:00Z")
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Item de exame não encontrado."),
            new OA\Response(response: 401, description: "Não autorizado.")
        ]
    )]
    public function getItemExameById() {}

    #[OA\Put(
        path: "/itens-exame/{id}",
        summary: "Atualizar um item de exame (ex: laudo, status)",
        tags: ["ItemExame"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID do item de exame",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "status", type: "string", example: "Concluído"),
                    new OA\Property(property: "laudo", type: "string", example: "Hemoglobina: 14 g/dL. Quadro dentro da normalidade."),
                    new OA\Property(property: "data_resultado", type: "string", format: "date", example: "2026-05-24"),
                    new OA\Property(property: "arquivo", type: "string", format: "binary", description: "Upload do laudo assinado em PDF")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Item de exame atualizado com sucesso.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 20),
                        new OA\Property(property: "status", type: "string", example: "Concluído"),
                        new OA\Property(property: "laudo", type: "string", example: "Hemoglobina: 14 g/dL. Quadro dentro da normalidade."),
                        new OA\Property(property: "data_resultado", type: "string", format: "date-time", example: "2026-05-24T17:15:00Z")
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Dados inválidos."),
            new OA\Response(response: 404, description: "Item de exame não encontrado."),
            new OA\Response(response: 401, description: "Não autorizado.")
        ]
    )]
    public function putItemExame() {}
}
