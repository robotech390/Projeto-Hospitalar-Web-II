<?php

namespace App\Repositories;

use App\Models\ItemExame;
use Illuminate\Support\Facades\Storage;

class LabExamEloquentRepository implements LabExamRepositoryInterface
{
    public function getAllExams(): array
    {
        // Arrays para mockar dados não presentes em ItemExame sem a tabela de pacientes/médicos completa
        $nomes = ['Maria Silva', 'João Souza', 'Carlos Lima', 'Ana Paula', 'Pedro Santos'];
        $medicos = ['Dr. João', 'Dra. Ana', 'Dr. Pedro', 'Dra. Beatriz', 'Dr. Carlos'];

        // Buscar itens reais do banco de dados, incluindo a relação com o tipo do exame
        $items = ItemExame::with('tipoExame')->latest()->get();

        $result = [];
        foreach ($items as $index => $item) {
            $pacienteNome = $nomes[$index % count($nomes)];
            $medicoNome = $medicos[$index % count($medicos)];
            
            // Gerar iniciais do nome do paciente mockado
            $words = explode(' ', $pacienteNome);
            $iniciais = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

            $result[] = [
                'id' => $item->id,
                'paciente' => $pacienteNome,
                'exame' => $item->tipoExame ? $item->tipoExame->nome : 'Exame Desconhecido',
                'tipo' => $item->tipoExame ? $item->tipoExame->tipo : 'Outro',
                'horario' => $item->created_at ? $item->created_at->format('H:i') : '00:00',
                'status' => $item->status,
                'medico' => $medicoNome,
                'dataSolicitacao' => $item->created_at ? $item->created_at->format('Y-m-d') : date('Y-m-d'),
                'iniciais' => $iniciais,
            ];
        }

        return $result;
    }

    public function updateResult(int $id, array $data): ItemExame
    {
        $item = ItemExame::findOrFail($id);

        $updateData = [
            'laudo' => $data['laudo'] ?? null,
            'status' => 'Concluído',
            'data_resultado' => now(),
        ];

        if (isset($data['arquivo']) && $data['arquivo'] instanceof \Illuminate\Http\UploadedFile) {
            // Remove o arquivo antigo se existir
            if ($item->arquivo) {
                Storage::disk('public')->delete($item->arquivo);
            }
            // Armazena no diretório 'resultados' dentro do disco 'public'
            $path = $data['arquivo']->store('resultados', 'public');
            $updateData['arquivo'] = $path;
        }

        $item->update($updateData);

        return $item;
    }
}
