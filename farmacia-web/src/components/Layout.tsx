import { Link, Outlet } from 'react-router-dom';
import { useEffect, useState } from 'react';
import axios from 'axios';

export default function Layout() {
  // O sistema começa no estado de 'conectando' (bloqueado)
  const [statusConexao, setStatusConexao] = useState<'conectando' | 'conectado' | 'erro'>('conectando');
  const [usuario, setUsuario] = useState({ nome: '', funcao: '' });

  useEffect(() => {
    const validarAcesso = async () => {
      try {
        // Bate na API da Equipe 1 sem tela de login
        const res = await axios.post('https://projeto-hospitalar-web-ii-production.up.railway.app/api/auth/login', {
          email: 'equipe4@hospital.com',
          senha: 'Equipe4@Farmacia'
        });
        
        // Se a resposta for 200 OK, libera o acesso
        if (res.status === 200) {
          localStorage.setItem('tokenFarmacia', res.data.token);
          setUsuario(res.data.usuario);
          setStatusConexao('conectado');
        } else {
          setStatusConexao('erro');
        }
      } catch (err) {
        // Se der 401 (senha errada), 500 ou CORS, bloqueia o sistema
        console.error("Falha ao autenticar com a API central:", err);
        setStatusConexao('erro');
      }
    };

    validarAcesso();
  }, []);

  // TELA 1: Bloqueio durante o carregamento (Esperando o 200)
  if (statusConexao === 'conectando') {
    return (
      <div className="h-screen w-full flex flex-col items-center justify-center bg-[var(--color-brand-bg)]">
        <div className="animate-spin rounded-full h-12 w-12 border-b-4 border-[var(--color-brand-dark)] mb-4"></div>
        <h2 className="text-xl font-bold text-[var(--color-brand-dark)] mb-2">Validando Credenciais</h2>
        <p className="text-gray-500 font-medium">Aguardando resposta do Servidor Central (Equipe 1)...</p>
      </div>
    );
  }

  // TELA 2: Bloqueio Total (Se a API cair ou der erro de senha)
  if (statusConexao === 'erro') {
    return (
      <div className="h-screen w-full flex flex-col items-center justify-center bg-gray-100 p-4">
        <div className="bg-white p-8 rounded-lg shadow-xl max-w-md text-center border-t-4 border-red-500">
          <div className="flex justify-center mb-4">
            <svg className="h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h2 className="text-2xl font-bold text-gray-800 mb-2">Acesso Negado</h2>
          <p className="text-gray-600 mb-6 leading-relaxed">
            O servidor da Equipe 1 recusou a conexão ou está offline. O sistema de farmácia não pode ser acessado sem autenticação central.
          </p>
          <button 
            onClick={() => window.location.reload()} 
            className="w-full bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700 transition font-bold shadow-lg"
          >
            TENTAR RECONECTAR
          </button>
        </div>
      </div>
    );
  }

  // TELA 3: Sistema Liberado (Se chegou aqui, veio o 200 OK)
  return (
    <div className="flex h-screen bg-[var(--color-brand-bg)] font-sans">
      <aside className="w-64 bg-[var(--color-brand-dark)] text-white flex flex-col shadow-lg">
        
        <div className="p-4 border-b border-[var(--color-brand-primary)] flex justify-center items-center">
          <img 
            src="/logo.png" 
            alt="Logo Farmácia" 
            className="w-32 h-auto object-contain drop-shadow-md"
          />
        </div>
        
        <nav className="flex-1 p-4 space-y-2">
          <div className="text-xs text-[var(--color-brand-light)] font-semibold mb-4 mt-2">
            MÓDULO FARMÁCIA
          </div>
          
          <Link to="/" className="block w-full text-left p-3 rounded hover:bg-[var(--color-brand-primary)] transition font-medium">
            Dashboard
          </Link>
          
          <Link to="/catalogo" className="block w-full text-left p-3 rounded hover:bg-[var(--color-brand-primary)] transition">
            Catálogo de Produtos
          </Link>

          <Link to="/gestao-notas" className="block w-full text-left p-3 rounded hover:bg-[var(--color-brand-primary)] transition">
            Gestão de Notas
          </Link>

          <Link to="/dispensacao" className="block w-full text-left p-3 rounded hover:bg-[var(--color-brand-primary)] transition">
            Dispensação
          </Link>
        </nav>
        
        <div className="p-4 border-t border-[var(--color-brand-primary)] text-sm">
          <p className="font-bold truncate" title={usuario.nome}>{usuario.nome}</p>
          <p className="text-gray-300 capitalize text-xs">{usuario.funcao}</p>
        </div>
      </aside>

      <main className="flex-1 flex flex-col overflow-hidden">
        <header className="h-16 bg-white shadow-sm flex items-center justify-between px-8">
          <h2 className="text-xl font-semibold text-gray-700">Módulo de Farmácia</h2>
          <div className="flex items-center">
             <input 
               type="text" 
               placeholder="Buscar sistema..." 
               className="border rounded-full px-4 py-2 w-72 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-brand-light)]"
             />
          </div>
        </header>
        <div className="p-8 overflow-y-auto flex-1">
          <Outlet />
        </div>
      </main>
    </div>
  );
}