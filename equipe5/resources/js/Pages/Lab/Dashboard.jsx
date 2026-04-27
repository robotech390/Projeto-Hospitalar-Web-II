

import React from "react";
import { usePage } from "@inertiajs/react";
import AppLayout from "./components/AppLayout";
import { Card, CardContent } from "./components/Card";
import StatusBadge from "./components/StatusBadge";
import Trend from "./components/Trend";
import { Clock, ClipboardList, BarChart3, Upload, DollarSign } from "lucide-react";
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  Tooltip,
  ResponsiveContainer,
  CartesianGrid,
  AreaChart,
  Area,
} from "recharts";


// Mapeamento local dos cards de estatísticas (label, icon, format)
const statsConfig = [
  {
    key: 'pendentes',
    label: 'Exames Pendentes',
    icon: ClipboardList,
    format: (v) => v,
  },
  {
    key: 'emAnalise',
    label: 'Em Análise',
    icon: BarChart3,
    format: (v) => v,
  },
  {
    key: 'concluidosHoje',
    label: 'Concluídos Hoje',
    icon: Upload,
    format: (v) => v,
  },
  {
    key: 'receitaHoje',
    label: 'Receita do Dia',
    icon: DollarSign,
    format: formatCurrency,
  },
];

function formatCurrency(value) {
  return `R$ ${Number(value).toFixed(2)}`;
}

export default function Dashboard() {
  const { pendentes = {}, emAnalise = {}, concluidosHoje = {}, receitaHoje = {}, weekData = [], revenueData = [], upcomingExams = [] } = usePage().props;
  const backendStats = { pendentes, emAnalise, concluidosHoje, receitaHoje };

  const stats = statsConfig.map((cfg) => {
    const backend = backendStats[cfg.key];
    const value = backend && backend.value !== undefined ? cfg.format(backend.value) : undefined;
    const trendValue = backend && backend.trendValue !== undefined ? backend.trendValue : undefined;
    const trendPercentual = backend && backend.trendPercentual !== undefined ? backend.trendPercentual : undefined;
    return {
      ...cfg,
      value,
      trendValue,
      trendPercentual,
    };
  });

  return (
    <AppLayout>
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-muted-foreground">Dashboard Laboratório</h1>
          <p className="text-sm text-muted-foreground/70 mt-1">Visão geral das operações do laboratório</p>
        </div>
        {/* Cards de estatísticas */}
        <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
          {stats.map((stat) => {
            const Icon = stat.icon;
            return (
              <Card key={stat.label} className="flex flex-col p-5 gap-3">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-3">
                    <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary/10">
                      <Icon className="w-5 h-5 text-secondary" />
                    </div>
                    <div>
                      <div className="text-2xl font-bold text-muted-foreground">{stat.value ?? '-'}</div>
                      <div className="text-xs text-muted-foreground/70 font-medium">{stat.label}</div>
                    </div>
                  </div>
                  <Trend percentual={stat.trendPercentual} value={stat.trendValue} />
                </div>
              </Card>
            );
          })}
        </div>
        {/* Gráficos */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card className="h-80 flex flex-col">
            <CardContent className="flex-1 flex flex-col p-5">
              <div className="flex items-center justify-between mb-2">
                <div>
                  <div className="font-semibold text-base text-muted-foreground">Exames por Dia</div>
                  <div className="text-xs text-muted-foreground/60">Distribuição semanal dos exames</div>
                </div>
              </div>
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={weekData} className="mt-2">
                  <CartesianGrid strokeDasharray="3 3" vertical={false} />
                  <XAxis dataKey="dia" axisLine={false} tickLine={false} />
                  <YAxis axisLine={false} tickLine={false} />
                  <Tooltip />
                  <Bar dataKey="sangue" fill="hsl(var(--chart-2))" radius={[4, 4, 0, 0]} name="Sangue" />
                  <Bar dataKey="imagem" fill="hsl(var(--chart-1))" radius={[4, 4, 0, 0]} name="Imagem" />
                </BarChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
          <Card className="h-80 flex flex-col">
            <CardContent className="flex-1 flex flex-col p-5">
              <div className="flex items-center justify-between mb-2">
                <div>
                  <div className="font-semibold text-base text-muted-foreground">Receita Mensal</div>
                  <div className="text-xs text-muted-foreground/60">Evolução da receita ao longo do ano</div>
                </div>
              </div>
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={revenueData} className="mt-2">
                  <CartesianGrid strokeDasharray="3 3" vertical={false} />
                  <XAxis dataKey="mes" axisLine={false} tickLine={false} />
                  <YAxis axisLine={false} tickLine={false} tickFormatter={formatCurrency} />
                  <Tooltip formatter={(v) => formatCurrency(v)} />
                  <Area type="monotone" dataKey="valor" stroke="hsl(var(--chart-3))" fill="hsl(var(--chart-2) / 0.15)" name="Receita" strokeWidth={2} />
                </AreaChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
        </div>
        {/* Listagem de próximos exames */}
        <Card>
          <CardContent className="p-5">
            <div className="flex items-center gap-2 mb-4">
              <Clock className="w-5 h-5 text-primary" />
              <div className="font-semibold text-base text-muted-foreground">Próximos Exames</div>
              <div className="text-xs text-muted-foreground/60">(até 5 próximos agendados)</div>
            </div>
            <ul className="divide-y divide-gray-200">
              {upcomingExams.map((exam, idx) => (
                <li key={idx} className="flex items-center py-3 gap-4">
                  <div className="flex-1 min-w-0">
                    <div className="font-medium text-muted-foreground truncate">{exam.paciente}</div>
                    <div className="text-xs text-muted-foreground/60 truncate">{exam.medico} • {exam.exame}</div>
                  </div>
                  <div className="text-xs text-muted-foreground/70 w-14 text-right">{exam.horario}</div>
                  <StatusBadge status={exam.status} />
                </li>
              ))}
            </ul>
          </CardContent>
        </Card>
      </div>
    </AppLayout>
  );
}
