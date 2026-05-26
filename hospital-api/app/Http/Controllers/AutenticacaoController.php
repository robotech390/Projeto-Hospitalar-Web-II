<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlterarSenhaPrimeiroAcessoRequest;
use App\Http\Requests\EsqueciSenhaRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RedefinirSenhaRequest;
use App\Models\TokenRedefinicaoSenha;
use App\Models\Usuario;
use App\Services\LogService;
use App\Services\SenhaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Autenticação via JWT.
 *
 * Fluxos cobertos:
 *  - Login e logout
 *  - Primeiro acesso (troca de senha temporária)
 *  - Esqueci minha senha (envio de e-mail com link de redefinição)
 *  - Alteração de senha pelo próprio usuário autenticado
 */
class AutenticacaoController extends Controller
{
    // ─── Login ────────────────────────────────────────────────────────────────

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Autenticação"},
     *     summary="Realizar login",
     *     description="Autentica um usuário com e-mail e senha. Retorna um token JWT válido por 1 dia. Se primeiro_acesso for true, o frontend deve redirecionar para a tela de troca de senha.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","senha"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@hospital.com"),
     *             @OA\Property(property="senha", type="string", format="password", example="Admin@123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="token",           type="string",  example="eyJ0eXAiOiJKV1Qi..."),
     *             @OA\Property(property="tipo",            type="string",  example="Bearer"),
     *             @OA\Property(property="expira_em",       type="integer", example=86400),
     *             @OA\Property(property="primeiro_acesso", type="boolean", example=false),
     *             @OA\Property(property="usuario", type="object",
     *                 @OA\Property(property="id",          type="integer", example=1),
     *                 @OA\Property(property="nome",        type="string",  example="Administrador"),
     *                 @OA\Property(property="email",       type="string",  example="admin@hospital.com"),
     *                 @OA\Property(property="funcao",      type="string",  example="administrador"),
     *                 @OA\Property(property="id_cadastro", type="integer", example=1)
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

        $token = JWTAuth::fromUser($usuario);

        LogService::registrar($usuario, "Usuário {$usuario->usuario} realizou login.");

        return response()->json([
            'token'           => $token,
            'tipo'            => 'Bearer',
            'expira_em'       => config('jwt.ttl') * 60,
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
     *     description="Invalida o token JWT atual.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logout realizado com sucesso"),
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
     *     @OA\Response(response=200, description="Dados do usuário"),
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
     *     description="Chamado quando o usuário acabou de se logar com a senha temporária recebida por e-mail. Não exige token JWT.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","senha_atual","nova_senha","nova_senha_confirmation"},
     *             @OA\Property(property="email",                   type="string", format="email", example="joao@hospital.com"),
     *             @OA\Property(property="senha_atual",             type="string", example="Temp@1234"),
     *             @OA\Property(property="nova_senha",              type="string", example="MinhaSenha2026"),
     *             @OA\Property(property="nova_senha_confirmation", type="string", example="MinhaSenha2026")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Senha alterada com sucesso. Retorna novo token JWT."),
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
                'mensagem' => 'Este endpoint é exclusivo para o primeiro acesso. Utilize /auth/alterar-senha.',
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

    // ─── Troca de senha pelo usuário autenticado ──────────────────────────────

    /**
     * @OA\Post(
     *     path="/auth/alterar-senha",
     *     tags={"Autenticação"},
     *     summary="Alterar senha (usuário autenticado)",
     *     description="Permite ao usuário autenticado alterar a própria senha. Requer a senha atual.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"senha_atual","nova_senha","nova_senha_confirmation"},
     *             @OA\Property(property="senha_atual",             type="string", example="SenhaAtual123"),
     *             @OA\Property(property="nova_senha",              type="string", example="NovaSenha2026"),
     *             @OA\Property(property="nova_senha_confirmation", type="string", example="NovaSenha2026")
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
            'nova_senha'              => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/',
            ],
            'nova_senha_confirmation' => ['required'],
        ], [
            'senha_atual.required'    => 'Informe a senha atual.',
            'nova_senha.required'     => 'Informe a nova senha.',
            'nova_senha.min'          => 'A nova senha deve ter no mínimo 8 caracteres.',
            'nova_senha.confirmed'    => 'As senhas não conferem.',
            'nova_senha.regex'        => 'A senha deve conter ao menos uma letra e um número.',
        ]);

        $usuario = JWTAuth::parseToken()->authenticate();

        if (!Hash::check($request->senha_atual, $usuario->senha)) {
            return response()->json(['mensagem' => 'A senha atual está incorreta.'], 401);
        }

        $usuario->update(['senha' => Hash::make($request->nova_senha)]);

        LogService::registrar($usuario, "Usuário {$usuario->usuario} alterou sua senha.");

        return response()->json(['mensagem' => 'Senha alterada com sucesso.']);
    }

    // ─── Esqueci minha senha ──────────────────────────────────────────────────

    /**
     * @OA\Post(
     *     path="/auth/esqueci-senha",
     *     tags={"Autenticação"},
     *     summary="Solicitar redefinição de senha",
     *     description="Envia um e-mail com link para redefinir a senha. Por segurança, sempre retorna sucesso mesmo que o e-mail não exista no sistema.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="joao@hospital.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Solicitação processada",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensagem", type="string",
     *                 example="Se o e-mail informado estiver cadastrado, você receberá um link para redefinir sua senha.")
     *         )
     *     ),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro")
     * )
     */
    public function esqueciSenha(EsqueciSenhaRequest $request): JsonResponse
    {
        $usuario = Usuario::where('email', $request->email)->first();

        if ($usuario) {
            SenhaService::gerarEnviarTokenRedefinicao($usuario->email, $usuario->usuario);
            LogService::registrar($usuario, "Usuário {$usuario->usuario} solicitou redefinição de senha.");
        }

        // Resposta genérica por segurança (evita enumerar e-mails existentes)
        return response()->json([
            'mensagem' => 'Se o e-mail informado estiver cadastrado, você receberá um link para redefinir sua senha.',
        ]);
    }

    // ─── Redefinir senha via token ────────────────────────────────────────────

    /**
     * @OA\Post(
     *     path="/auth/redefinir-senha",
     *     tags={"Autenticação"},
     *     summary="Redefinir senha com token recebido por e-mail",
     *     description="Recebe o token enviado por e-mail e a nova senha. O token expira em 60 minutos.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","token","nova_senha","nova_senha_confirmation"},
     *             @OA\Property(property="email",                   type="string", format="email", example="joao@hospital.com"),
     *             @OA\Property(property="token",                   type="string", example="cE5...64-caracteres..."),
     *             @OA\Property(property="nova_senha",              type="string", example="NovaSenha2026"),
     *             @OA\Property(property="nova_senha_confirmation", type="string", example="NovaSenha2026")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Senha redefinida com sucesso"),
     *     @OA\Response(response=400, description="Token inválido, expirado ou já utilizado"),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro")
     * )
     */
    public function redefinirSenha(RedefinirSenhaRequest $request): JsonResponse
    {
        $tokenRegistro = TokenRedefinicaoSenha::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenRegistro || !$tokenRegistro->ehValido()) {
            return response()->json([
                'mensagem' => 'Token inválido, expirado ou já utilizado. Solicite uma nova redefinição.',
            ], 400);
        }

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario) {
            return response()->json(['mensagem' => 'Usuário não encontrado.'], 404);
        }

        $usuario->update([
            'senha'           => Hash::make($request->nova_senha),
            'primeiro_acesso' => false,
        ]);

        $tokenRegistro->update(['utilizado_em' => now()]);

        LogService::registrar($usuario, "Usuário {$usuario->usuario} redefiniu a senha via e-mail.");

        return response()->json([
            'mensagem' => 'Senha redefinida com sucesso. Você já pode entrar com a nova senha.',
        ]);
    }
}
