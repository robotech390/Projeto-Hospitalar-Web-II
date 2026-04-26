<?php

namespace App\Repositories;

interface LabCatalogRepositoryInterface
{
    public function getAllCatalog(): array;
    public function store(array $data): \App\Models\TipoExame;
    public function update(string $id, array $data): \App\Models\TipoExame;
    public function delete(string $id): bool;
}
