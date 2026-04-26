<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlterarSenhaPrimeiroAcessoRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Usuario;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Autenticação via JWT.
 *
 * Fluxo de primeiro acesso:
 *  1. Outra equipe chama POST /api/usuarios/registrar → usuário é criado com senha temporária
 *  2. Usuário chama POST /api/auth/login → recebe token com `primeiro_acesso: true` no payload
 *  3. Frontend exibe modal de troca de senha
 *  4. Usuário chama POST /api/auth/alterar-senha-primeiro-acesso → senha atualizada, `primeiro_acesso` vira false
 *  5. Fluxo normal segue com o token retornado
 */
class AutenticacaoController extends Controller
{
    // ─── Login ────────────────────────────────────────────────────────────────

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Autenticação"},
     *     summary="Realizar login",
     *     description="Autentica um usuário com e-mail e senha. Retorna um token JWT válido por 1 dia. Se `primeiro_acesso` for `true` no retorno, o frontend deve exibir o modal de troca de senha.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","senha"},
     *             @OA\Property(property="email", type="string", format="email", example="joao.silva@hospital.com"),
     *             @OA\Property(property="senha",  type="string", format="password", example="MinhaSenh@123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token",          type="string",  example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="tipo",           type="string",  example="Bearer"),
     *             @OA\Property(property="expira_em",      type="integer", example=86400, description="Segundos até expiração"),
     *             @OA\Property(property="primeiro_acesso",type="boolean", example=false, description="Se true, exibir modal de troca de senha"),
     *             @OA\Property(property="usuario", type="object",
     *                 @OA\Property(property="id",          type="integer", example=1),
     *                 @OA\Property(property="nome",        type="string",  example="João Silva"),
     *                 @OA\Property(property="email",       type="string",  example="joao.silva@hospital.com"),
     *                 @OA\Property(property="funcao",      type="string",  example="medico"),
     *                 @OA\Property(property="id_cadastro", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciais inválidas"),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->senha, $usuario->senha)) {
            return response()->json([
                'mensagem' => 'E-mail ou senha incorretos.',
            ], 401);
        }

        // Gera o token com as credenciais do usuário
        $token = JWTAuth::fromUser($usuario);

        LogService::registrar($usuario, "Usuário {$usuario->usuario} realizou login.");

        return response()->json([
            'token'           => $token,
            'tipo'            => 'Bearer',
            'expira_em'       => config('jwt.ttl') * 60, // segundos
            'primeiro_acesso' => $usuario->primeiro_acesso,
            'usuario'         => [
                'id'          => $usuario->id,
                'nome'        => $usuario->usuario,
                'email'       => $usuario->email,
                'funcao'      => $usuario->funcao,
                'id_cadastro' => $usuario->id_cadastro,
            ],
        ]);
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"Autenticação"},
     *     summary="Realizar logout",
     *     description="Invalida o token JWT atual. O token não poderá mais ser usado após esta chamada.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logout realizado com sucesso",
     *         @OA\JsonContent(@OA\Property(property="mensagem", type="string", example="Logout realizado com sucesso."))
     *     ),
     *     @OA\Response(response=401, description="Token inválido ou ausente")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $usuario = JWTAuth::parseToken()->authenticate();
        JWTAuth::invalidate(JWTAuth::getToken());

        LogService::registrar($usuario, "Usuário {$usuario->usuario} realizou logout.");

        return response()->json(['mensagem' => 'Logout realizado com sucesso.']);
    }

    // ─── Dados do usuário autenticado ─────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/auth/me",
     *     tags={"Autenticação"},
     *     summary="Dados do usuário autenticado",
     *     description="Retorna os dados do usuário dono do token JWT. Útil para as outras equipes validarem quem está logado.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id",           type="integer", example=1),
     *             @OA\Property(property="nome",         type="string",  example="João Silva"),
     *             @OA\Property(property="email",        type="string",  example="joao.silva@hospital.com"),
     *             @OA\Property(property="funcao",       type="string",  example="medico"),
     *             @OA\Property(property="id_cadastro",  type="integer", example=5),
     *             @OA\Property(property="primeiro_acesso", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Token inválido ou ausente")
     * )
     */
    public function me(): JsonResponse
    {
        $usuario = JWTAuth::parseToken()->authenticate();

        return response()->json([
            'id'              => $usuario->id,
            'nome'            => $usuario->usuario,
            'email'           => $usuario->email,
            'funcao'          => $usuario->funcao,
            'id_cadastro'     => $usuario->id_cadastro,
            'primeiro_acesso' => $usuario->primeiro_acesso,
        ]);
    }

    // ─── Troca de senha no primeiro acesso ────────────────────────────────────

    /**
     * @OA\Post(
     *     path="/auth/alterar-senha-primeiro-acesso",
     *     tags={"Autenticação"},
     *     summary="Trocar senha no primeiro acesso",
     *     description="Chamado quando `primeiro_acesso` é `true`. O usuário informa a senha temporária recebida por e-mail e define a nova senha. Não exige token JWT — apenas e-mail e senha atual.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","senha_atual","nova_senha","nova_senha_confirmation"},
     *             @OA\Property(property="email",                    type="string", format="email", example="joao.silva@hospital.com"),
     *             @OA\Property(property="senha_atual",              type="string", example="Temp@1234"),
     *             @OA\Property(property="nova_senha",               type="string", example="MinhaSenha@2026"),
     *             @OA\Property(property="nova_senha_confirmation",  type="string", example="MinhaSenha@2026")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Senha alterada com sucesso. Retorna novo token JWT.",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensagem", type="string",  example="Senha alterada com sucesso."),
     *             @OA\Property(property="token",    type="string",  example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Credenciais inválidas ou usuário não está em primeiro acesso"),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro")
     * )
     */
    public function alterarSenhaPrimeiroAcesso(AlterarSenhaPrimeiroAcessoRequest $request): JsonResponse
    {
        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->senha_atual, $usuario->senha)) {
            return response()->json(['mensagem' => 'E-mail ou senha atual incorretos.'], 401);
        }

        if (!$usuario->primeiro_acesso) {
            return response()->json([
                'mensagem' => 'Este endpoint é exclusivo para o primeiro acesso. Use /auth/alterar-senha para trocar sua senha normalmente.',
            ], 401);
        }

        $usuario->update([
            'senha'           => Hash::make($request->nova_senha),
            'primeiro_acesso' => false,
        ]);

        $token = JWTAuth::fromUser($usuario);

        LogService::registrar($usuario, "Usuário {$usuario->usuario} definiu sua senha no primeiro acesso.");

        return response()->json([
            'mensagem' => 'Senha alterada com sucesso.',
            'token'    => $token,
        ]);
    }

    // ─── Troca de senha comum (usuário já autenticado) ────────────────────────

    /**
     * @OA\Post(
     *     path="/auth/alterar-senha",
     *     tags={"Autenticação"},
     *     summary="Alterar senha",
     *     description="Permite ao usuário autenticado alterar sua senha a qualquer momento.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"senha_atual","nova_senha","nova_senha_confirmation"},
     *             @OA\Property(property="senha_atual",             type="string", example="SenhaAtual@123"),
     *             @OA\Property(property="nova_senha",              type="string", example="NovaSenha@2026"),
     *             @OA\Property(property="nova_senha_confirmation", type="string", example="NovaSenha@2026")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Senha alterada com sucesso"),
     *     @OA\Response(response=401, description="Senha atual incorreta ou token inválido"),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro")
     * )
     */
    public function alterarSenha(Request $request): JsonResponse
    {
        $request->validate([
            'senha_atual'             => ['required', 'string'],
            'nova_senha'              => ['required', 'string', 'min:8', 'confirmed'],
            'nova_senha_confirmation' => ['required'],
        ]);

        $usuario = JWTAuth::parseToken()->authenticate();

        if (!Hash::check($request->senha_atual, $usuario->senha)) {
            return response()->json(['mensagem' => 'A senha atual está incorreta.'], 401);
        }

        $usuario->update(['senha' => Hash::make($request->nova_senha)]);

        LogService::registrar($usuario, "Usuário {$usuario->usuario} alterou sua senha.");

        return response()->json(['mensagem' => 'Senha alterada com sucesso.']);
    }
}
