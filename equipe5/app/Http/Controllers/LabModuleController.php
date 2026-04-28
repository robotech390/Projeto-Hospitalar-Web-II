<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LabModuleController extends Controller
{
    public function dashboard(\App\Services\LabDashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData();
        return Inertia::render('Lab/Dashboard', $data);
    }

    public function examCatalog(\App\Services\LabCatalogService $catalogService)
    {
        $data = $catalogService->getCatalogData();
        return Inertia::render('Lab/ExamCatalog', $data);
    }

    public function storeExam(Request $request, \App\Services\LabCatalogService $catalogService)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:tipo_exame,nome',
            'tipo' => ['required', new \Illuminate\Validation\Rules\Enum(\App\Enums\TipoExameEnum::class)],
            'preco' => 'required|numeric|min:0',
            'preparo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $exame = $catalogService->createExam($validator->validated());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'exame' => $exame]);
        }

        return redirect()->back()->with('success', 'Exame cadastrado com sucesso!');
    }

    public function updateExam(Request $request, string $id, \App\Services\LabCatalogService $catalogService)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:tipo_exame,nome,' . $id,
            'tipo' => ['required', new \Illuminate\Validation\Rules\Enum(\App\Enums\TipoExameEnum::class)],
            'preco' => 'required|numeric|min:0',
            'preparo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $exame = $catalogService->updateExam($id, $validator->validated());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'exame' => $exame]);
        }

        return redirect()->back()->with('success', 'Exame atualizado com sucesso!');
    }

    public function deleteExam(Request $request, string $id, \App\Services\LabCatalogService $catalogService)
    {
        $catalogService->deleteExam($id);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Exame removido com sucesso!');
    }

    public function collectionQueue(\App\Services\LabCollectionQueueService $queueService)
    {
        $data = $queueService->getQueueData();
        return Inertia::render('Lab/CollectionQueue', $data);
    }

    public function resultEntryForm(\App\Services\LabResultEntryService $resultEntryService)
    {
        $data = $resultEntryService->getResultEntryData();
        return Inertia::render('Lab/ResultEntryForm', $data);
    }

    public function updateResultEntry(Request $request, string $id, \App\Services\LabResultEntryService $resultEntryService)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'laudo' => 'required|string',
            'arquivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $itemExame = $resultEntryService->updateResult((int)$id, $validator->validated());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'item' => $itemExame]);
        }

        return redirect()->back()->with('success', 'Resultado lançado com sucesso!');
    }

    public function examStatus(\App\Services\LabExamStatusService $examStatusService)
    {
        $data = $examStatusService->getExamStatusData();
        return Inertia::render('Lab/ExamStatus', $data);
    }

    public function examSolicitations(\App\Services\LabSolicitationService $solicitationService)
    {
        $data = $solicitationService->getSolicitationsData();
        return Inertia::render('Lab/ExamSolicitations', $data);
    }

    public function storeSolicitation(Request $request, \App\Services\LabSolicitationService $solicitationService)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'id_consulta' => 'required|integer',
            'justificativa' => 'required|string',
            'prioridade' => 'required|integer|min:1|max:3',
            'itens' => 'required|array|min:1',
            'itens.*.id_tipo_exame' => 'required|integer|exists:tipo_exame,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $solicitationService->createSolicitation($validator->validated());

        return redirect()->back()->with('success', 'Solicitação cadastrada com sucesso!');
    }

    public function updateSolicitation(Request $request, string $id, \App\Services\LabSolicitationService $solicitationService)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'id_consulta' => 'required|integer',
            'justificativa' => 'required|string',
            'prioridade' => 'required|integer|min:1|max:3',
            'itens' => 'required|array|min:1',
            'itens.*.id' => 'nullable|integer|exists:itens_exame,id',
            'itens.*.id_tipo_exame' => 'required|integer|exists:tipo_exame,id',
            'itens.*.status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $solicitationService->updateSolicitation((int)$id, $validator->validated());

        return redirect()->back()->with('success', 'Solicitação atualizada com sucesso!');
    }

    public function deleteSolicitation(string $id, \App\Services\LabSolicitationService $solicitationService)
    {
        $solicitationService->deleteSolicitation((int)$id);
        return redirect()->back()->with('success', 'Solicitação removida com sucesso!');
    }
}
