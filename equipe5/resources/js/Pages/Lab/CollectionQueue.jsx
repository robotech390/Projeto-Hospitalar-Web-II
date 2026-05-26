

import { Card, CardContent } from './components/Card';
import Button from './components/Button';
import StatusBadge from './components/StatusBadge';
import { CheckCircle, Search } from 'lucide-react';
import AppLayout from './components/AppLayout';
import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import Input from './components/Input';
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectItem,
  SelectValue
} from './components/Select';


export default function CollectionQueue({ orders = [] }) {

  const [search, setSearch] = useState('');
  const [filterTipo, setFilterTipo] = useState('Todos');

  const filtered = (orders || []).filter((o) => {
    const matchSearch = (o.paciente || '').toLowerCase().includes(search.toLowerCase()) ||
                        (o.exame || '').toLowerCase().includes(search.toLowerCase());
    const matchTipo = filterTipo === 'Todos' || o.tipo === filterTipo;
    return matchSearch && matchTipo;
  });

  return (
    <AppLayout>
      <div>
        <h1 className="text-2xl font-bold">Fila de Coleta</h1>
        <p className="text-sm text-gray-500">Pacientes aguardando coleta ou realização de exame</p>
      </div>
      <div className="flex flex-wrap items-center gap-3 my-4">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
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
      <div className="space-y-3 mt-4">
        {filtered.length === 0 && (
          <Card className="shadow-sm">
            <CardContent className="flex flex-col items-center justify-center py-12 text-gray-400">
              <CheckCircle className="mb-2 h-10 w-10" />
              <p className="text-sm font-medium">Nenhum paciente na fila</p>
            </CardContent>
          </Card>
        )}
        {filtered.map((o) => (
          <Card key={o.id} className="shadow-sm transition-shadow hover:shadow-md">
            <CardContent className="flex items-center gap-4 p-5">
              <div className="flex-1 min-w-0">
                <p className="text-sm font-semibold">{o.paciente}</p>
                <p className="text-xs text-gray-500">{o.medico} · {o.exame}</p>
                <p className="text-xs text-gray-500">Tipo: {o.tipo} · Solicitado: {o.dataSolicitacao}</p>
              </div>
              <span className="text-sm tabular-nums text-muted-foreground">{o.horario}</span>
              <StatusBadge status={o.status} />
              {/* Botões de ação removidos pois não há lógica local */}
            </CardContent>
          </Card>
        ))}
      </div>
    </AppLayout>
  );
}
