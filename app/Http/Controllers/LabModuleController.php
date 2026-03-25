<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LabModuleController extends Controller
{
    public function examCatalog()
    {
        return Inertia::render('Lab/ExamCatalog');
    }

    public function collectionQueue()
    {
        return Inertia::render('Lab/CollectionQueue');
    }

    public function resultEntryForm()
    {
        return Inertia::render('Lab/ResultEntryForm');
    }

    public function examStatus()
    {
        return Inertia::render('Lab/ExamStatus');
    }
}
