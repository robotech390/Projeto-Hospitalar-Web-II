import { useState, useEffect } from 'react';
import axios from 'axios';

interface Medicamento {
  id: number;
  nome: string;
  principio_ativo: string;
  dosagem: string;
  id_tipo_medicamento: number;
  preco: number;
}

interface TipoMedicamento {
  id: number;
  descricao: string;
}

export default function Catalogo() {
  const [medicamentos, setMedicamentos] = useState<Medicamento[]>([]);
  const [tipos, setTipos] = useState<TipoMedicamento[]>([]);
  
  const [modalAberto, setModalAberto] = useState(false);
  const [carregando, setCarregando] = useState(true);

  const [novo, setNovo] = useState({
    nome: '',
    principio_ativo: '',
    dosagem: '',
    id_tipo_medicamento: '',
    preco: ''
  });
  
  const [criandoNovoTipo, setCriandoNovoTipo] = useState(false);
  const [descricaoNovoTipo, setDescricaoNovoTipo] = useState('');

  const buscarDados = () => {
    Promise.all([
      axios.get('http://localhost:8000/api/medicamentos'),
      axios.get('http://localhost:8000/api/tipos')
    ])
    .then(([resMed, resTipos]) => {
      setMedicamentos(resMed.data);
      setTipos(resTipos.data);
      setCarregando(false);
    })
    .catch(err => {
      console.error("Erro ao buscar dados do MySQL (Catálogo):", err);
      // Tratamento de erro: Força as listas a ficarem vazias
      setMedicamentos([]);
      setTipos([]);
      setCarregando(false);
    });
  };

  useEffect(() => {
    buscarDados();
  }, []);

  const handleSalvar = async (e: React.FormEvent) => {
    e.preventDefault();
    let idTipoFinal = novo.id_tipo_medicamento;

    try {
      if (criandoNovoTipo) {
        if (!descricaoNovoTipo.trim()) {
          alert("Digite a descrição do novo tipo.");
          return;
        }
        const resTipo = await axios.post('http://localhost:8000/api/tipos', { 
          descricao: descricaoNovoTipo 
        });
        idTipoFinal = resTipo.data.id; 
      } else if (!idTipoFinal) {
        alert("Selecione um tipo de medicamento.");
        return;
      }

      await axios.post('http://localhost:8000/api/medicamentos', {
        ...novo,
        id_tipo_medicamento: idTipoFinal
      });

      setModalAberto(false);
      setNovo({ nome: '', principio_ativo: '', dosagem: '', id_tipo_medicamento: '', preco: '' });
      setCriandoNovoTipo(false);
      setDescricaoNovoTipo('');
      buscarDados();

    } catch (err) {
      console.error("Erro no fluxo de salvamento:", err);
      alert("Falha ao salvar no banco de dados. O backend pode estar offline.");
    }
  };

  return (
    <div className="bg-white rounded-lg shadow-sm p-6 relative">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-semibold text-gray-700">Catálogo de Produtos</h2>
        <button 
          onClick={() => setModalAberto(true)} 
          className="bg-[var(--color-brand-primary)] text-white px-4 py-2 rounded shadow hover:bg-[var(--color-brand-dark)] transition"
        >
          + Novo Medicamento
        </button>
      </div>

      {carregando ? (
        <div className="text-center py-8 text-gray-500 font-medium">Carregando catálogo...</div>
      ) : (
        <table className="w-full text-left border-collapse">
          <thead>
            <tr className="border-b text-sm text-gray-500">
              <th className="pb-3 font-medium">ID</th>
              <th className="pb-3 font-medium">Nome Comercial</th>
              <th className="pb-3 font-medium">Princípio Ativo</th>
              <th className="pb-3 font-medium">Dosagem</th>
              <th className="pb-3 font-medium">Preço (R$)</th>
            </tr>
          </thead>
          <tbody className="text-sm">
            {medicamentos.map((m) => (
              <tr key={m.id} className="border-b hover:bg-gray-50">
                <td className="py-3 text-gray-500">#{m.id}</td>
                <td className="py-3 font-semibold text-gray-800">{m.nome}</td>
                <td className="py-3">{m.principio_ativo}</td>
                <td className="py-3">{m.dosagem}</td>
                <td className="py-3">R$ {Number(m.preco).toFixed(2)}</td>
              </tr>
            ))}
            {medicamentos.length === 0 && (
              <tr>
                <td colSpan={5} className="py-8 text-center text-gray-500">
                  Nenhum medicamento encontrado. O banco de dados está vazio ou fora do ar.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      )}

      {modalAberto && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-white p-8 rounded-lg shadow-lg w-[500px]">
            <h3 className="text-xl font-semibold mb-4 text-[var(--color-brand-dark)]">Cadastrar Medicamento</h3>
            
            <form onSubmit={handleSalvar} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Nome Comercial</label>
                <input required value={novo.nome} onChange={e => setNovo({...novo, nome: e.target.value})} className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[var(--color-brand-light)] outline-none" />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Princípio Ativo</label>
                <input required value={novo.principio_ativo} onChange={e => setNovo({...novo, principio_ativo: e.target.value})} className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[var(--color-brand-light)] outline-none" />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Dosagem</label>
                  <input required value={novo.dosagem} onChange={e => setNovo({...novo, dosagem: e.target.value})} className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[var(--color-brand-light)] outline-none" />
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Tipo de Medicamento</label>
                  {!criandoNovoTipo ? (
                    <select 
                      value={novo.id_tipo_medicamento} 
                      onChange={e => {
                        if (e.target.value === 'novo') {
                          setCriandoNovoTipo(true);
                          setNovo({...novo, id_tipo_medicamento: ''});
                        } else {
                          setNovo({...novo, id_tipo_medicamento: e.target.value});
                        }
                      }} 
                      className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[var(--color-brand-light)] outline-none bg-white"
                      required
                    >
                      <option value="">Selecione...</option>
                      {tipos.map(t => (
                        <option key={t.id} value={t.id}>{t.descricao}</option>
                      ))}
                      <option value="novo" className="font-bold text-[var(--color-brand-primary)]">
                        + Cadastrar novo tipo...
                      </option>
                    </select>
                  ) : (
                    <div className="flex">
                      <input 
                        type="text" 
                        autoFocus
                        placeholder="Ex: Pomada"
                        value={descricaoNovoTipo} 
                        onChange={e => setDescricaoNovoTipo(e.target.value)} 
                        className="w-full border rounded-l px-3 py-2 focus:ring-2 focus:ring-[var(--color-brand-light)] outline-none" 
                        required 
                      />
                      <button 
                        type="button" 
                        onClick={() => { setCriandoNovoTipo(false); setDescricaoNovoTipo(''); }}
                        className="bg-gray-200 border border-l-0 rounded-r px-2 text-gray-600 hover:bg-gray-300 transition"
                        title="Cancelar novo tipo"
                      >
                        X
                      </button>
                    </div>
                  )}
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Preço Unitário (R$)</label>
                <input type="number" step="0.01" required value={novo.preco} onChange={e => setNovo({...novo, preco: e.target.value})} className="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[var(--color-brand-light)] outline-none" />
              </div>

              <div className="flex justify-end space-x-3 mt-6">
                <button type="button" onClick={() => { setModalAberto(false); setCriandoNovoTipo(false); }} className="px-4 py-2 border rounded text-gray-600 hover:bg-gray-100 transition">Cancelar</button>
                <button type="submit" className="px-4 py-2 bg-[var(--color-brand-primary)] text-white rounded shadow hover:bg-[var(--color-brand-dark)] transition">Salvar</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}