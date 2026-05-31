<?php

namespace App\Http\Controllers;

use App\Models\ContaHospitalar;
use App\Models\Consulta;
use App\Models\MedicamentoReceita;
use App\Models\SolicitacaoExame;
use App\Models\Plano;
use Illuminate\Http\Request;
use Inertia\Inertia;
class ContaHospitalarController extends Controller
{
    public function index()
    {
        return Inertia::render('Faturamento/ContaHospitalar', [
            'contas' => ContaHospitalar::with([
                'consulta',
                'paciente',
                'plano.convenio',
                'itens',
                'fatura',
            ])
            ->orderBy('created_at', 'desc')
            ->get(),

            'planos' => Plano::with('convenio')
            ->orderBy('descricao')
            ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'id_consulta' => 'required|exists:consultas,id',
            'id_plano' => 'required|exists:planos,id',
        ]);

        $consulta = Consulta::findOrFail($dados['id_consulta']);

        ContaHospitalar::create([
            'id_consulta' => $dados['id_consulta'],
            'id_paciente' => $consulta->id_paciente,
            'id_plano' => $dados['id_plano'],
            'status' => 'aberta',
            'data_abertura' => now(),
        ]);

        return redirect()->route('conta-hospitalar.index')
            ->with('success', 'Conta hospitalar criada com sucesso.');
    }

    public function fechar(ContaHospitalar $conta)
    {
        if ($conta->status !== 'aberta') {
            return redirect()->route('conta-hospitalar.index')
                ->with('error', 'A conta hospitalar já está fechada.');
        }

        $plano = $conta->id_plano ? Plano::find($conta->id_plano) : null;

        foreach ($conta->itens as $item) {
            $coberto = false;
            if($plano) {
                $coberto = match($item->origem) {
                    'consulta' => (function() use ($plano, $item) {
                        $consulta = Consulta::find($item->id_origem);

                        return $consulta ? $plano->cobre('consulta', $consulta->id_tipo_consulta) : false;
                    })(),

                    'medicamento' => (function() use ($plano, $item) {
                        $medicamentoReceita = MedicamentoReceita::with('medicamento')
                            ->find($item->id_origem);

                            return $medicamentoReceita && $medicamentoReceita->medicamento
                                ? $plano->cobre('medicamento', $medicamentoReceita->medicamento->id_tipo_medicamento) : false;
                    })(),

                    //CONFERIR COMO O GRUPO 5 FEZ OS EXAMES
                    'exame' => (function() use ($plano, $item) {
                        $solicitacao = SolicitacaoExame::find($item->id_origem);

                        return $solicitacao && isset($solicitacao->id_tipo_exame)
                            ? $plano->cobre('exame', $solicitacao->id_tipo_exame) : false;
                    })(),

                    default => false,
                };
            }

            $item->update(['coberto_convenio' => $coberto]);
        }

        $conta->load('itens');

        //AJUSTAR CÁLCULOS COM BASE NA COBERTURA DO PLANO
        $valorTotal = $conta->itens->sum('valor');
        $valorConvênio = $conta->itens->where('coberto_convenio', true)->sum('valor');
        $valorPaciente = $valorTotal - $valorConvênio;

        $conta->update([
            'status' => 'fechada',
            'valor_total' => $valorTotal,
            'valor_convenio' => $valorConvênio,
            'valor_paciente' => $valorPaciente,
            'data_fechamento' => now(),
        ]);

        return redirect()->route('conta-hospitalar.index')
            ->with('success', 'Conta hospitalar fechada com sucesso.');
    }

    public function addItem(Request $request, ContaHospitalar $conta)
    {
        if ($conta->status !== 'aberta') {
            return redirect()->route('conta-hospitalar.index')
                ->with('error', 'Não é possível adicionar itens a uma conta hospitalar fechada.');
        }

        $dados = $request->validate([
            'origem' => 'required|in:consulta,medicamento,exame',
            'id_origem' => 'required|integer',
            'descricao' => 'required|string|max:255',
            'quantidade' => 'required|integer|min:1',
            'valor_unitario' => 'required|numeric|min:0',
        ]);

        $conta->itens()->create([
            'origem' => $dados['origem'],
            'id_origem' => $dados['id_origem'],
            'descricao' => $dados['descricao'],
            'quantidade' => $dados['quantidade'],
            'valor_unitario' => $dados['valor_unitario'],
            'valor_total' => $dados['quantidade'] * $dados['valor_unitario'],
            'coberto_convenio' => false,
        ]);

        return redirect()->route('conta-hospitalar.index')
            ->with('success', 'Item adicionado à conta hospitalar com sucesso.');
    }

    public function destroy(ContaHospitalar $conta)
    {
        if ($conta->status !== 'aberta') {
            return redirect()->route('conta-hospitalar.index')
                ->with('error', 'Não é possível excluir uma conta hospitalar fechada.');
        }

        $conta->delete();

        return redirect()->route('conta-hospitalar.index')
            ->with('success', 'Conta hospitalar excluída com sucesso.');
    }
}