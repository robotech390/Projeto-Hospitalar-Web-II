<?php

namespace App\Repositories;

use App\Models\ItemExame;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LabDashboardEloquentRepository implements LabDashboardRepositoryInterface
{
    public function getWeekData(): array
    {
        $diasIngles = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $diasTraducao = [
            'Sunday' => 'Dom',
            'Monday' => 'Seg',
            'Tuesday' => 'Ter',
            'Wednesday' => 'Qua',
            'Thursday' => 'Qui',
            'Friday' => 'Sex',
            'Saturday' => 'Sáb'
        ];

        // Buscar contagem de exames agrupados por dia da semana e tipo
        $counts = ItemExame::join('tipo_exame', 'itens_exame.id_tipo_exame', '=', 'tipo_exame.id')
            ->select(
                DB::raw('DAYNAME(itens_exame.data_criacao) as dia'),
                'tipo_exame.tipo',
                DB::raw('count(*) as total')
            )
            ->where('itens_exame.data_criacao', '>=', Carbon::now()->startOfWeek())
            ->groupBy('dia', 'tipo')
            ->get();

        $weekData = [];
        foreach ($diasIngles as $dia) {
            $diaTraduzido = $diasTraducao[$dia];
            $sangue = $counts->where('dia', $dia)->whereIn('tipo', ['Sangue', 'Laboratorial'])->sum('total');
            $imagem = $counts->where('dia', $dia)->whereIn('tipo', ['Imagem', 'Raio-X', 'Ressonância'])->sum('total');
            
            $weekData[] = [
                'dia' => $diaTraduzido,
                'sangue' => (int)$sangue,
                'imagem' => (int)$imagem,
            ];
        }

        return $weekData;
    }

    public function getRevenueData(): array
    {
        $mesesIngles = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $mesesTraducao = [
            'January' => 'Jan', 'February' => 'Fev', 'March' => 'Mar', 'April' => 'Abr',
            'May' => 'Mai', 'June' => 'Jun', 'July' => 'Jul', 'August' => 'Ago',
            'September' => 'Set', 'October' => 'Out', 'November' => 'Nov', 'December' => 'Dez'
        ];

        $revenues = ItemExame::join('tipo_exame', 'itens_exame.id_tipo_exame', '=', 'tipo_exame.id')
            ->select(
                DB::raw('MONTHNAME(itens_exame.data_criacao) as mes'),
                DB::raw('SUM(tipo_exame.preco) as total')
            )
            ->where('itens_exame.status', 'Concluído')
            ->whereYear('itens_exame.data_criacao', date('Y'))
            ->groupBy('mes')
            ->get();

        $revenueData = [];
        foreach ($mesesIngles as $mes) {
            $mesTraduzido = $mesesTraducao[$mes];
            $total = $revenues->where('mes', $mes)->first();
            
            $revenueData[] = [
                'mes' => $mesTraduzido,
                'valor' => $total ? (float)$total->total : 0.0,
            ];
        }

        return $revenueData;
    }

    public function getRevenueToday(): float
    {
        return (float) ItemExame::join('tipo_exame', 'itens_exame.id_tipo_exame', '=', 'tipo_exame.id')
            ->where('itens_exame.status', 'Concluído')
            ->whereDate('itens_exame.data_criacao', Carbon::today())
            ->sum('tipo_exame.preco');
    }
}
