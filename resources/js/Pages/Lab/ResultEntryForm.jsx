
import { useState } from 'react';
import { Card, CardContent } from './components/Card';
import Button from './components/Button';
import Input from './components/Input';
import Textarea from './components/Textarea';
import StatusBadge from './components/StatusBadge';
import { pedidosExames } from './data';
import { toastSuccess, toastError } from './toast';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from './components/Dialog';
import { Search, Upload, FileText, CheckCircle } from 'lucide-react';

export default function ResultEntryForm() {
  const [orders, setOrders] = useState(pedidosExames.map(o => ({ ...o }))); // cópia para estado local
  const [search, setSearch] = useState('');
  const [selected, setSelected] = useState(null);
  const [laudo, setLaudo] = useState('');
  const [fileName, setFileName] = useState('');
  const [dialogOpen, setDialogOpen] = useState(false);

  // Exames em análise ou coletados
  const emAnalise = orders.filter((o) => o.status === 'Em Análise' || o.status === 'Coletado');
  const filtered = emAnalise.filter((o) =>
    o.paciente.toLowerCase().includes(search.toLowerCase()) ||
    o.exame.toLowerCase().includes(search.toLowerCase())
  );

  const handleFileChange = (e) => {
    const file = e.target.files?.[0];
    if (file) setFileName(file.name);
  };

  const handleConcluir = () => {
    if (!selected) return;
    if (!laudo && !fileName) {
      toastError('Informe o laudo ou anexe um arquivo.');
      return;
    }
    setOrders((prev) =>
      prev.map((o) => (o.id === selected.id ? { ...o, status: 'Concluído', resultado: laudo || fileName } : o))
    );
    toastSuccess(`Resultado do exame de ${selected.paciente} lançado!`);
    setSelected(null);
    setLaudo('');
    setFileName('');
    setDialogOpen(false);
  };

  return (
    <div className="space-y-6 p-6 bg-slate-50 min-h-screen">
      <div className="max-w-4xl mx-auto">
        <div>
          <h1 className="text-2xl font-bold">Lançamento de Resultados</h1>
          <p className="text-sm text-gray-500">Insira laudos e anexe arquivos para exames em análise</p>
        </div>

        <div className="relative my-4">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
          <Input className="pl-9" placeholder="Buscar paciente ou exame..." value={search} onChange={e => setSearch(e.target.value)} />
        </div>

        {filtered.length === 0 && (
          <Card className="shadow-sm">
            <CardContent className="flex flex-col items-center justify-center py-12 text-gray-400">
              <CheckCircle className="mb-2 h-10 w-10" />
              <p className="text-sm font-medium">Todos os exames foram concluídos</p>
            </CardContent>
          </Card>
        )}

        <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
          {filtered.map((o) => (
            <Card key={o.id} className="cursor-pointer shadow-sm transition-shadow hover:shadow-md" onClick={() => { setSelected(o); setLaudo(''); setFileName(''); setDialogOpen(true); }}>
              <CardContent className="p-5">
                <div className="mb-3 flex items-center justify-between">
                  <div className="flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-700">
                    {o.iniciais}
                  </div>
                  <StatusBadge status={o.status} />
                </div>
                <p className="text-sm font-semibold">{o.paciente}</p>
                <p className="text-xs text-gray-500">{o.exame}</p>
                <p className="mt-1 text-xs text-gray-500">{o.medico} · {o.dataSolicitacao}</p>
              </CardContent>
            </Card>
          ))}
        </div>

        <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Lançar Resultado</DialogTitle>
            </DialogHeader>
            {selected && (
              <div className="space-y-4">
                <div>
                  <div className="font-semibold">Paciente:</div>
                  <div>{selected.paciente}</div>
                  <div className="text-xs text-gray-500">{selected.exame} · {selected.medico}</div>
                </div>
                <div>
                  <label className="block font-semibold mb-1">Laudo Médico</label>
                  <Textarea value={laudo} onChange={e => setLaudo(e.target.value)} placeholder="Digite o laudo..." />
                </div>
                <div>
                  <label className="block font-semibold mb-1">Anexar arquivo</label>
                  <div className="flex items-center gap-2">
                    <input id="file-upload" type="file" className="hidden" onChange={handleFileChange} />
                    <label htmlFor="file-upload">
                      <Button asChild variant="secondary" size="sm">
                        <span className="flex items-center gap-1 cursor-pointer"><Upload className="h-4 w-4" /> Anexar</span>
                      </Button>
                    </label>
                    {fileName && (
                      <span className="flex items-center gap-1 text-xs text-gray-700"><FileText className="h-4 w-4" /> {fileName}</span>
                    )}
                  </div>
                </div>
              </div>
            )}
            <DialogFooter>
              <Button onClick={handleConcluir} variant="default">Concluir</Button>
              <DialogClose asChild>
                <Button variant="ghost">Cancelar</Button>
              </DialogClose>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>
    </div>
  );
}
