<?php

/**
 * @OA\Info(
 *     title="Hospital API — Equipe 3: Prontuário eletrônico do paciente",
 *     version="1.0.0",
 *     description="API responsável pelo gerenciamento de prontuários eletrônicos dos pacientes e interface de consulta do médico."
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="Servidor de desenvolvimento local (ajuste a URL conforme necessário para produção)"
 * )
 *
 * @OA\Tag(name="Prontuário",     description="CRUD de prontuários eletrônicos dos pacientes")
 * @OA\Tag(name="Consulta",       description="Registro de consultas, diagnósticos e evolução dos pacientes")
 * @OA\Tag(name="Receita",        description="Gerenciamento de receitas de medicamentos")
 * @OA\Tag(name="Exames",         description="Gerenciamento de pedidos de exames")
 *
 * @OA\Schema(
 *     schema="RespostaErro",
 *     @OA\Property(property="mensagem", type="string", example="Dados inválidos."),
 *     @OA\Property(property="erros",    type="object")
 * )
 */
