import { Link, Outlet } from 'react-router-dom';

export default function Layout() {
  return (
    <div className="flex h-screen bg-[var(--color-brand-bg)] font-sans">
      <aside className="w-64 bg-[var(--color-brand-dark)] text-white flex flex-col">
      <div className="p-4 border-b border-[var(--color-brand-primary)] flex justify-center items-center">
        <img 
          src="./public/logo.png" 
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
          <p className="font-semibold">Dr. Farmacêutico</p>
          <p className="text-gray-300">CRF: 12345/SC</p>
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