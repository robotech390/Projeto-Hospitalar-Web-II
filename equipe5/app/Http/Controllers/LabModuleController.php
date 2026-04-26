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

    public function examStatus(\App\Services\LabExamStatusService $examStatusService)
    {
        $data = $examStatusService->getExamStatusData();
        return Inertia::render('Lab/ExamStatus', $data);
    }
}
