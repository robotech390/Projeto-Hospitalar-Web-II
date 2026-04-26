<?php

namespace App\Repositories;

interface LabExamRepositoryInterface
{
    public function getAllExams(): array;
    public function updateResult(int $id, array $data): \App\Models\ItemExame;
}
