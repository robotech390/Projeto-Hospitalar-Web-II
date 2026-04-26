<?php

namespace App\Repositories;

use App\Models\TipoExame;

class LabCatalogEloquentRepository implements LabCatalogRepositoryInterface
{
    public function getAllCatalog(): array
    {
        return TipoExame::all()->toArray();
    }

    public function store(array $data): TipoExame
    {
        return TipoExame::create($data);
    }

    public function update(string $id, array $data): TipoExame
    {
        $tipoExame = TipoExame::findOrFail($id);
        $tipoExame->update($data);
        return $tipoExame;
    }

    public function delete(string $id): bool
    {
        $tipoExame = TipoExame::findOrFail($id);
        return $tipoExame->delete();
    }
}
