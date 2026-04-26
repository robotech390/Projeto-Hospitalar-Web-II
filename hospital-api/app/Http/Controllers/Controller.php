<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Hospital API — Equipe 1: Autenticação e Gerenciamento de Acesso",
 *     version="1.0.0",
 *     description="API responsável pelo login via JWT, gerenciamento de usuários, médicos e agenda de plantões. Todas as outras equipes do sistema hospitalar devem se autenticar nesta API e validar tokens aqui.",
 *     @OA\Contact(
 *         email="equipe1@hospital.ifsc.edu.br",
 *         name="Equipe 1 — IFSC"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="Servidor principal"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Token JWT obtido via POST /api/auth/login. Envie no header: Authorization: Bearer {token}"
 * )
 *
 * @OA\Tag(name="Autenticação",  description="Login, logout e troca de senha")
 * @OA\Tag(name="Usuários",      description="Criação e gerenciamento de usuários do sistema")
 * @OA\Tag(name="Médicos",       description="CRUD de médicos — consumido pelo Grupo 2 (agendamento)")
 * @OA\Tag(name="Agenda",        description="Plantões e disponibilidade dos médicos")
 * @OA\Tag(name="Logs",          description="Histórico de ações — recebe logs de todos os grupos")
 *
 * @OA\Schema(
 *     schema="RespostaErro",
 *     @OA\Property(property="mensagem", type="string", example="Dados inválidos."),
 *     @OA\Property(property="erros",    type="object")
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests;
}
