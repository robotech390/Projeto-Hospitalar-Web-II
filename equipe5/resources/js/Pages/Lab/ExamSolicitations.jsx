
import { useState, useEffect } from 'react';
import { usePage, router } from '@inertiajs/react';
import axios from 'axios';
import { ClipboardList, Pencil, Plus, Trash2, User, Stethoscope, AlertCircle } from 'lucide-react';
import { Card, CardContent } from './components/Card';
import Button from './components/Button';
import clsx from 'clsx';
import AppLayout from './components/AppLayout';
import Input from './components/Input';
import Label from './components/Label';
import Textarea from './components/Textarea';
import StatusBadge from './components/StatusBadge';
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue
} from './components/Select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogClose } from './components/Dialog';
import { toast } from './toast';

function PriorityBadge({ priority }) {
  const settings = {
    1: { label: 'Normal', color: 'bg-blue-100 text-blue-800' },
    2: { label: 'Urgente', color: 'bg-orange-100 text-orange-800' },
    3: { label: 'Emergência', color: 'bg-red-100 text-red-800' },
  };
  const s = settings[priority] || settings[1];
  return <span className={clsx('px-2 py-0.5 rounded-full text-xs font-semibold', s.color)}>{s.label}</span>;
}

export default function ExamSolicitations() {
  const { props } = usePage();
  const [solicitations, setSolicitations] = useState(props.solicitations || []);
  const [examTypes] = useState(props.examTypes || []);
  const [consultations] = useState(props.consultations || []);
  const [search, setSearch] = useState('');
  
  const [dialogOpen, setDialogOpen] = useState(false);
  const [editItem, setEditItem] = useState(null);
  const [form, setForm] = useState({
    id_consulta: '',
    justificativa: '',
    prioridade: '1',
    itens: []
  });
  const [processing, setProcessing] = useState(false);

  console.log('Props recebidas:', { consultations: props.consultations, examTypes: props.examTypes });

  useEffect(() => {
    setSolicitations(props.solicitations || []);
  }, [props.solicitations]);

  const filtered = solicitations.filter((s) => {
    const pacienteName = s.paciente || '';
    const justificativaText = s.justificativa || '';
    const matchSearch = pacienteName.toLowerCase().includes(search.toLowerCase()) || 
                      justificativaText.toLowerCase().includes(search.toLowerCase());
    return matchSearch;
  });

  const openNew = () => {
    setEditItem(null);
    setForm({ id_consulta: '', justificativa: '', prioridade: '1', itens: [] });
    setDialogOpen(true);
  };

  const openEdit = (item) => {
    console.log('Editando item:', item);
    setEditItem(item);
    setForm({
      id_consulta: String(item.id_consulta),
      justificativa: item.justificativa || '',
      prioridade: String(item.prioridade),
      itens: (item.itens_exame || []).map(i => ({ 
        id: i.id,
        id_tipo_exame: String(i.id_tipo_exame),
        status: i.status || 'Pendente',
        data_alteracao: i.data_alteracao
      }))
    });
    setDialogOpen(true);
  };

  const addItem = () => {
    setForm({ ...form, itens: [...form.itens, { id_tipo_exame: '' }] });
  };

  const removeItem = (index) => {
    const newItens = [...form.itens];
    newItens.splice(index, 1);
    setForm({ ...form, itens: newItens });
  };

  const updateItem = (index, value) => {
    const newItens = [...form.itens];
    newItens[index].id_tipo_exame = value;
    setForm({ ...form, itens: newItens });
  };

  const handleSave = async () => {
    if (!form.id_consulta || !form.justificativa || form.itens.length === 0) {
      toast.error('Preencha todos os campos obrigatórios e adicione pelo menos um exame.');
      return;
    }

    const isEditing = !!editItem;
    const tempId = Date.now();
    const selectedConsultation = consultations.find(c => String(c.id) === String(form.id_consulta));

    // Prepare optimistic item
    const optimisticSolicitation = {
      id: isEditing ? editItem.id : tempId,
      ...form,
      id_consulta: Number(form.id_consulta),
      prioridade: Number(form.prioridade),
      paciente: selectedConsultation?.paciente?.nome || selectedConsultation?.paciente || 'Desconhecido',
      medico: selectedConsultation?.medico?.nome || selectedConsultation?.medico || 'Desconhecido',
      itens_exame: form.itens.map(i => ({
        ...i,
        status: i.status || 'Pendente',
        tipoExame: examTypes.find(et => String(et.id) === String(i.id_tipo_exame))
      }))
    };

    const previousState = [...solicitations];
    
    // Optimistic Update
    setSolicitations(prev => 
      isEditing 
        ? prev.map(s => s.id === editItem.id ? optimisticSolicitation : s)
        : [optimisticSolicitation, ...prev]
    );
    
    setDialogOpen(false);
    setProcessing(true);

    try {
      if (isEditing) {
        await axios.post(`/lab/solicitations/update/${editItem.id}`, form);
        toast.success('Solicitação atualizada com sucesso!');
      } else {
        await axios.post('/lab/solicitations', form);
        toast.success('Solicitação cadastrada com sucesso!');
      }
      router.reload({ preserveScroll: true, only: ['solicitations'] });
    } catch (error) {
      console.error(error);
      setSolicitations(previousState);
      toast.error('Erro ao salvar solicitação.');
      if (isEditing) setDialogOpen(true);
    } finally {
      setProcessing(false);
    }
  };

  const handleDelete = async (id) => {
    if (confirm('Tem certeza que deseja excluir esta solicitação?')) {
      if (!id) {
        console.error('ID ausente no handleDelete');
        toast.error('Erro: ID não informado.');
        return;
      }

      const itemToDelete = solicitations.find(s => s.id === id);
      const previousState = [...solicitations];
      
      // Optimistic Update
      setSolicitations(prev => prev.filter(s => s.id !== id));

      try {
        await axios.post(`/lab/solicitations/delete/${id}`);
        toast.success('Solicitação removida com sucesso!');
        router.reload({ preserveScroll: true, only: ['solicitations'] });
      } catch (error) {
        console.error(error);
        setSolicitations(previousState);
        toast.error('Erro ao excluir solicitação.');
      }
    }
  };

  return (
    <AppLayout>
      <div className="flex items-center justify-between mb-4">
        <div>
          <h1 className="text-2xl font-bold">Solicitações de Exame</h1>
          <p className="text-sm text-gray-500">Gerencie os pedidos de exames laboratoriais</p>
        </div>
        <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
          <DialogTrigger asChild>
            <Button onClick={openNew} variant="default">
              <Plus className="h-4 w-4" /> Nova Solicitação
            </Button>
          </DialogTrigger>
          <DialogContent className="sm:max-w-[800px] h-[650px] flex flex-col overflow-hidden">
            <DialogHeader>
              <DialogTitle>{editItem ? 'Editar Solicitação' : 'Nova Solicitação'}</DialogTitle>
            </DialogHeader>
            <div className="flex-1 overflow-hidden flex flex-col space-y-4 py-2 mt-2">
              <div className="grid grid-cols-12 gap-4">
                <div className="col-span-8">
                  <Label>Consulta / Paciente</Label>
                  <Select value={form.id_consulta} onValueChange={v => setForm({ ...form, id_consulta: v })}>
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione a consulta" />
                    </SelectTrigger>
                    <SelectContent>
                      {(!consultations || consultations.length === 0) ? (
                        <SelectItem disabled value="none" className="text-gray-400 italic text-xs">
                          Nenhuma consulta disponível
                        </SelectItem>
                      ) : (
                        consultations.map(c => {
                          const patientName = c.paciente?.nome || c.paciente || 'Desconhecido';
                          const doctorName = c.medico?.nome || c.medico || 'Desconhecido';
                          return (
                            <SelectItem key={c.id} value={String(c.id)}>
                              Consulta #{c.id} - {patientName} (Dr. {doctorName})
                            </SelectItem>
                          );
                        })
                      )}
                    </SelectContent>
                  </Select>
                </div>
                <div className="col-span-4">
                  <Label>Prioridade</Label>
                  <Select value={form.prioridade} onValueChange={v => setForm({ ...form, prioridade: v })}>
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione a prioridade" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="1">Normal</SelectItem>
                      <SelectItem value="2">Urgente</SelectItem>
                      <SelectItem value="3">Emergência</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div>
                <Label>Justificativa Clínica</Label>
                <Textarea 
                  value={form.justificativa} 
                  onChange={e => setForm({ ...form, justificativa: e.target.value })} 
                  placeholder="Motivo da solicitação..."
                  className="resize-none"
                  rows={2}
                />
              </div>

              <div className="flex-1 flex flex-col min-h-0 pt-2">
                <div className="flex items-center justify-between mb-2">
                  <Label className="text-sm font-bold uppercase text-gray-500 tracking-wider">Itens da Solicitação</Label>
                  <Button type="button" variant="outline" size="sm" onClick={addItem} className="h-8 shadow-sm">
                    <Plus className="h-4 w-4 mr-1" /> Adicionar Exame
                  </Button>
                </div>
                
                <div className="flex-1 overflow-auto border rounded-xl bg-gray-50/50">
                  <table className="w-full text-xs">
                    <thead className="bg-slate-100 border-b sticky top-0 z-10">
                      <tr>
                        <th className="px-3 py-2 text-left font-semibold text-gray-600">Exame</th>
                        <th className="px-3 py-2 text-center font-semibold text-gray-600 w-32">Status</th>
                        <th className="px-3 py-2 text-center font-semibold text-gray-600 w-40">Atualizado</th>
                        <th className="px-3 py-2 text-center w-10"></th>
                      </tr>
                    </thead>
                    <tbody className="divide-y">
                      {form.itens.length === 0 && (
                        <tr>
                          <td colSpan={4} className="py-8 text-center text-gray-400 italic font-medium">
                            Nenhum exame selecionado. Clique em "Adicionar Exame" para começar.
                          </td>
                        </tr>
                      )}
                      {form.itens.map((item, index) => {
                        const isPendente = !editItem || item.status === 'Pendente' || !item.status;
                        return (
                          <tr key={index} className="hover:bg-gray-100/50 transition-colors">
                            <td className="px-3 py-2">
                              <Select 
                                value={item.id_tipo_exame} 
                                onValueChange={v => updateItem(index, v)}
                                disabled={!isPendente}
                              >
                                <SelectTrigger className="bg-white h-9 text-[12px] border-gray-200">
                                  <SelectValue placeholder="Selecione" />
                                </SelectTrigger>
                                <SelectContent>
                                  {examTypes.map(et => (
                                    <SelectItem key={et.id} value={String(et.id)}>
                                      {et.nome} ({et.tipo})
                                    </SelectItem>
                                  ))}
                                </SelectContent>
                              </Select>
                            </td>
                            <td className="px-3 py-2 text-center">
                              {item.status && editItem ? (
                                <StatusBadge status={item.status} />
                              ) : (
                                <span className="text-gray-300 italic">Novo</span>
                              )}
                            </td>
                            <td className="px-3 py-2 text-center text-[10px] text-gray-500 font-medium">
                              {item.data_alteracao && editItem ? (
                                new Date(item.data_alteracao).toLocaleString('pt-BR', { day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' })
                              ) : (
                                <span className="text-gray-300">-</span>
                              )}
                            </td>
                            <td className="px-3 py-2 text-center">
                              <Button 
                                type="button" 
                                variant="ghost" 
                                size="icon" 
                                className="inline-flex items-center p-2 text-gray-500 hover:text-red-600 bg-transparent hover:bg-transparent"
                                onClick={() => removeItem(index)}
                                disabled={!isPendente}
                                title={!isPendente ? "Não é possível remover exames já processados" : "Remover exame"}
                              >
                                <Trash2 className="h-4 w-4" />
                              </Button>
                            </td>
                          </tr>
                        );
                      })}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <DialogFooter className="mt-4 pt-4 border-t flex justify-between items-center w-full">
              <DialogClose setOpen={setDialogOpen} className="text-gray-500 hover:text-gray-800 transition-colors font-medium">
                Cancelar
              </DialogClose>
              <Button disabled={processing} onClick={handleSave}>{editItem ? 'Salvar' : 'Cadastrar'}</Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>

      <Card className="p-4 border rounded-xl">
        <div className="flex flex-wrap items-center gap-3 mb-4">
          <div className="relative flex-1">
            <Input className="pl-10" placeholder="Buscar por paciente ou justificativa..." value={search} onChange={e => setSearch(e.target.value)} />
            <ClipboardList className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
          </div>
        </div>

        <div className="overflow-x-auto">
          <table className="min-w-full text-sm">
            <thead>
              <tr className="border-b text-gray-500 font-medium">
                <th className="py-2 px-3 text-left">Paciente / Médico</th>
                <th className="py-2 px-3 text-left">Exames</th>
                <th className="py-2 px-3 text-left">Justificativa</th>
                <th className="py-2 px-3 text-center">Prioridade</th>
                <th className="py-2 px-3 text-center">Ações</th>
              </tr>
            </thead>
            <tbody>
              {filtered.length === 0 && (
                <tr>
                  <td colSpan={5} className="py-12 text-center text-gray-400">Nenhuma solicitação encontrada</td>
                </tr>
              )}
              {filtered.map((s) => (
                <tr key={s.id} className="border-b last:border-0 hover:bg-gray-50 transition-colors">
                  <td className="py-4 px-3">
                    <div className="flex flex-col">
                      <span className="font-bold text-gray-900">{s.paciente}</span>
                      <span className="text-xs text-gray-500">Solicitado por: Dr. {s.medico}</span>
                    </div>
                  </td>
                  <td className="py-4 px-3">
                    <div className="flex flex-wrap gap-2 max-w-[200px]">
                      {Object.entries(
                        (s.itens_exame || []).reduce((acc, i) => {
                          acc[i.status] = (acc[i.status] || 0) + 1;
                          return acc;
                        }, {})
                      ).sort(([statusA], [statusB]) => {
                        const order = { 'Pendente': 1, 'Coletado': 2, 'Em Análise': 3, 'Concluído': 4 };
                        return (order[statusA] || 5) - (order[statusB] || 5);
                      }).map(([status, count]) => (
                        <div key={status} className="flex items-center gap-1">
                          <span className="font-medium text-xs">{count}x</span>
                          <StatusBadge status={status} />
                        </div>
                      ))}
                      {(s.itens_exame || []).length === 0 && <span className="text-gray-400 italic text-xs">Sem itens</span>}
                    </div>
                  </td>
                  <td className="py-4 px-3">
                    <p className="text-gray-600 line-clamp-2 max-w-[250px]" title={s.justificativa}>
                      {s.justificativa}
                    </p>
                  </td>
                  <td className="py-4 px-3 text-center">
                    <PriorityBadge priority={s.prioridade} />
                  </td>
                  <td className="py-4 px-3 text-center">
                    <div className="flex justify-center gap-1">
                      <button
                        className="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded"
                        title="Editar"
                        onClick={() => openEdit(s)}
                      >
                        <Pencil className="h-4 w-4" />
                      </button>
                      <button
                        className="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded"
                        title="Excluir"
                        onClick={() => handleDelete(s.id)}
                      >
                        <Trash2 className="h-4 w-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </Card>
    </AppLayout>
  );
}
