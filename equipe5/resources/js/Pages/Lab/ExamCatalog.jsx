

import { useState } from 'react';
import { usePage } from '@inertiajs/react';
import { Card, CardContent } from './components/Card';
import { FlaskConical, Pencil, Plus, Trash2 } from 'lucide-react';
import Button from './components/Button';
import clsx from 'clsx';
import AppLayout from './components/AppLayout';
function TypeBadge({ type }) {
  const color = type === 'Sangue' ? 'bg-green-100 text-green-800' :
    type === 'Raio-X' ? 'bg-gray-100 text-gray-800' :
      type === 'Imagem' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800';
  return <span className={clsx('px-2 py-0.5 rounded-full text-xs font-semibold', color)}>{type}</span>;
}
import Input from './components/Input';
import Label from './components/Label';
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue
} from './components/Select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogClose } from './components/Dialog';
import { toastSuccess } from './toast';

export default function ExamCatalog() {
  const { props } = usePage();
  const [exames, setExames] = useState([...props.catalogoExames]);
  const [search, setSearch] = useState('');
  const [filterTipo, setFilterTipo] = useState('Todos');
  const [editItem, setEditItem] = useState(null);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [form, setForm] = useState({ nome: '', tipo: 'Sangue', preco: '', preparo: '' });

  const filtered = exames.filter((e) => {
    const matchSearch = e.nome.toLowerCase().includes(search.toLowerCase());
    const matchTipo = filterTipo === 'Todos' || e.tipo === filterTipo;
    return matchSearch && matchTipo;
  });

  const openNew = () => {
    setEditItem(null);
    setForm({ nome: '', tipo: 'Sangue', preco: '', preparo: '' });
    setDialogOpen(true);
  };

  const openEdit = (item) => {
    setEditItem(item);
    setForm({ nome: item.nome, tipo: item.tipo, preco: String(item.preco), preparo: item.preparo });
    setDialogOpen(true);
  };

  const handleSave = () => {
    if (!form.nome || !form.preco) return;
    if (editItem) {
      setExames((prev) => prev.map((e) => (e.id === editItem.id ? { ...e, ...form, preco: Number(form.preco) } : e)));
      toastSuccess('Exame atualizado com sucesso!');
    } else {
      setExames((prev) => [...prev, { id: String(Date.now()), ...form, preco: Number(form.preco) }]);
      toastSuccess('Exame cadastrado com sucesso!');
    }
    setDialogOpen(false);
  };

  const handleDelete = (id) => {
    setExames((prev) => prev.filter((e) => e.id !== id));
    toastSuccess('Exame removido.');
  };

  return (
    <AppLayout>
      <div className="flex items-center justify-between mb-4">
        <div>
          <h1 className="text-2xl font-bold">Catálogo de Exames</h1>
          <p className="text-sm text-gray-500">Gerencie os tipos de exames disponíveis</p>
        </div>
        <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
          <DialogTrigger asChild>
            <Button onClick={openNew} variant="default">
              <Plus className="h-4 w-4" /> Novo Exame
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>{editItem ? 'Editar Exame' : 'Novo Exame'}</DialogTitle>
            </DialogHeader>
            <div className="space-y-4 py-2">
              <div>
                <Label>Nome do Exame</Label>
                <Input value={form.nome} onChange={e => setForm({ ...form, nome: e.target.value })} placeholder="Ex: Hemograma Completo" />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label>Tipo</Label>
                  <Select value={form.tipo} onValueChange={v => setForm({ ...form, tipo: v })}>
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione o tipo" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Sangue">Sangue</SelectItem>
                      <SelectItem value="Raio-X">Raio-X</SelectItem>
                      <SelectItem value="Imagem">Imagem</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <Label>Preço (R$)</Label>
                  <Input type="number" value={form.preco} onChange={e => setForm({ ...form, preco: e.target.value })} />
                </div>
              </div>
              <div>
                <Label>Preparo</Label>
                <Input value={form.preparo} onChange={e => setForm({ ...form, preparo: e.target.value })} placeholder="Ex: Jejum de 8 horas" />
              </div>
            </div>
            <DialogFooter>
              <Button onClick={handleSave}>{editItem ? 'Salvar' : 'Cadastrar'}</Button>
              <DialogClose setOpen={setDialogOpen}>Cancelar</DialogClose>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>
      <Card className="p-4 border rounded-xl">
        <div className="flex flex-wrap items-center gap-3 mb-4">
          <div className="relative flex-1">
            <Input className="pl-10" placeholder="Buscar exame..." value={search} onChange={e => setSearch(e.target.value)} />
            <FlaskConical className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
          </div>
          <Select value={filterTipo} onValueChange={v => setFilterTipo(v)}>
            <SelectTrigger className="w-44">
              <SelectValue placeholder="Todos os Tipos" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="Todos">Todos os Tipos</SelectItem>
              <SelectItem value="Sangue">Sangue</SelectItem>
              <SelectItem value="Raio-X">Raio-X</SelectItem>
              <SelectItem value="Imagem">Imagem</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <div className="overflow-x-auto">
          <table className="min-w-full text-sm">
            <thead>
              <tr className="border-b text-gray-500">
                <th className="py-2 px-2 text-left font-medium">Exame</th>
                <th className="py-2 px-2 text-left font-medium">Tipo</th>
                <th className="py-2 px-2 text-left font-medium">Preço</th>
                <th className="py-2 px-2 text-left font-medium">Preparo</th>
                <th className="py-2 px-2 text-center font-medium">Ações</th>
              </tr>
            </thead>
            <tbody>
              {filtered.length === 0 && (
                <tr>
                  <td colSpan={5} className="py-8 text-center text-gray-400">Nenhum exame cadastrado</td>
                </tr>
              )}
              {filtered.map((e) => (
                <tr key={e.id} className="border-b last:border-0 hover:bg-gray-50 group">
                  <td className="py-3 px-2">
                    {e.nome}
                  </td>
                  <td className="py-3 px-2"><TypeBadge type={e.tipo} /></td>
                  <td className="py-3 px-2">R$ {Number(e.preco).toFixed(2)}</td>
                  <td className="py-3 px-2">{e.preparo}</td>
                  <td className="py-3 px-2 text-center">
                    <button
                      className="inline-flex items-center p-2 text-gray-500 hover:text-primary"
                      title="Editar"
                      onClick={() => openEdit(e)}
                    >
                      <Pencil className="h-4 w-4" />
                    </button>
                    <button
                      className="inline-flex items-center p-2 text-gray-500 hover:text-red-600"
                      title="Excluir"
                      onClick={() => handleDelete(e.id)}
                    >
                      <Trash2 className="h-4 w-4" />
                    </button>
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
