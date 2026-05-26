<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlterarSenhaPrimeiroAcessoRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\EsqueciSenhaRequest;
use App\Http\Requests\RedefinirSenhaRequest;
use App\Mail\CodigoRecuperacaoMail;
use App\Models\Usuario;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class AutenticacaoController extends Controller
{
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

    public function logout(Request $request): JsonResponse
    {
        $usuario = JWTAuth::parseToken()->authenticate();
        JWTAuth::invalidate(JWTAuth::getToken());

        LogService::registrar($usuario, "Usuário {$usuario->usuario} realizou logout.");

        return response()->json(['mensagem' => 'Logout realizado com sucesso.']);
    }

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

    /**
     * @OA\Post(
     * path="/auth/esqueci-senha",
     * tags={"Autenticação"},
     * summary="Solicitar recuperação de senha",
     * description="Gera um código verificador temporário de 6 dígitos e envia para o e-mail do usuário via SMTP.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email"},
     * @OA\Property(property="email", type="string", format="email", example="joao.silva@hospital.com")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="E-mail enviado com sucesso",
     * @OA\JsonContent(@OA\Property(property="mensagem", type="string", example="Código de recuperação enviado para o e-mail informado."))
     * ),
     * @OA\Response(response=422, ref="#/components/schemas/RespostaErro")
     * )
     */
    public function esqueciSenha(EsqueciSenhaRequest $request): JsonResponse
    {
        $usuario = Usuario::where('email', $request->email)->first();

        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('recuperacao_senha')->updateOrInsert(
            ['email' => $request->email],
            [
                'codigo' => Hash::make($codigo),
                'expira_em' => now()->addMinutes(15),
                'created_at' => now()
            ]
        );

        Mail::to($usuario->email)->send(new CodigoRecuperacaoMail($codigo));

        LogService::registrar($usuario, "Usuário {$usuario->usuario} solicitou recuperação de senha.");

        return response()->json([
            'mensagem' => 'Código de recuperação enviado para o e-mail informado.'
        ]);
    }

    /**
     * @OA\Post(
     * path="/auth/redefinir-senha",
     * tags={"Autenticação"},
     * summary="Redefinir senha com código",
     * description="Valida o código de 6 dígitos enviado por e-mail e atualiza a senha do usuário.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","codigo","nova_senha","nova_senha_confirmation"},
     * @OA\Property(property="email", type="string", format="email", example="joao.silva@hospital.com"),
     * @OA\Property(property="codigo", type="string", example="123456"),
     * @OA\Property(property="nova_senha", type="string", example="NovaSenh@2026"),
     * @OA\Property(property="nova_senha_confirmation", type="string", example="NovaSenh@2026")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Senha alterada com sucesso",
     * @OA\JsonContent(@OA\Property(property="mensagem", type="string", example="Senha redefinida com sucesso."))
     * ),
     * @OA\Response(response=401, description="Código inválido ou expirado"),
     * @OA\Response(response=422, ref="#/components/schemas/RespostaErro")
     * )
     */
    public function redefinirSenha(RedefinirSenhaRequest $request): JsonResponse
    {
        $registro = DB::table('recuperacao_senha')->where('email', $request->email)->first();

        if (!$registro || now()->isAfter($registro->expira_em) || !Hash::check($request->codigo, $registro->codigo)) {
            return response()->json(['mensagem' => 'Código inválido ou expirado.'], 401);
        }

        $usuario = Usuario::where('email', $request->email)->first();
        $usuario->update([
            'senha' => Hash::make($request->nova_senha)
        ]);

        DB::table('recuperacao_senha')->where('email', $request->email)->delete();

        LogService::registrar($usuario, "Usuário {$usuario->usuario} redefiniu a senha com sucesso via código de e-mail.");

        return response()->json(['mensagem' => 'Senha redefinida com sucesso.']);
    }
}