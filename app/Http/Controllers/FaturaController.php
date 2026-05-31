<?php

namespace App\Http\Controllers;

use App\Models\Fatura;
use App\Models\ContaHospitalar;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaturaController extends Controller
{
    public function index(){
        return Inertia::render('Faturas/Index', [
            'faturas' => Fatura::with([
                'paciente',
                'contaHospitalar.itens',
                'contaHospitalar.plano.convenio',
            ])->orderBy('created_at', 'desc')
            ->get(),
        ]);
    }

    public function store(Request $request){
        $dados = $request->validate([
            'id_conta_hospitalar' => 'required|exists:contas_hospitalares,id|unique:fatura,id_conta_hospitalar',
            'data_vencimento' => 'nullable|date',
            'forma_pagamento' => 'nullable|in:dinheiro,cartao,pix,convenio,misto',
        ]);

        $conta = ContaHospitalar::findOrFail($dados['id_conta_hospitalar']);

        if($conta->status !== 'fechada'){
            return back()->withErrors(['id_conta_hospitalar' => 'A conta hospitalar deve estar fechada para gerar a fatura.']);
        }

        $numeroFatura = 'F' . date('Ymd') . str_pad(Fatura::count() + 1, 4, '0', STR_PAD_LEFT);

        Fatura::create([
            'id_conta_hospitalar' => $dados['id_conta_hospitalar'],
            'id_paciente' => $conta->id_paciente,
            'status' => 'pendente',
            'valor_total' => $conta->valor_total,
            'valor_convenio' => $conta->valor_convenio,
            'valor_paciente' => $conta->valor_paciente,
            'forma_pagamento' => $dados['forma_pagamento'] ?? null,
            'data_emissao' => now(),
            'data_vencimento' => $dados['data_vencimento'] ?? now()->addDays(30),
            'numero_fatura' => $numeroFatura,
        ]);

        $conta->update(['status' => 'faturada']);

        return redirect()->route('faturas.index')->with('success', 'Fatura gerada com sucesso.');
    }

    public function pagar(Request $request, Fatura $fatura){
        if($fatura->status !== 'pendente'){
            return redirect()->route('faturas.index')->withErrors(['status' => 'A fatura já foi paga ou está em outro status.']);
        }

        $dados = $request->validate([
            'forma_pagamento' => 'required|in:dinheiro,cartao,pix,convenio,misto',
        ]);

        $fatura->update([
            'status' => 'paga',
            'forma_pagamento' => $dados['forma_pagamento'],
            'data_pagamento' => now(),
        ]);

        return redirect()->route('faturas.index')->with('success', 'Fatura paga com sucesso.');
    }

    public function destroy(Fatura $fatura){
        if($fatura->status === 'paga'){
            return redirect()->route('faturas.index')->withErrors(['status' => 'Não é possível cancelar uma fatura paga.']);
        }

        $fatura->update(['status' => 'cancelada']);

        return redirect()->route('faturas.index')->with('success', 'Fatura cancelada. A conta foi reaberta para faturamento.');
    }

    //API PARA OUTROS GRUPOS (2 E 7)
    //GET /api/faturamento/status-pagamento/{id_consulta}

    public function statusPagamento(int $id_consulta){
        $conta = ContaHospitalar::where('id_consulta', $id_consulta)
        ->with('fatura')
        ->first();

        if(! $conta){
            return response()->json(['message' => 'Conta hospitalar não encontrada'], 404);
        }

        if(! $conta->fatura){
            return response()->json([
                'id_consulta' => $id_consulta,
                'status_conta' => $conta->status,
                'status_fatura' => null,
                'valor_paciente' => $conta->valor_paciente,
                'id_fatura' => null,
            ]);
        }

        return response()->json([
            'id_consulta' => $id_consulta,
            'status_conta' => $conta->status,
            'status_fatura' => $conta->fatura->status,
            'valor_paciente' => $conta->valor_paciente,
            'id_fatura' => $conta->fatura->id,
            'numero_fatura' => $conta->fatura->numero_fatura,
            'data_pagamento' => $conta->fatura->data_pagamento,
        ]);
    }
}