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
  const [mensagemSucesso, setMensagemSucesso] = useState('');
  
  const [editandoId, setEditandoId] = useState<number | null>(null);

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
    .catch(() => {
      setMedicamentos([]);
      setTipos([]);
      setCarregando(false);
    });
  };

  useEffect(() => { buscarDados(); }, []);

  const abrirEdicao = (m: Medicamento) => {
    setEditandoId(m.id);
    setNovo({
      nome: m.nome,
      principio_ativo: m.principio_ativo,
      dosagem: m.dosagem,
      id_tipo_medicamento: m.id_tipo_medicamento.toString(),
      preco: m.preco.toString()
    });
    setModalAberto(true);
  };

  const [salvando, setSalvando] = useState(false);

  const handleSalvar = async (e: React.FormEvent) => {
    e.preventDefault();
    setSalvando(true);
  
    const principioPadronizado = novo.principio_ativo.charAt(0).toUpperCase() + novo.principio_ativo.slice(1).toLowerCase();
  
    if (!editandoId) {
      const existe = medicamentos.some(
        m => m.principio_ativo.toLowerCase() === principioPadronizado.toLowerCase()
      );
      
      if (existe) {
        const confirmar = window.confirm("Atenção: Este Princípio Ativo já está cadastrado. Deseja cadastrar novamente mesmo assim?");
        if (!confirmar) {
          setSalvando(false);
          return;
        }
      }
    }
  
    let idTipoFinal = novo.id_tipo_medicamento;
  
    try {
      if (criandoNovoTipo) {
        const resTipo = await axios.post('http://localhost:8000/api/tipos', { 
          descricao: descricaoNovoTipo 
        });
        idTipoFinal = resTipo.data.id;
      }
  
      const dadosParaEnviar = { 
        ...novo, 
        principio_ativo: principioPadronizado,
        id_tipo_medicamento: idTipoFinal 
      };
  
      if (editandoId) {
        await axios.put(`http://localhost:8000/api/medicamentos/${editandoId}`, dadosParaEnviar);
        setMensagemSucesso('Medicamento atualizado com sucesso!');
      } else {
        await axios.post('http://localhost:8000/api/medicamentos', dadosParaEnviar);
        setMensagemSucesso('Medicamento cadastrado com sucesso!');
      }
  
      setModalAberto(false);
      setNovo({ nome: '', principio_ativo: '', dosagem: '', id_tipo_medicamento: '', preco: '' });
      setEditandoId(null);
      setCriandoNovoTipo(false);
      setDescricaoNovoTipo('');
      buscarDados();
      setTimeout(() => setMensagemSucesso(''), 3000);
  
    } catch (err) {
      alert("Erro ao salvar. Verifique a conexão com o banco de dados.");
    } finally {
      setSalvando(false);
    }
  };

  return (
    <div className="bg-white rounded-lg shadow-sm p-6 relative">
      {mensagemSucesso && (
        <div className="mb-4 p-3 bg-green-100 border-l-4 border-green-500 text-green-700 font-medium">
          {mensagemSucesso}
        </div>
      )}

      <div className="flex justify-between items-center mb-6">
        <h2 className="text-2xl font-semibold text-gray-700">Catálogo de Produtos</h2>
        <button 
          onClick={() => { setEditandoId(null); setModalAberto(true); setNovo({nome:'', principio_ativo:'', dosagem:'', id_tipo_medicamento:'', preco:''}); }} 
          className="bg-[var(--color-brand-primary)] text-white px-4 py-2 rounded shadow hover:bg-[var(--color-brand-dark)] transition"
        >
          + Novo Medicamento
        </button>
      </div>

      <table className="w-full text-left border-collapse">
        <thead>
          <tr className="border-b text-sm text-gray-500">
            <th className="pb-3">ID</th>
            <th className="pb-3">Nome Comercial</th>
            <th className="pb-3">Princípio Ativo</th>
            <th className="pb-3">Dosagem</th>
            <th className="pb-3">Preço (R$)</th>
            <th className="pb-3 text-center">Ações</th>
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
              <td className="py-3 text-center">
                <button onClick={() => abrirEdicao(m)} className="text-blue-600 hover:underline font-medium">
                  Editar
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {modalAberto && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-white p-8 rounded-lg shadow-lg w-[500px]">
            <h3 className="text-xl font-semibold mb-4 text-[var(--color-brand-dark)]">
              {editandoId ? 'Editar Medicamento' : 'Cadastrar Medicamento'}
            </h3>
            
            <form onSubmit={handleSalvar} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Nome Comercial</label>
                <input required value={novo.nome} onChange={e => setNovo({...novo, nome: e.target.value})} className="w-full border rounded px-3 py-2 outline-none" />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Princípio Ativo</label>
                <input 
                  required 
                  value={novo.principio_ativo} 
                  onChange={e => setNovo({...novo, principio_ativo: e.target.value})} 
                  className={`w-full border rounded px-3 py-2 outline-none ${editandoId ? 'bg-gray-100 cursor-not-allowed' : ''}`}
                  disabled={!!editandoId}
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Dosagem</label>
                  <input required value={novo.dosagem} onChange={e => setNovo({...novo, dosagem: e.target.value})} className="w-full border rounded px-3 py-2 outline-none" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                  {!criandoNovoTipo ? (
                    <select 
                      value={novo.id_tipo_medicamento} 
                      onChange={e => e.target.value === 'novo' ? setCriandoNovoTipo(true) : setNovo({...novo, id_tipo_medicamento: e.target.value})} 
                      className={`w-full border rounded px-3 py-2 bg-white ${editandoId ? 'bg-gray-100 cursor-not-allowed' : ''}`}
                      required
                      disabled={!!editandoId}
                    >
                      <option value="">Selecione...</option>
                      {tipos.map(t => <option key={t.id} value={t.id}>{t.descricao}</option>)}
                      {!editandoId && <option value="novo" className="text-blue-600 font-bold">+ Novo tipo</option>}
                    </select>
                  ) : (
                    <input autoFocus placeholder="Novo tipo..." value={descricaoNovoTipo} onChange={e => setDescricaoNovoTipo(e.target.value)} className="w-full border rounded px-3 py-2" required />
                  )}
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Preço Unitário (R$)</label>
                <input type="number" step="0.01" required value={novo.preco} onChange={e => setNovo({...novo, preco: e.target.value})} className="w-full border rounded px-3 py-2" />
              </div>

              <div className="flex justify-end space-x-3 mt-6">
                <button type="button" onClick={() => { setModalAberto(false); setEditandoId(null); }} className="px-4 py-2 border rounded">Cancelar</button>
                
                {/* O NOVO BOTÃO FOI ADICIONADO AQUI: */}
                <button 
                  type="submit" 
                  disabled={salvando}
                  className={`px-4 py-2 text-white rounded shadow transition flex items-center justify-center min-w-[100px] ${
                    salvando ? 'bg-gray-400 cursor-not-allowed' : 'bg-[var(--color-brand-primary)] hover:bg-[var(--color-brand-dark)]'
                  }`}
                >
                  {salvando ? (
                    <>
                      <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      Salvando...
                    </>
                  ) : (
                    editandoId ? 'Atualizar' : 'Salvar'
                  )}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}