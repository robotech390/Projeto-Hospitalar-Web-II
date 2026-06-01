import React from 'react';
import { Link } from '@inertiajs/react';

import {
    BarChart,
    Bar,
    XAxis,
    YAxis,
    Tooltip,
    ResponsiveContainer,
    LineChart,
    Line,
    CartesianGrid,
} from 'recharts';

import {
    Search,
    Bell,
    User,
    Users,
    CalendarCheck,
    ClipboardCheck,
    DollarSign,
    LayoutDashboard,
    FileText,
    CreditCard,
    Building2,
    Stethoscope,
    FlaskConical,
    Pill,
    Calculator,
} from 'lucide-react';

export default function Dashboard() {
    const consultasExames = [
        { dia: 'Seg', consultas: 30, exames: 24 },
        { dia: 'Ter', consultas: 37, exames: 27 },
        { dia: 'Qua', consultas: 34, exames: 26 },
        { dia: 'Qui', consultas: 40, exames: 29 },
        { dia: 'Sex', consultas: 38, exames: 28 },
        { dia: 'Sáb', consultas: 25, exames: 22 },
        { dia: 'Dom', consultas: 20, exames: 18 },
    ];

    const receitaMensal = [
        { mes: 'Jan', receita: 9000 },
        { mes: 'Fev', receita: 11500 },
        { mes: 'Mar', receita: 10800 },
        { mes: 'Abr', receita: 12500 },
        { mes: 'Mai', receita: 14800 },
        { mes: 'Jun', receita: 13900 },
    ];

    const pacientesHoje = [
        {
            nome: 'Maria Silva',
            convenio: 'Unimed',
            horario: '08:30',
            status: 'Em consulta',
        },
        {
            nome: 'João Santos',
            convenio: 'Particular',
            horario: '09:00',
            status: 'Aguardando pagamento',
        },
        {
            nome: 'Ana Pereira',
            convenio: 'Bradesco Saúde',
            horario: '10:15',
            status: 'Retorno liberado',
        },
    ];

    const menuItems = [
        {
            label: 'Dashboard',
            icon: LayoutDashboard,
            href: '/faturamento/dashboard',
            active: true,
        },
        {
            label: 'Conta Hospitalar',
            icon: Calculator,
            href: '/faturamento/conta-hospitalar',
        },
        {
            label: 'Convênios',
            icon: Building2,
            href: '/faturamento/convenio',
        },
        {
            label: 'Planos',
            icon: ClipboardCheck,
            href: '/faturamento/plano',
        },
        {
            label: 'Tipo de Cobrança',
            icon: CreditCard,
            href: '/faturamento/tipo-cobranca',
        },
        {
            label: 'Pacientes',
            icon: Users,
            href: '#',
        },
        {
            label: 'Consultas e Agenda',
            icon: CalendarCheck,
            href: '#',
        },
        {
            label: 'Prontuário/PEP',
            icon: FileText,
            href: '#',
        },
        {
            label: 'Farmácia',
            icon: Pill,
            href: '#',
        },
        {
            label: 'Laboratório',
            icon: FlaskConical,
            href: '#',
        },
    ];

    return (
        <div className="min-h-screen bg-[#eef5f3] flex text-slate-700">
            <aside className="w-64 bg-[#005f5f] text-white flex flex-col">
                <div className="px-6 py-6 border-b border-white/10">
                    <div className="text-2xl font-bold tracking-wide">
                        Saúde+
                    </div>

                    <div className="text-xs text-teal-100 mt-1">
                        Sistema Hospitalar
                    </div>
                </div>

                <nav className="flex-1 px-3 py-5 space-y-1">
                    {menuItems.map((item) => {
                        const Icon = item.icon;

                        return (
                            <Link
                                key={item.label}
                                href={item.href}
                                className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition ${
                                    item.active
                                        ? 'bg-white text-[#006b6b] shadow'
                                        : 'text-teal-50 hover:bg-white/10'
                                }`}
                            >
                                <Icon size={18} />
                                <span>{item.label}</span>
                            </Link>
                        );
                    })}
                </nav>

                <div className="px-6 py-4 text-xs text-teal-100 border-t border-white/10">
                    Grupo 6 — Faturamento e Convênios
                </div>
            </aside>

            <main className="flex-1">
                <header className="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8">
                    <div className="relative w-96">
                        <Search
                            size={18}
                            className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                        />

                        <input
                            type="text"
                            placeholder="Buscar paciente, fatura ou convênio..."
                            className="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-100 border border-transparent focus:border-[#008080] focus:ring-0 text-sm"
                        />
                    </div>

                    <div className="flex items-center gap-4">
                        <button className="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                            <Bell size={18} />
                        </button>

                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-full bg-[#007f7f] text-white flex items-center justify-center">
                                <User size={18} />
                            </div>

                            <div>
                                <div className="text-sm font-semibold">
                                    Dr. Adrien
                                </div>

                                <div className="text-xs text-slate-500">
                                    Administrador
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <section className="p-8">
                    <div className="mb-6">
                        <h1 className="text-2xl font-bold text-slate-800">
                            Dashboard
                        </h1>

                        <p className="text-sm text-slate-500 mt-1">
                            Visão geral do hospital — terça-feira, 20 de maio de 2026
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
                        <IndicadorCard
                            icon={Users}
                            valor="47"
                            titulo="Pacientes hoje"
                            variacao="+12%"
                        />

                        <IndicadorCard
                            icon={CalendarCheck}
                            valor="128"
                            titulo="Consultas agendadas"
                            variacao="+8%"
                        />

                        <IndicadorCard
                            icon={ClipboardCheck}
                            valor="342"
                            titulo="Atendimentos concluídos"
                            variacao="+15%"
                        />

                        <IndicadorCard
                            icon={DollarSign}
                            valor="R$ 45,2k"
                            titulo="Receita do dia"
                            variacao="+18%"
                        />
                    </div>

                    <div className="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
                        <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                            <div className="flex justify-between items-start mb-4">
                                <div>
                                    <h2 className="font-semibold text-slate-800">
                                        Consultas & Exames
                                    </h2>

                                    <p className="text-xs text-slate-500">
                                        Últimos 7 dias
                                    </p>
                                </div>

                                <Stethoscope size={18} className="text-[#007f7f]" />
                            </div>

                            <div className="h-72">
                                <ResponsiveContainer width="100%" height="100%">
                                    <BarChart data={consultasExames}>
                                        <CartesianGrid strokeDasharray="3 3" vertical={false} />
                                        <XAxis dataKey="dia" />
                                        <YAxis />
                                        <Tooltip />

                                        <Bar
                                            dataKey="consultas"
                                            name="Consultas"
                                            fill="#007f7f"
                                            radius={[6, 6, 0, 0]}
                                        />

                                        <Bar
                                            dataKey="exames"
                                            name="Exames"
                                            fill="#9cc9c3"
                                            radius={[6, 6, 0, 0]}
                                        />
                                    </BarChart>
                                </ResponsiveContainer>
                            </div>
                        </div>

                        <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                            <div className="flex justify-between items-start mb-4">
                                <div>
                                    <h2 className="font-semibold text-slate-800">
                                        Receita Mensal
                                    </h2>

                                    <p className="text-xs text-slate-500">
                                        Últimos 6 meses
                                    </p>
                                </div>

                                <DollarSign size={18} className="text-[#007f7f]" />
                            </div>

                            <div className="h-72">
                                <ResponsiveContainer width="100%" height="100%">
                                    <LineChart data={receitaMensal}>
                                        <CartesianGrid strokeDasharray="3 3" vertical={false} />
                                        <XAxis dataKey="mes" />
                                        <YAxis />
                                        <Tooltip
                                            formatter={(value) =>
                                                `R$ ${value.toLocaleString('pt-BR')}`
                                            }
                                        />

                                        <Line
                                            type="monotone"
                                            dataKey="receita"
                                            name="Receita"
                                            stroke="#007f7f"
                                            strokeWidth={3}
                                            dot={{ r: 4 }}
                                            activeDot={{ r: 6 }}
                                        />
                                    </LineChart>
                                </ResponsiveContainer>
                            </div>
                        </div>
                    </div>

                    <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
                        <div className="flex justify-between items-start mb-5">
                            <div>
                                <h2 className="font-semibold text-slate-800">
                                    Pacientes de Hoje
                                </h2>

                                <p className="text-xs text-slate-500">
                                    Pacientes aguardando atendimento, pagamento ou retorno
                                </p>
                            </div>

                            <button className="text-sm text-[#007f7f] font-medium">
                                Ver todos
                            </button>
                        </div>

                        <div className="space-y-3">
                            {pacientesHoje.map((paciente) => (
                                <div
                                    key={paciente.nome}
                                    className="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-100"
                                >
                                    <div className="flex items-center gap-3">
                                        <div className="w-10 h-10 rounded-full bg-[#007f7f] text-white flex items-center justify-center font-semibold">
                                            {paciente.nome.charAt(0)}
                                        </div>

                                        <div>
                                            <div className="font-semibold text-sm text-slate-800">
                                                {paciente.nome}
                                            </div>

                                            <div className="text-xs text-slate-500">
                                                {paciente.convenio}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex items-center gap-4">
                                        <span className="text-xs text-slate-500">
                                            {paciente.horario}
                                        </span>

                                        <StatusBadge status={paciente.status} />
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            </main>
        </div>
    );
}

function IndicadorCard({ icon: Icon, valor, titulo, variacao }) {
    return (
        <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <div className="flex justify-between items-start">
                <div className="w-11 h-11 rounded-xl bg-[#e1f2ef] text-[#007f7f] flex items-center justify-center">
                    <Icon size={21} />
                </div>

                <span className="text-xs text-emerald-600 font-medium">
                    {variacao}
                </span>
            </div>

            <div className="mt-5">
                <div className="text-2xl font-bold text-slate-800">
                    {valor}
                </div>

                <div className="text-sm text-slate-500 mt-1">
                    {titulo}
                </div>
            </div>
        </div>
    );
}

function StatusBadge({ status }) {
    const classes = {
        'Em consulta': 'bg-blue-100 text-blue-700',
        'Aguardando pagamento': 'bg-amber-100 text-amber-700',
        'Retorno liberado': 'bg-emerald-100 text-emerald-700',
    };

    return (
        <span
            className={`px-3 py-1 rounded-full text-xs font-medium ${
                classes[status] || 'bg-slate-100 text-slate-600'
            }`}
        >
            {status}
        </span>
    );
}