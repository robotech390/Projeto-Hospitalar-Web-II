<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected string $model;
    protected string $resource;
    protected array $load = [];

    /**
     * Listagem genérica
     */
    public function index()
    {
        $query = $this->model::query();

        if (!empty($this->load)) {
            $query->with($this->load);
        }

        return $this->resource::collection($query->get());
    }
    
    /**
     * Detalhes genéricos
     */
    public function show($id)
    {
        $record = $this->model::query();

        if (!empty($this->load)) {
            $record->with($this->load);
        }

        return new $this->resource($record->findOrFail($id));
    }

    /**
     * Regras de validação que o filho deve definir
     */
    protected function rules(): array
    {
        return [];
    }
}
