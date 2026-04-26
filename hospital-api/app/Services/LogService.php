<?php

namespace App\Services;

use App\Models\HistoricoLog;
use App\Models\Usuario;

/**
 * LogService — registro centralizado de ações no sistema.
 * Uso: LogService::registrar($usuario, 'Descrição da ação');
 */
class LogService
{
    public static function registrar(Usuario|int $usuario, string $descricao): void
    {
        $idUsuario = $usuario instanceof Usuario ? $usuario->id : $usuario;

        HistoricoLog::create([
            'id_usuario' => $idUsuario,
            'log'        => $descricao,
            'data'       => now(),
        ]);
    }
}
