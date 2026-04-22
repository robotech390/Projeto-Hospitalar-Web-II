<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitacaoExameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'data' => $this->data,
            'status' => $this->status,
            'prioridade' => $this->prioridade,
            'justificativa' => $this->justificativa,
            'id_consulta' => $this->id_consulta,
            'itens' => TipoExameResource::collection($this->whenLoaded('tiposExame')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
