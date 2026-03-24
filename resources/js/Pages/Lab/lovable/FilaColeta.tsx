import { useState } from "react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Search, CheckCircle } from "lucide-react";
import StatusBadge from "@/components/StatusBadge";
import { pedidosExames, type ExamOrder } from "@/data/mockData";
import { toast } from "sonner";

const FilaColeta = () => {
  const [orders, setOrders] = useState<ExamOrder[]>(pedidosExames);
  const [search, setSearch] = useState("");
  const [filterTipo, setFilterTipo] = useState("Todos");

  const fila = orders.filter((o) => o.status === "Pendente" || o.status === "Coletado");

  const filtered = fila.filter((o) => {
    const matchSearch = o.paciente.toLowerCase().includes(search.toLowerCase()) || o.exame.toLowerCase().includes(search.toLowerCase());
    const matchTipo = filterTipo === "Todos" || o.tipo === filterTipo;
    return matchSearch && matchTipo;
  });

  const confirmarColeta = (id: string) => {
    setOrders((prev) =>
      prev.map((o) => (o.id === id ? { ...o, status: "Coletado" as const } : o))
    );
    toast.success("Coleta confirmada!");
  };

  const enviarAnalise = (id: string) => {
    setOrders((prev) =>
      prev.map((o) => (o.id === id ? { ...o, status: "Em Análise" as const } : o))
    );
    toast.success("Enviado para análise!");
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Fila de Coleta</h1>
        <p className="text-sm text-muted-foreground">Pacientes aguardando coleta ou realização de exame</p>
      </div>

      <div className="flex flex-wrap items-center gap-3">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
          <Input className="pl-9" placeholder="Buscar paciente ou exame..." value={search} onChange={(e) => setSearch(e.target.value)} />
        </div>
        <Select value={filterTipo} onValueChange={setFilterTipo}>
          <SelectTrigger className="w-40"><SelectValue /></SelectTrigger>
          <SelectContent>
            <SelectItem value="Todos">Todos</SelectItem>
            <SelectItem value="Sangue">Sangue</SelectItem>
            <SelectItem value="Raio-X">Raio-X</SelectItem>
            <SelectItem value="Imagem">Imagem</SelectItem>
          </SelectContent>
        </Select>
      </div>

      <div className="space-y-3">
        {filtered.length === 0 && (
          <Card className="shadow-sm">
            <CardContent className="flex flex-col items-center justify-center py-12 text-muted-foreground">
              <CheckCircle className="mb-2 h-10 w-10" />
              <p className="text-sm font-medium">Nenhum paciente na fila</p>
            </CardContent>
          </Card>
        )}
        {filtered.map((o) => (
          <Card key={o.id} className="shadow-sm transition-shadow hover:shadow-md">
            <CardContent className="flex items-center gap-4 p-5">
              <div className="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-secondary text-sm font-bold text-secondary-foreground">
                {o.iniciais}
              </div>
              <div className="flex-1 min-w-0">
                <p className="text-sm font-semibold">{o.paciente}</p>
                <p className="text-xs text-muted-foreground">{o.medico} · {o.exame}</p>
                <p className="text-xs text-muted-foreground">Tipo: {o.tipo} · Solicitado: {o.dataSolicitacao}</p>
              </div>
              <span className="text-sm tabular-nums text-muted-foreground">{o.horario}</span>
              <StatusBadge status={o.status} />
              <div className="flex gap-2">
                {o.status === "Pendente" && (
                  <Button size="sm" onClick={() => confirmarColeta(o.id)}>Confirmar Coleta</Button>
                )}
                {o.status === "Coletado" && (
                  <Button size="sm" variant="outline" onClick={() => enviarAnalise(o.id)}>Enviar p/ Análise</Button>
                )}
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
};

export default FilaColeta;
