<?php

namespace App\Repositories;

class LabCatalogMockRepository implements LabCatalogRepositoryInterface
{
    public function getAllCatalog(): array
    {
        return [
            [
                'id' => '1',
                'nome' => 'Hemograma',
                'tipo' => 'Sangue',
                'preco' => 35.5,
                'preparo' => 'Jejum de 8 horas',
            ],
            [
                'id' => '2',
                'nome' => 'Raio-X Tórax',
                'tipo' => 'Raio-X',
                'preco' => 120,
                'preparo' => 'Remover objetos metálicos',
            ],
        ];
    }
}
