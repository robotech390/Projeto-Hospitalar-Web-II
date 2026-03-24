import { useState } from "react";
import { Card, CardContent } from "./components/ui/card";
import { Button } from "./components/ui/button";
import { Input } from "./components/ui/input";
import { Label } from "./components/ui/label";
import { Textarea } from "./components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "./components/ui/select";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger, DialogFooter, DialogClose } from "./components/ui/dialog";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "./components/ui/table";
import { Plus, Pencil, Trash2, Search, FlaskConical } from "lucide-react";
import { catalogoExames, type ExamCatalogItem } from "@/data/mockData";
import { toast } from "sonner";

const CatalogoExames = () => {
  const [exames, setExames] = useState<ExamCatalogItem[]>(catalogoExames);
  const [search, setSearch] = useState("");
  const [filterTipo, setFilterTipo] = useState("Todos");
  const [editItem, setEditItem] = useState<ExamCatalogItem | null>(null);
  const [dialogOpen, setDialogOpen] = useState(false);

  const [form, setForm] = useState({ nome: "", tipo: "Sangue", preco: "", preparo: "" });

  const filtered = exames.filter((e) => {
    const matchSearch = e.nome.toLowerCase().includes(search.toLowerCase());
    const matchTipo = filterTipo === "Todos" || e.tipo === filterTipo;
    return matchSearch && matchTipo;
  });

  const openNew = () => {
    setEditItem(null);
    setForm({ nome: "", tipo: "Sangue", preco: "", preparo: "" });
    setDialogOpen(true);
  };

  const openEdit = (item: ExamCatalogItem) => {
    setEditItem(item);
    setForm({ nome: item.nome, tipo: item.tipo, preco: String(item.preco), preparo: item.preparo });
    setDialogOpen(true);
  };

  const handleSave = () => {
    if (!form.nome || !form.preco) return;
    if (editItem) {
      setExames((prev) => prev.map((e) => (e.id === editItem.id ? { ...e, ...form, preco: Number(form.preco) } : e)));
      toast.success("Exame atualizado com sucesso!");
    } else {
      setExames((prev) => [...prev, { id: String(Date.now()), ...form, preco: Number(form.preco) }]);
      toast.success("Exame cadastrado com sucesso!");
    }
    setDialogOpen(false);
  };

  const handleDelete = (id: string) => {
    setExames((prev) => prev.filter((e) => e.id !== id));
    toast.success("Exame removido.");
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Catálogo de Exames</h1>
          <p className="text-sm text-muted-foreground">Gerencie os tipos de exames disponíveis</p>
        </div>
        <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
          <DialogTrigger asChild>
            <Button onClick={openNew} className="gap-2">
              <Plus className="h-4 w-4" /> Novo Exame
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>{editItem ? "Editar Exame" : "Novo Exame"}</DialogTitle>
            </DialogHeader>
            <div className="space-y-4 py-2">
              <div>
                <Label>Nome do Exame</Label>
                <Input value={form.nome} onChange={(e) => setForm({ ...form, nome: e.target.value })} placeholder="Ex: Hemograma Completo" />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label>Tipo</Label>
                  <Select value={form.tipo} onValueChange={(v) => setForm({ ...form, tipo: v })}>
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Sangue">Sangue</SelectItem>
                      <SelectItem value="Raio-X">Raio-X</SelectItem>
                      <SelectItem value="Imagem">Imagem</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <Label>Preço (R$)</Label>
                  <Input type="number" value={form.preco} onChange={(e) => setForm({ ...form, preco: e.target.value })} placeholder="0.00" />
                </div>
              </div>
              <div>
                <Label>Instruções de Preparo</Label>
                <Textarea value={form.preparo} onChange={(e) => setForm({ ...form, preparo: e.target.value })} placeholder="Descreva o preparo necessário..." rows={3} />
              </div>
            </div>
            <DialogFooter>
              <DialogClose asChild><Button variant="outline">Cancelar</Button></DialogClose>
              <Button onClick={handleSave}>{editItem ? "Salvar" : "Cadastrar"}</Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>

      <Card className="shadow-sm">
        <CardContent className="p-5">
          <div className="mb-4 flex flex-wrap items-center gap-3">
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input className="pl-9" placeholder="Buscar exame..." value={search} onChange={(e) => setSearch(e.target.value)} />
            </div>
            <Select value={filterTipo} onValueChange={setFilterTipo}>
              <SelectTrigger className="w-40"><SelectValue /></SelectTrigger>
              <SelectContent>
                <SelectItem value="Todos">Todos os Tipos</SelectItem>
                <SelectItem value="Sangue">Sangue</SelectItem>
                <SelectItem value="Raio-X">Raio-X</SelectItem>
                <SelectItem value="Imagem">Imagem</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Exame</TableHead>
                <TableHead>Tipo</TableHead>
                <TableHead>Preço</TableHead>
                <TableHead>Preparo</TableHead>
                <TableHead className="w-24">Ações</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {filtered.map((e) => (
                <TableRow key={e.id}>
                  <TableCell className="font-medium">
                    <div className="flex items-center gap-2">
                      <FlaskConical className="h-4 w-4 text-secondary" />
                      {e.nome}
                    </div>
                  </TableCell>
                  <TableCell>
                    <span className="rounded-md bg-muted px-2 py-0.5 text-xs font-medium">{e.tipo}</span>
                  </TableCell>
                  <TableCell className="tabular-nums">R$ {e.preco.toFixed(2)}</TableCell>
                  <TableCell className="max-w-[220px] truncate text-sm text-muted-foreground">{e.preparo}</TableCell>
                  <TableCell>
                    <div className="flex gap-1">
                      <Button size="icon" variant="ghost" onClick={() => openEdit(e)}>
                        <Pencil className="h-4 w-4" />
                      </Button>
                      <Button size="icon" variant="ghost" onClick={() => handleDelete(e.id)} className="text-destructive hover:text-destructive">
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  );
};

export default CatalogoExames;
