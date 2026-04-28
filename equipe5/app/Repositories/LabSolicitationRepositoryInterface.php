<?php

namespace App\Repositories;

use App\Models\SolicitacaoExame;

interface LabSolicitationRepositoryInterface
{
    public function getAll(): array;
    public function create(array $data): SolicitacaoExame;
    public function update(int $id, array $data): SolicitacaoExame;
    public function delete(int $id): bool;
    public function getById(int $id): SolicitacaoExame;
}
