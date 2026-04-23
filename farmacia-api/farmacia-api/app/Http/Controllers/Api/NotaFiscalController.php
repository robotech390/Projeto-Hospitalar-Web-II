<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotaFiscal;
use App\Models\Lote;
use App\Models\LoteNotaFiscal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaFiscalController extends Controller
{
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            // 1. Cria a Nota
            $nf = NotaFiscal::create([
                'numero' => $request->numero,
                'cpf_cnpj' => $request->cpf_cnpj,
                'destinatario' => $request->destinatario ?? 1,
                'data' => $request->data,
                'tipo' => $request->tipo // 'E' ou 'S'
            ]);

            foreach ($request->itens as $item) {
                $idLote = null;
            
                if ($request->tipo === 'E') {
                    // Busca se já existe este lote para este medicamento
                    $loteExistente = Lote::where('id_medicamento', $item['id_medicamento'])
                                        ->where('numero', $item['numero_lote'])
                                        ->first();
            
                    if ($loteExistente) {
                        // INCREMENTA: Se existe, soma a quantidade e garante que está ativo
                        $loteExistente->quantidade_produtos += $item['quantidade'];
                        $loteExistente->ativo = 1;
                        $loteExistente->save();
                        $idLote = $loteExistente->id;
                    } else {
                        // CRIA: Se não existe, gera um novo registro
                        $novoLote = Lote::create([
                            'id_medicamento' => $item['id_medicamento'],
                            'numero' => $item['numero_lote'],
                            'data_validade' => $item['data_validade'],
                            'quantidade_produtos' => $item['quantidade'],
                            'ativo' => 1
                        ]);
                        $idLote = $novoLote->id;
                    }
                } else {
                    // SAÍDA: Lógica de subtração (já implementada anteriormente)
                    $lote = Lote::find($item['id_lote']);
                    if ($lote) {
                        $lote->quantidade_produtos -= $item['quantidade'];
                        if ($lote->quantidade_produtos <= 0) $lote->ativo = 0;
                        $lote->save();
                        $idLote = $lote->id;
                    }
                }
            
                // Vincula à Nota Fiscal
                LoteNotaFiscal::create([
                    'id_nota_fiscal' => $nf->id,
                    'id_lote' => $idLote,
                    'icms' => $item['icms'] ?? '0',
                    'cfop' => $item['cfop'] ?? '5102',
                    'quantidade' => $item['quantidade']
                ]);
            }

            return response()->json(['mensagem' => 'Nota Fiscal e Estoque processados com sucesso!']);
        });
    }
}