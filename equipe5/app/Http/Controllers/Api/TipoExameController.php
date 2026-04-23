<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TipoExameResource;
use App\Models\TipoExame;
use Illuminate\Http\Request;

class TipoExameController extends Controller
{
    protected string $model = TipoExame::class;
    protected string $resource = TipoExameResource::class;

    /**
     * @OA\Get(
     *     path="/api/tipos-exame",
     *     summary="Listar todos os tipos de exame",
     *     tags={"TipoExame"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tipos de exame",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/TipoExame"))
     *     )
     * )
     */
    public function index()
    {
        return parent::index();
    }

    /**
     * @OA\Post(
     *     path="/api/tipos-exame",
     *     summary="Criar um novo tipo de exame",
     *     tags={"TipoExame"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TipoExame")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tipo de exame criado",
     *         @OA\JsonContent(ref="#/components/schemas/TipoExame")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $tipo = TipoExame::create($validated);

        return new TipoExameResource($tipo);
    }

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|max:255|unique:tipo_exame,nome',
            'tipo' => ['required', new \Illuminate\Validation\Rules\Enum(\App\Enums\TipoExameEnum::class)],
            'preco' => 'required|numeric|min:0',
            'preparo' => 'nullable|string',
        ];
    }
}
