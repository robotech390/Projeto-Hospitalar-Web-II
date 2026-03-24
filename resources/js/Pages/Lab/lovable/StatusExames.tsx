import { useState } from "react";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Search, Clock, FlaskConical, Microscope, CheckCircle2 } from "lucide-react";
import StatusBadge, { type ExamStatus } from "@/components/StatusBadge";
import { pedidosExames } from "@/data/mockData";

const statusSteps: { status: ExamStatus; icon: React.ElementType; label: string }[] = [
  { status: "Pendente", icon: Clock, label: "Pendente" },
  { status: "Coletado", icon: FlaskConical, label: "Coletado" },
  { status: "Em Análise", icon: Microscope, label: "Em Análise" },
  { status: "Concluído", icon: CheckCircle2, label: "Concluído" },
];

const statusOrder: Record<ExamStatus, number> = { Pendente: 0, Coletado: 1, "Em Análise": 2, Concluído: 3 };

const StatusExames = () => {
  const [search, setSearch] = useState("");
  const [filterStatus, setFilterStatus] = useState("Todos");

  const filtered = pedidosExames.filter((o) => {
    const matchSearch = o.paciente.toLowerCase().includes(search.toLowerCase()) || o.exame.toLowerCase().includes(search.toLowerCase());
    const matchStatus = filterStatus === "Todos" || o.status === filterStatus;
    return matchSearch && matchStatus;
  });

  const counts = pedidosExames.reduce(
    (acc, o) => {
      acc[o.status] = (acc[o.status] || 0) + 1;
      return acc;
    },
    {} as Record<string, number>
  );

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Status dos Exames</h1>
        <p className="text-sm text-muted-foreground">Acompanhe o progresso de todos os exames</p>
      </div>

      <div className="grid grid-cols-2 gap-3 lg:grid-cols-4">
        {statusSteps.map((s) => (
          <Card
            key={s.status}
            className={`cursor-pointer shadow-sm transition-shadow hover:shadow-md ${filterStatus === s.status ? "ring-2 ring-secondary" : ""}`}
            onClick={() => setFilterStatus(filterStatus === s.status ? "Todos" : s.status)}
          >
            <CardContent className="flex items-center gap-3 p-4">
              <div className={`flex h-10 w-10 items-center justify-center rounded-lg ${
                s.status === "Pendente" ? "bg-status-pendente/10" :
                s.status === "Coletado" ? "bg-status-coletado/10" :
                s.status === "Em Análise" ? "bg-status-analise/10" :
                "bg-status-concluido/10"
              }`}>
                <s.icon className={`h-5 w-5 ${
                  s.status === "Pendente" ? "text-status-pendente" :
                  s.status === "Coletado" ? "text-status-coletado" :
                  s.status === "Em Análise" ? "text-status-analise" :
                  "text-status-concluido"
                }`} />
              </div>
              <div>
                <p className="text-xl font-bold tabular-nums">{counts[s.status] || 0}</p>
                <p className="text-xs text-muted-foreground">{s.label}</p>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      <Card className="shadow-sm">
        <CardContent className="p-5">
          <div className="mb-4 flex flex-wrap items-center gap-3">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input className="pl-9" placeholder="Buscar paciente ou exame..." value={search} onChange={(e) => setSearch(e.target.value)} />
            </div>
            <Select value={filterStatus} onValueChange={setFilterStatus}>
              <SelectTrigger className="w-44"><SelectValue /></SelectTrigger>
              <SelectContent>
                <SelectItem value="Todos">Todos os Status</SelectItem>
                <SelectItem value="Pendente">Pendente</SelectItem>
                <SelectItem value="Coletado">Coletado</SelectItem>
                <SelectItem value="Em Análise">Em Análise</SelectItem>
                <SelectItem value="Concluído">Concluído</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Paciente</TableHead>
                <TableHead>Exame</TableHead>
                <TableHead>Tipo</TableHead>
                <TableHead>Médico</TableHead>
                <TableHead>Data</TableHead>
                <TableHead>Progresso</TableHead>
                <TableHead>Status</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filtered.map((o) => (
                <TableRow key={o.id}>
                  <TableCell>
                    <div className="flex items-center gap-2">
                      <div className="flex h-8 w-8 items-center justify-center rounded-full bg-secondary text-[10px] font-bold text-secondary-foreground">
                        {o.iniciais}
                      </div>
                      <span className="font-medium">{o.paciente}</span>
                    </div>
                  </TableCell>
                  <TableCell>{o.exame}</TableCell>
                  <TableCell><span className="rounded-md bg-muted px-2 py-0.5 text-xs font-medium">{o.tipo}</span></TableCell>
                  <TableCell className="text-muted-foreground">{o.medico}</TableCell>
                  <TableCell className="tabular-nums text-muted-foreground">{o.dataSolicitacao}</TableCell>
                  <TableCell>
                    <div className="flex items-center gap-1">
                      {statusSteps.map((step, i) => (
                        <div
                          key={step.status}
                          className={`h-1.5 w-6 rounded-full ${
                            i <= statusOrder[o.status]
                              ? o.status === "Concluído" ? "bg-status-concluido" :
                                o.status === "Em Análise" ? "bg-status-analise" :
                                o.status === "Coletado" ? "bg-status-coletado" :
                                "bg-status-pendente"
                              : "bg-muted"
                          }`}
                        />
                      ))}
                    </div>
                  </TableCell>
                  <TableCell><StatusBadge status={o.status} /></TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  );
};

export default StatusExames;
