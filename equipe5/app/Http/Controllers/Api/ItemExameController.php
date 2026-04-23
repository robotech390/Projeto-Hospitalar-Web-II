<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemExameResource;
use App\Models\ItemExame;
use Illuminate\Http\Request;

class ItemExameController extends Controller
{
    protected string $model = ItemExame::class;
    protected string $resource = ItemExameResource::class;
    protected array $load = ['tipoExame'];

    /**
     * @OA\Get(
     *     path="/api/itens-exame",
     *     summary="Listar todos os itens de exame",
     *     tags={"ItemExame"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de itens de exame",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function index()
    {
        return parent::index();
    }

    /**
     * @OA\Get(
     *     path="/api/itens-exame/{id}",
     *     summary="Mostrar detalhes de um item de exame",
     *     tags={"ItemExame"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do item de exame"
     *     )
     * )
     */
    public function show($id)
    {
        return parent::show($id);
    }

    /**
     * @OA\Put(
     *     path="/api/itens-exame/{id}",
     *     summary="Atualizar um item de exame (ex: laudo, status)",
     *     tags={"ItemExame"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="laudo", type="string"),
     *             @OA\Property(property="data_resultado", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item de exame atualizado"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $item = ItemExame::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'sometimes|string',
            'laudo' => 'sometimes|nullable|string',
            'data_resultado' => 'sometimes|nullable|date',
            'arquivo' => 'sometimes|nullable|string',
        ]);

        $item->update($validated);

        return new ItemExameResource($item);
    }
}
