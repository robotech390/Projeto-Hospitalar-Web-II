<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarUsuarioRequest;
use App\Models\Pessoa;
use App\Models\Usuario;
use App\Services\LogService;
use App\Services\SenhaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsuarioController extends Controller
{
    /**
     * @OA\Post(
     *     path="/usuarios/registrar",
     *     tags={"Usuários"},
     *     summary="Registrar novo usuário",
     *     description="Cria um usuário no sistema. Gera senha aleatória e envia por e-mail. Para administradores o campo id_cadastro é opcional.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(required={"email","funcao","nome"},
     *             @OA\Property(property="email",       type="string", format="email", example="maria@email.com"),
     *             @OA\Property(property="funcao",      type="string", example="paciente"),
     *             @OA\Property(property="id_cadastro", type="integer", example=42),
     *             @OA\Property(property="nome",        type="string", example="Maria Santos")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Usuário criado. Senha enviada por e-mail."),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro"),
     *     @OA\Response(response=401, description="Token inválido")
     * )
     */
    public function registrar(RegistrarUsuarioRequest $request): JsonResponse
    {
        $senhaPrimeiroAcesso = SenhaService::gerarSenhaPrimeiroAcesso();

        $usuario = Usuario::create([
            'usuario'         => $request->nome,
            'email'           => $request->email,
            'senha'           => Hash::make($senhaPrimeiroAcesso),
            'funcao'          => $request->funcao,
            'id_cadastro'     => $request->id_cadastro,
            'primeiro_acesso' => true,
        ]);

        SenhaService::enviarSenhaPrimeiroAcesso(
            email:  $request->email,
            nome:   $request->nome,
            funcao: $request->funcao,
            senha:  $senhaPrimeiroAcesso,
        );

        $logado = JWTAuth::parseToken()->authenticate();
        LogService::registrar(
            $logado,
            "Usuário {$logado->usuario} registrou o novo usuário '{$request->nome}' ({$request->funcao}) com e-mail {$request->email}."
        );

        return response()->json([
            'mensagem'    => 'Usuário criado com sucesso. A senha de primeiro acesso foi enviada por e-mail.',
            'id_usuario'  => $usuario->id,
            'email'       => $usuario->email,
            'funcao'      => $usuario->funcao,
            'id_cadastro' => $usuario->id_cadastro,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/usuarios/{id}/reenviar-senha",
     *     tags={"Usuários"},
     *     summary="Reenviar senha de primeiro acesso",
     *     description="Gera uma nova senha temporária para o usuário e a envia por e-mail. Marca o usuário como primeiro_acesso = true, forçando a troca no próximo login.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Nova senha enviada por e-mail"),
     *     @OA\Response(response=404, description="Usuário não encontrado")
     * )
     */
    public function reenviarSenha(int $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);

        SenhaService::reenviarSenhaPrimeiroAcesso($usuario);

        $logado = JWTAuth::parseToken()->authenticate();
        LogService::registrar(
            $logado,
            "Usuário {$logado->usuario} reenviou a senha de primeiro acesso para '{$usuario->usuario}' ({$usuario->email})."
        );

        return response()->json([
            'mensagem' => "Nova senha temporária enviada para {$usuario->email}.",
        ]);
    }

    /**
     * @OA\Get(
     *     path="/usuarios",
     *     tags={"Usuários"},
     *     summary="Listar usuários",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="funcao", in="query", @OA\Schema(type="string", example="medico")),
     *     @OA\Response(response=200, description="Lista de usuários"),
     *     @OA\Response(response=401, description="Token inválido")
     * )
     */
    public function index(): JsonResponse
    {
        $usuarios = Usuario::when(request('funcao'), fn($q) => $q->where('funcao', request('funcao')))
            ->select('id', 'usuario', 'email', 'funcao', 'id_pessoa', 'id_cadastro', 'primeiro_acesso', 'data_criacao', 'data_alteracao')
            ->orderBy('usuario')
            ->get();

        return response()->json($usuarios);
    }

    /**
     * @OA\Get(
     *     path="/usuarios/{id}",
     *     tags={"Usuários"},
     *     summary="Buscar usuário por ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Dados do usuário"),
     *     @OA\Response(response=404, description="Não encontrado")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $usuario = Usuario::select('id', 'usuario', 'email', 'funcao', 'id_pessoa', 'id_cadastro', 'primeiro_acesso', 'data_criacao', 'data_alteracao')
            ->findOrFail($id);

        return response()->json($usuario);
    }

    /**
     * @OA\Put(
     *     path="/usuarios/{id}",
     *     tags={"Usuários"},
     *     summary="Atualizar usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="nome",   type="string", example="João da Silva"),
     *         @OA\Property(property="email",  type="string", format="email"),
     *         @OA\Property(property="funcao", type="string", example="recepcionista")
     *     )),
     *     @OA\Response(response=200, description="Usuário atualizado"),
     *     @OA\Response(response=404, description="Não encontrado"),
     *     @OA\Response(response=422, ref="#/components/schemas/RespostaErro")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);

        $dados = $request->validate([
            'nome'   => ['sometimes', 'string', 'min:3', 'max:100'],
            'email'  => ['sometimes', 'email', 'max:345', Rule::unique('usuario', 'email')->ignore($usuario->id)],
            'funcao' => ['sometimes', 'string', 'in:administrador,medico,farmaceutico,recepcionista,paciente'],
        ], [
            'nome.min'     => 'O nome deve ter ao menos 3 caracteres.',
            'email.email'  => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso por outro usuário.',
            'funcao.in'    => 'Função inválida.',
        ]);

        if (isset($dados['nome'])) {
            $dados['usuario'] = $dados['nome'];
            unset($dados['nome']);
        }

        $usuario->update($dados);

        if ($usuario->id_pessoa) {
            $pessoa = Pessoa::find($usuario->id_pessoa);
            if ($pessoa) {
                $pessoa->update(array_filter([
                    'nome'  => $dados['usuario'] ?? null,
                    'email' => $dados['email']   ?? null,
                ], fn($v) => !is_null($v)));
            }
        }

        $logado = JWTAuth::parseToken()->authenticate();
        LogService::registrar($logado, "Usuário {$logado->usuario} atualizou o usuário ID {$id}.");

        return response()->json([
            'mensagem' => 'Usuário atualizado com sucesso.',
            'usuario'  => $usuario->fresh(),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/usuarios/{id}",
     *     tags={"Usuários"},
     *     summary="Remover usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Usuário removido"),
     *     @OA\Response(response=404, description="Não encontrado")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $usuario = Usuario::findOrFail($id);
        $nomeRemovido = $usuario->usuario;
        $usuario->delete();

        $logado = JWTAuth::parseToken()->authenticate();
        LogService::registrar($logado, "Usuário {$logado->usuario} removeu o usuário '{$nomeRemovido}' (ID {$id}).");

        return response()->json(['mensagem' => 'Usuário removido com sucesso.']);
    }
}
