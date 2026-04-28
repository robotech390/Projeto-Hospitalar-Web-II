

import { useState, useEffect } from 'react';
import axios from 'axios';
import { Card, CardContent } from './components/Card';
import Button from './components/Button';
import Input from './components/Input';
import Textarea from './components/Textarea';
import StatusBadge from './components/StatusBadge';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogClose } from './components/Dialog';
import { Search, Upload, FileText, CheckCircle } from 'lucide-react';
import AppLayout from './components/AppLayout';
import { toast } from './toast';


export default function ResultEntryForm({ orders = [] }) {
  // Nenhuma lógica local de filtro ou busca, apenas exibe os dados recebidos do backend
  const [examOrders, setExamOrders] = useState(orders || []);
  const [selected, setSelected] = useState(null);
  const [search, setSearch] = useState('');
  const [laudo, setLaudo] = useState('');
  const [fileName, setFileName] = useState('');
  const [file, setFile] = useState(null);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    setExamOrders(orders || []);
  }, [orders]);

  const handleFileChange = (e) => {
    const selectedFile = e.target.files?.[0];
    if (selectedFile) {
      setFileName(selectedFile.name);
      setFile(selectedFile);
    } else {
      setFileName('');
      setFile(null);
    }
  };

  const handleSave = async () => {
    if (!laudo || !selected) return;

    setIsSubmitting(true);
    try {
      const formData = new FormData();
      formData.append('laudo', laudo);
      if (file) {
        formData.append('arquivo', file);
      }

      await axios.post(`/lab/result-entry/${selected.id}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      // Atualização Otimista
      setExamOrders(prev => prev.filter(o => o.id !== selected.id));
      setDialogOpen(false);
      toast.success('Resultado salvo com sucesso!');
    } catch (error) {
      console.error(error);
      toast.error('Erro ao salvar resultado. Verifique os dados e tente novamente.');
    } finally {
      setIsSubmitting(false);
    }
  };

  const filtered = examOrders.filter((o) => {
    const matchesSearch = o.paciente.toLowerCase().includes(search.toLowerCase()) ||
      o.exame.toLowerCase().includes(search.toLowerCase());
    return matchesSearch && (o.status === 'Em Análise' || o.status === 'Coletado');
  });

  return (
    <AppLayout>
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
            <Button onClick={handleSave} disabled={isSubmitting || !laudo}>
              {isSubmitting ? 'Salvando...' : 'Salvar Resultado'}
            </Button>
            <DialogClose asChild>
              <Button variant="ghost" disabled={isSubmitting}>Cancelar</Button>
            </DialogClose>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </AppLayout>
  );
}
