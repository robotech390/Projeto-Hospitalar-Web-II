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
