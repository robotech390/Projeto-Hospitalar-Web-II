import { useState } from "react";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from "@/components/ui/dialog";
import { Search, Upload, FileText, CheckCircle2 } from "lucide-react";
import StatusBadge from "@/components/StatusBadge";
import { pedidosExames, type ExamOrder } from "@/data/mockData";
import { toast } from "sonner";

const LancarResultados = () => {
  const [orders, setOrders] = useState<ExamOrder[]>(pedidosExames);
  const [search, setSearch] = useState("");
  const [selected, setSelected] = useState<ExamOrder | null>(null);
  const [laudo, setLaudo] = useState("");
  const [fileName, setFileName] = useState("");

  const emAnalise = orders.filter((o) => o.status === "Em Análise" || o.status === "Coletado");
  const filtered = emAnalise.filter((o) => o.paciente.toLowerCase().includes(search.toLowerCase()) || o.exame.toLowerCase().includes(search.toLowerCase()));

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) setFileName(file.name);
  };

  const handleConcluir = () => {
    if (!selected) return;
    if (!laudo && !fileName) {
      toast.error("Informe o laudo ou anexe um arquivo.");
      return;
    }
    setOrders((prev) =>
      prev.map((o) => (o.id === selected.id ? { ...o, status: "Concluído" as const, resultado: laudo || fileName } : o))
    );
    toast.success(`Resultado do exame de ${selected.paciente} lançado!`);
    setSelected(null);
    setLaudo("");
    setFileName("");
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Lançamento de Resultados</h1>
        <p className="text-sm text-muted-foreground">Insira laudos e anexe arquivos para exames em análise</p>
      </div>

      <div className="relative">
        <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        <Input className="pl-9" placeholder="Buscar paciente ou exame..." value={search} onChange={(e) => setSearch(e.target.value)} />
      </div>

      {filtered.length === 0 && (
        <Card className="shadow-sm">
          <CardContent className="flex flex-col items-center justify-center py-12 text-muted-foreground">
            <CheckCircle2 className="mb-2 h-10 w-10" />
            <p className="text-sm font-medium">Todos os exames foram concluídos</p>
          </CardContent>
        </Card>
      )}

      <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {filtered.map((o) => (
          <Card key={o.id} className="cursor-pointer shadow-sm transition-shadow hover:shadow-md" onClick={() => { setSelected(o); setLaudo(""); setFileName(""); }}>
            <CardContent className="p-5">
              <div className="mb-3 flex items-center justify-between">
                <div className="flex h-9 w-9 items-center justify-center rounded-full bg-secondary text-xs font-bold text-secondary-foreground">
                  {o.iniciais}
                </div>
                <StatusBadge status={o.status} />
              </div>
              <p className="text-sm font-semibold">{o.paciente}</p>
              <p className="text-xs text-muted-foreground">{o.exame}</p>
              <p className="mt-1 text-xs text-muted-foreground">{o.medico} · {o.dataSolicitacao}</p>
            </CardContent>
          </Card>
        ))}
      </div>

      <Dialog open={!!selected} onOpenChange={(open) => !open && setSelected(null)}>
        <DialogContent className="sm:max-w-lg">
          <DialogHeader>
            <DialogTitle>Lançar Resultado</DialogTitle>
          </DialogHeader>
          {selected && (
            <div className="space-y-4 py-2">
              <div className="rounded-lg bg-muted p-3">
                <p className="text-sm font-semibold">{selected.paciente}</p>
                <p className="text-xs text-muted-foreground">{selected.exame} · {selected.tipo}</p>
                <p className="text-xs text-muted-foreground">{selected.medico} · Solicitado em {selected.dataSolicitacao}</p>
              </div>

              <div>
                <Label>Laudo Médico</Label>
                <Textarea value={laudo} onChange={(e) => setLaudo(e.target.value)} placeholder="Digite o laudo do exame..." rows={5} />
              </div>

              <div>
                <Label>Anexar Arquivo (PDF/Imagem)</Label>
                <div className="mt-1 flex items-center gap-3">
                  <label className="flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-input bg-muted/50 px-4 py-3 text-sm text-muted-foreground transition-colors hover:bg-muted">
                    <Upload className="h-4 w-4" />
                    {fileName || "Selecionar arquivo..."}
                    <input type="file" accept=".pdf,.jpg,.jpeg,.png" className="hidden" onChange={handleFileChange} />
                  </label>
                  {fileName && <FileText className="h-5 w-5 text-secondary" />}
                </div>
              </div>
            </div>
          )}
          <DialogFooter>
            <DialogClose asChild><Button variant="outline">Cancelar</Button></DialogClose>
            <Button onClick={handleConcluir}>Concluir Exame</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
};

export default LancarResultados;
