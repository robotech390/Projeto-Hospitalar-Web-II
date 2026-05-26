import { Card, CardContent } from './components/Card';
import StatusBadge from './components/StatusBadge';
import { Clock, FlaskConical, Microscope, CheckCircle } from 'lucide-react';
import AppLayout from './components/AppLayout';

const statusSteps = [
    { status: 'Pendente', icon: Clock, label: 'Pendente' },
    { status: 'Coletado', icon: FlaskConical, label: 'Coletado' },
    { status: 'Em Análise', icon: Microscope, label: 'Em Análise' },
    { status: 'Concluído', icon: CheckCircle, label: 'Concluído' },
];
const statusOrder = { Pendente: 0, Coletado: 1, 'Em Análise': 2, Concluído: 3 };


export default function ExamStatus({ orders = [] }) {
    const counts = orders.reduce((acc, o) => {
        acc[o.status] = (acc[o.status] || 0) + 1;
        return acc;
    }, {});

    return (
        <AppLayout>
            <div>
                <h1 className="text-2xl font-bold">Status dos Exames</h1>
                <p className="text-sm text-gray-500">Acompanhe o progresso de todos os exames</p>
            </div>

            <div className="my-4 overflow-x-auto">
                <div className="flex gap-3 min-w-[700px]">
                    {statusSteps.map((s) => (
                        <Card
                            key={s.status}
                            className="flex-1 min-w-[160px] shadow-sm"
                        >
                            <CardContent className="flex items-center gap-3 p-4">
                                <div className={`flex h-10 w-10 items-center justify-center rounded-lg ${s.status === 'Pendente' ? 'bg-yellow-100' :
                                        s.status === 'Coletado' ? 'bg-blue-100' :
                                            s.status === 'Em Análise' ? 'bg-purple-100' :
                                                'bg-green-100'
                                    }`}>
                                    <s.icon className={`h-5 w-5 ${s.status === 'Pendente' ? 'text-yellow-800' :
                                            s.status === 'Coletado' ? 'text-blue-800' :
                                                s.status === 'Em Análise' ? 'text-purple-800' :
                                                    'text-green-800'
                                        }`} />
                                </div>
                                <div>
                                    <p className="text-xl font-bold tabular-nums">{counts[s.status] || 0}</p>
                                    <p className="text-xs text-gray-500">{s.label}</p>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            </div>

            <Card className="shadow-sm">
                <CardContent className="p-5">
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b">
                                    <th className="py-2 px-2 text-left font-semibold">Paciente</th>
                                    <th className="py-2 px-2 text-left font-semibold">Exame</th>
                                    <th className="py-2 px-2 text-left font-semibold">Tipo</th>
                                    <th className="py-2 px-2 text-left font-semibold">Médico</th>
                                    <th className="py-2 px-2 text-left font-semibold">Data</th>
                                    <th className="py-2 px-2 text-left font-semibold">Progresso</th>
                                    <th className="py-2 px-2 text-left font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {orders.map((o) => (
                                    <tr key={o.id} className="border-b hover:bg-gray-50">
                                        <td className="py-2 px-2">
                                            <div className="flex items-center gap-2">
                                                <span className="font-medium">{o.paciente}</span>
                                            </div>
                                        </td>
                                        <td className="py-2 px-2">{o.exame}</td>
                                        <td className="py-2 px-2"><span className="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium">{o.tipo}</span></td>
                                        <td className="py-2 px-2 text-gray-500">{o.medico}</td>
                                        <td className="py-2 px-2 text-gray-500 tabular-nums">{o.dataSolicitacao}</td>
                                        <td className="py-2 px-2">
                                            <div className="flex items-center gap-1">
                                                {statusSteps.map((step, i) => (
                                                    <div
                                                        key={step.status}
                                                        className={`h-1.5 w-6 rounded-full ${i <= statusOrder[o.status]
                                                                ? o.status === 'Concluído' ? 'bg-status-concluido' :
                                                                    o.status === 'Em Análise' ? 'bg-status-analise' :
                                                                        o.status === 'Coletado' ? 'bg-status-coletado' :
                                                                            'bg-status-pendente'
                                                                : 'bg-gray-200'
                                                            }`}
                                                    />
                                                ))}
                                            </div>
                                        </td>
                                        <td className="py-2 px-2"><StatusBadge status={o.status} /></td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                        {orders.length === 0 && (
                            <div className="text-center text-gray-400 py-8">Nenhum exame encontrado</div>
                        )}
                    </div>
                </CardContent>
            </Card>
        </AppLayout>
    );
}
