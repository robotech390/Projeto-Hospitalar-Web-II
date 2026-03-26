import { FlaskConical, CalendarCheck, Clock, DollarSign, TrendingUp, TrendingDown } from "lucide-react";
import { Card, CardContent } from "@/components/ui/card";
import { pedidosExames } from "@/data/mockData";
import StatusBadge from "@/components/StatusBadge";
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, AreaChart, Area } from "recharts";

const stats = [
  { label: "Exames Pendentes", value: pedidosExames.filter(p => p.status === "Pendente").length, icon: Clock, trend: "+3", up: true },
  { label: "Em Análise", value: pedidosExames.filter(p => p.status === "Em Análise").length, icon: FlaskConical, trend: "+1", up: true },
  { label: "Concluídos Hoje", value: pedidosExames.filter(p => p.status === "Concluído").length, icon: CalendarCheck, trend: "+12%", up: true },
  { label: "Receita do Dia", value: "R$ 2.4k", icon: DollarSign, trend: "+8%", up: true },
];

const weekData = [
  { day: "Seg", sangue: 28, imagem: 14 },
  { day: "Ter", sangue: 35, imagem: 18 },
  { day: "Qua", sangue: 40, imagem: 22 },
  { day: "Qui", sangue: 48, imagem: 20 },
  { day: "Sex", sangue: 42, imagem: 16 },
  { day: "Sáb", sangue: 20, imagem: 10 },
  { day: "Dom", sangue: 8, imagem: 4 },
];

const revenueData = [
  { month: "Jan", valor: 42000 },
  { month: "Fev", valor: 48000 },
  { month: "Mar", valor: 45000 },
  { month: "Abr", valor: 52000 },
  { month: "Mai", valor: 58000 },
  { month: "Jun", valor: 61000 },
];

const today = new Date().toLocaleDateString("pt-BR", { weekday: "long", year: "numeric", month: "long", day: "numeric" });

const DashboardPage = () => (
  <div className="space-y-6">
    <div>
      <h1 className="text-2xl font-bold text-foreground">Dashboard — Laboratório</h1>
      <p className="text-sm text-muted-foreground">Visão geral — {today}</p>
    </div>

    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      {stats.map((s) => (
        <Card key={s.label} className="shadow-sm">
          <CardContent className="flex items-start justify-between p-5">
            <div className="space-y-2">
              <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary/10">
                <s.icon className="h-5 w-5 text-secondary" />
              </div>
              <p className="text-2xl font-bold tabular-nums">{s.value}</p>
              <p className="text-xs text-muted-foreground">{s.label}</p>
            </div>
            <span className={`flex items-center gap-0.5 text-xs font-semibold ${s.up ? "text-status-concluido" : "text-destructive"}`}>
              {s.up ? <TrendingUp className="h-3.5 w-3.5" /> : <TrendingDown className="h-3.5 w-3.5" />}
              {s.trend}
            </span>
          </CardContent>
        </Card>
      ))}
    </div>

    <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
      <Card className="shadow-sm">
        <CardContent className="p-5">
          <h2 className="text-base font-semibold">Exames por Dia</h2>
          <p className="mb-4 text-xs text-muted-foreground">Últimos 7 dias</p>
          <div className="h-64">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={weekData}>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="hsl(var(--border))" />
                <XAxis dataKey="day" tick={{ fontSize: 12 }} stroke="hsl(var(--muted-foreground))" />
                <YAxis tick={{ fontSize: 12 }} stroke="hsl(var(--muted-foreground))" />
                <Tooltip />
                <Bar dataKey="sangue" fill="hsl(var(--primary))" radius={[4, 4, 0, 0]} name="Sangue" />
                <Bar dataKey="imagem" fill="hsl(var(--accent))" radius={[4, 4, 0, 0]} name="Imagem" />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </CardContent>
      </Card>

      <Card className="shadow-sm">
        <CardContent className="p-5">
          <h2 className="text-base font-semibold">Receita Mensal</h2>
          <p className="mb-4 text-xs text-muted-foreground">Últimos 6 meses</p>
          <div className="h-64">
            <ResponsiveContainer width="100%" height="100%">
              <AreaChart data={revenueData}>
                <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="hsl(var(--border))" />
                <XAxis dataKey="month" tick={{ fontSize: 12 }} stroke="hsl(var(--muted-foreground))" />
                <YAxis tick={{ fontSize: 12 }} stroke="hsl(var(--muted-foreground))" />
                <Tooltip formatter={(v: number) => `R$ ${(v / 1000).toFixed(1)}k`} />
                <Area type="monotone" dataKey="valor" stroke="hsl(var(--secondary))" fill="hsl(var(--secondary) / 0.15)" strokeWidth={2} name="Receita" />
              </AreaChart>
            </ResponsiveContainer>
          </div>
        </CardContent>
      </Card>
    </div>

    <Card className="shadow-sm">
      <CardContent className="p-5">
        <div className="mb-4 flex items-center justify-between">
          <div>
            <h2 className="text-base font-semibold">Próximos Exames</h2>
            <p className="text-xs text-muted-foreground">Pacientes aguardando</p>
          </div>
          <Clock className="h-5 w-5 text-muted-foreground" />
        </div>
        <div className="divide-y divide-border">
          {pedidosExames.filter(p => p.status === "Pendente" || p.status === "Coletado").slice(0, 5).map((p) => (
            <div key={p.id} className="flex items-center gap-4 py-3">
              <div className="flex h-9 w-9 items-center justify-center rounded-full bg-secondary text-xs font-bold text-secondary-foreground">
                {p.iniciais}
              </div>
              <div className="flex-1">
                <p className="text-sm font-medium">{p.paciente}</p>
                <p className="text-xs text-muted-foreground">{p.medico} · {p.exame}</p>
              </div>
              <span className="text-sm tabular-nums text-muted-foreground">{p.horario}</span>
              <StatusBadge status={p.status} />
            </div>
          ))}
        </div>
      </CardContent>
    </Card>
  </div>
);

export default DashboardPage;
