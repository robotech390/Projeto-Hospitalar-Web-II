<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemExameResource extends JsonResource
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
            'id_solicitacao' => $this->id_solicitacao,
            'id_tipo_exame' => $this->id_tipo_exame,
            'status' => $this->status,
            'laudo' => $this->laudo,
            'arquivo' => $this->arquivo,
            'data_resultado' => $this->data_resultado,
            'tipo_exame' => new TipoExameResource($this->whenLoaded('tipoExame')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
