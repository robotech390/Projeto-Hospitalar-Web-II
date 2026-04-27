<?php

namespace App\Repositories;

use App\Models\ItemExame;
use App\Services\ConsultationService;
use Illuminate\Support\Facades\Storage;

class LabExamEloquentRepository implements LabExamRepositoryInterface
{
    protected $consultationService;

    public function __construct(ConsultationService $consultationService)
    {
        $this->consultationService = $consultationService;
    }

    public function getAllExams(): array
    {
        // Buscar itens reais do banco de dados, incluindo a relação com o tipo do exame e a solicitação
        $items = ItemExame::with(['tipoExame', 'solicitacaoExame'])->latest('data_criacao')->get();

        $result = [];
        foreach ($items as $item) {
            $pacienteNome = 'Desconhecido';
            $medicoNome = 'Desconhecido';
            
            // Buscar dados de consulta (paciente/médico) via serviço mockado
            if ($item->solicitacaoExame && $item->solicitacaoExame->id_consulta) {
                $consultation = $this->consultationService->getConsultationData($item->solicitacaoExame->id_consulta);
                if ($consultation) {
                    $pacienteNome = $consultation['paciente']['nome'] ?? 'Desconhecido';
                    $medicoNome = $consultation['medico']['nome'] ?? 'Desconhecido';
                }
            }
            
            // Gerar iniciais do nome do paciente
            $words = explode(' ', $pacienteNome);
            $iniciais = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

            $result[] = [
                'id' => $item->id,
                'paciente' => $pacienteNome,
                'exame' => $item->tipoExame ? $item->tipoExame->nome : 'Exame Desconhecido',
                'tipo' => $item->tipoExame ? $item->tipoExame->tipo : 'Outro',
                'horario' => $item->data_criacao ? $item->data_criacao->format('H:i') : '00:00',
                'status' => $item->status,
                'medico' => $medicoNome,
                'dataSolicitacao' => $item->data_criacao ? $item->data_criacao->format('Y-m-d') : date('Y-m-d'),
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
