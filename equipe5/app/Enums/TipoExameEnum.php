<?php

namespace App\Enums;

enum TipoExameEnum: string
{
    case SANGUE = 'Sangue';
    case RAIO_X = 'Raio-X';
    case IMAGEM = 'Imagem';
    case URINA = 'Urina';
    case OUTRO = 'Outro';

    /**
     * Retorna todos os valores permitidos.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
