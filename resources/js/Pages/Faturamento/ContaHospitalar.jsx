import React, { useMemo, useState } from 'react';
import { Head } from '@inertiajs/react';
import FaturamentoLayout from '@/Components/Faturamento/FaturamentoLayout';

import {
    ReceiptText,
    CheckCircle,
    XCircle,
} from 'lucide-react';

export default function ContaHospitalar() {
    const [form, setForm] = useState({
        paciente: 'Maria Silva',
        convenio: 'Unimed',
        plano: 'Unimed Básico',
        cobreConsulta: true,
        cobreRemedio: false,
        cobreExame: true,
        valorConsulta: 150,
        valorRemedios: 80,
        valorExames: 200,
        statusPagamento: 'pendente',
    });

    const totais = useMemo(() => {
        const consulta = Number(form.valorConsulta) || 0;
        const remedios = Number(form.valorRemedios) || 0;
        const exames = Number(form.valorExames) || 0;

        const totalGeral = consulta + remedios + exames;

        let valorCoberto = 0;

        if (form.cobreConsulta) {
            valorCoberto += consulta;
        }

        if (form.cobreRemedio) {
            valorCoberto += remedios;
        }

        if (form.cobreExame) {
            valorCoberto += exames;
        }

        const valorPaciente = totalGeral - valorCoberto;
        const retornoLiberado = form.statusPagamento === 'pago';

        return {
            totalGeral,
            valorCoberto,
            valorPaciente,
            retornoLiberado,
        };
    }, [form]);

    function handleChange(event) {
        const { name, value, type, checked } = event.target;

        setForm((prev) => ({
            ...prev,
            [name]: type === 'checkbox' ? checked : value,
        }));
    }

    function formatarMoeda(valor) {
        return Number(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        });
    }

    return (
        <FaturamentoLayout currentPage="conta-hospitalar">
            <Head title="Conta Hospitalar" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#e1f2ef] text-[#00767F] text-xs font-semibold mb-3">
                            Conta Hospitalar
                        </div>

                        <h1 className="text-2xl font-bold text-slate-800">
                            Conta Hospitalar
                        </h1>

                        <p className="text-sm text-slate-500 mt-1">
                            Fechamento da conta, cálculo de cobertura e geração de fatura.
                        </p>
                    </div>

                    <div className="flex items-center gap-2 px-4 py-3 rounded-xl bg-white border border-slate-100 shadow-sm">
                        <ReceiptText size={20} className="text-[#007f7f]" />

                        <span className="text-sm font-semibold text-slate-700">
                            Fatura simulada
                        </span>
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <ResumoCard
                        titulo="Consulta"
                        valor={formatarMoeda(form.valorConsulta)}
                        descricao="Valor recebido do grupo de consultas"
                    />

                    <ResumoCard
                        titulo="Remédios"
                        valor={formatarMoeda(form.valorRemedios)}
                        descricao="Valor recebido do grupo de farmácia"
                    />

                    <ResumoCard
                        titulo="Exames"
                        valor={formatarMoeda(form.valorExames)}
                        descricao="Valor recebido do grupo de exames"
                    />

                    <ResumoCard
                        titulo="Paciente paga"
                        valor={formatarMoeda(totais.valorPaciente)}
                        descricao="Após aplicação da cobertura"
                    />
                </div>

                <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div className="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                        <h2 className="text-lg font-semibold text-slate-800 mb-1">
                            Dados do Atendimento
                        </h2>

                        <p className="text-sm text-slate-500 mb-6">
                            Os valores abaixo simulam os dados recebidos dos grupos de consulta, farmácia e exames.
                        </p>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label className="block text-sm font-medium text-slate-600 mb-2">
                                    Paciente
                                </label>

                                <input
                                    name="paciente"
                                    value={form.paciente}
                                    onChange={handleChange}
                                    className="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-[#007f7f] focus:ring-[#007f7f]"
                                />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-slate-600 mb-2">
                                    Convênio
                                </label>

                                <select
                                    name="convenio"
                                    value={form.convenio}
                                    onChange={handleChange}
                                    className="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-[#007f7f] focus:ring-[#007f7f]"
                                >
                                    <option>Unimed</option>
                                    <option>Bradesco Saúde</option>
                                    <option>Particular</option>
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-slate-600 mb-2">
                                    Plano
                                </label>

                                <select
                                    name="plano"
                                    value={form.plano}
                                    onChange={handleChange}
                                    className="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-[#007f7f] focus:ring-[#007f7f]"
                                >
                                    <option>Unimed Básico</option>
                                    <option>Unimed Completo</option>
                                    <option>Bradesco Empresarial</option>
                                    <option>Particular</option>
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-slate-600 mb-2">
                                    Status do Pagamento
                                </label>

                                <select
                                    name="statusPagamento"
                                    value={form.statusPagamento}
                                    onChange={handleChange}
                                    className="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-[#007f7f] focus:ring-[#007f7f]"
                                >
                                    <option value="pendente">Pendente</option>
                                    <option value="pago">Pago</option>
                                </select>
                            </div>
                        </div>

                        <div className="mt-8">
                            <h3 className="text-md font-semibold text-slate-800 mb-4">
                                Valores recebidos dos outros grupos
                            </h3>

                            <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <ValorInput
                                    label="Valor da Consulta"
                                    origem="Grupo 2"
                                    name="valorConsulta"
                                    value={form.valorConsulta}
                                    onChange={handleChange}
                                />

                                <ValorInput
                                    label="Valor dos Remédios"
                                    origem="Grupo 4"
                                    name="valorRemedios"
                                    value={form.valorRemedios}
                                    onChange={handleChange}
                                />

                                <ValorInput
                                    label="Valor dos Exames"
                                    origem="Grupo 5"
                                    name="valorExames"
                                    value={form.valorExames}
                                    onChange={handleChange}
                                />
                            </div>
                        </div>

                        <div className="mt-8">
                            <h3 className="text-md font-semibold text-slate-800 mb-4">
                                Regras de cobertura do plano
                            </h3>

                            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <CoberturaCheckbox
                                    label="Cobre consulta"
                                    name="cobreConsulta"
                                    checked={form.cobreConsulta}
                                    onChange={handleChange}
                                />

                                <CoberturaCheckbox
                                    label="Cobre remédio"
                                    name="cobreRemedio"
                                    checked={form.cobreRemedio}
                                    onChange={handleChange}
                                />

                                <CoberturaCheckbox
                                    label="Cobre exame"
                                    name="cobreExame"
                                    checked={form.cobreExame}
                                    onChange={handleChange}
                                />
                            </div>
                        </div>
                    </div>

                    <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                        <h2 className="text-lg font-semibold text-slate-800 mb-1">
                            Fatura Detalhada
                        </h2>

                        <p className="text-sm text-slate-500 mb-6">
                            Resultado do fechamento da conta hospitalar.
                        </p>

                        <div className="space-y-4">
                            <ResumoLinha
                                label="Paciente"
                                value={form.paciente}
                            />

                            <ResumoLinha
                                label="Convênio"
                                value={form.convenio}
                            />

                            <ResumoLinha
                                label="Plano"
                                value={form.plano}
                            />

                            <hr />

                            <ResumoLinha
                                label="Consulta"
                                value={formatarMoeda(form.valorConsulta)}
                                detalhe={form.cobreConsulta ? 'Coberto' : 'Paciente paga'}
                            />

                            <ResumoLinha
                                label="Remédios"
                                value={formatarMoeda(form.valorRemedios)}
                                detalhe={form.cobreRemedio ? 'Coberto' : 'Paciente paga'}
                            />

                            <ResumoLinha
                                label="Exames"
                                value={formatarMoeda(form.valorExames)}
                                detalhe={form.cobreExame ? 'Coberto' : 'Paciente paga'}
                            />

                            <hr />

                            <ResumoLinha
                                label="Total geral"
                                value={formatarMoeda(totais.totalGeral)}
                                destaque
                            />

                            <ResumoLinha
                                label="Valor coberto"
                                value={formatarMoeda(totais.valorCoberto)}
                            />

                            <ResumoLinha
                                label="Valor do paciente"
                                value={formatarMoeda(totais.valorPaciente)}
                                destaque
                            />
                        </div>

                        <div className="mt-6">
                            {form.statusPagamento === 'pago' ? (
                                <div className="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <CheckCircle size={22} />

                                    <div>
                                        <div className="font-semibold">
                                            Pagamento realizado
                                        </div>

                                        <div className="text-xs">
                                            Retorno liberado para o paciente.
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="flex items-center gap-3 p-4 rounded-xl bg-amber-50 text-amber-700 border border-amber-100">
                                    <XCircle size={22} />

                                    <div>
                                        <div className="font-semibold">
                                            Pagamento pendente
                                        </div>

                                        <div className="text-xs">
                                            Retorno bloqueado até pagamento.
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>

                        <button className="w-full mt-6 bg-[#007f7f] hover:bg-[#006b6b] text-white py-3 rounded-xl font-semibold transition">
                            Fechar Conta
                        </button>
                    </div>
                </div>
            </div>
        </FaturamentoLayout>
    );
}

function ResumoCard({ titulo, valor, descricao }) {
    return (
        <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <div className="text-sm text-slate-500">
                {titulo}
            </div>

            <div className="text-2xl font-bold text-slate-800 mt-2">
                {valor}
            </div>

            <div className="text-xs text-slate-400 mt-1">
                {descricao}
            </div>
        </div>
    );
}

function ValorInput({ label, origem, name, value, onChange }) {
    return (
        <div className="rounded-2xl border border-slate-100 bg-slate-50 p-4">
            <div className="flex items-center justify-between mb-3">
                <label className="text-sm font-medium text-slate-700">
                    {label}
                </label>

                <span className="text-xs px-2 py-1 rounded-full bg-[#e1f2ef] text-[#007f7f]">
                    {origem}
                </span>
            </div>

            <input
                type="number"
                name={name}
                value={value}
                onChange={onChange}
                className="w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm focus:border-[#007f7f] focus:ring-[#007f7f]"
            />
        </div>
    );
}

function CoberturaCheckbox({ label, name, checked, onChange }) {
    return (
        <label className="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-4 cursor-pointer">
            <input
                type="checkbox"
                name={name}
                checked={checked}
                onChange={onChange}
                className="rounded border-slate-300 text-[#007f7f] focus:ring-[#007f7f]"
            />

            <span className="text-sm font-medium text-slate-700">
                {label}
            </span>
        </label>
    );
}

function ResumoLinha({ label, value, detalhe, destaque = false }) {
    return (
        <div className="flex items-start justify-between gap-4">
            <div>
                <div className="text-sm text-slate-500">
                    {label}
                </div>

                {detalhe && (
                    <div className="text-xs text-slate-400 mt-1">
                        {detalhe}
                    </div>
                )}
            </div>

            <div
                className={`text-right ${
                    destaque
                        ? 'text-lg font-bold text-slate-900'
                        : 'text-sm font-semibold text-slate-700'
                }`}
            >
                {value}
            </div>
        </div>
    );
}