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
