import { 
  Search, 
  LayoutDashboard, 
  Users, 
  CalendarDays, 
  FileText, 
  Pill, 
  TestTube, 
  DollarSign 
} from 'lucide-react';
import logoImg from '../assets/logo.png';

// Adicionamos o "activePage" para saber qual botão acender
export default function Layout({ children, activePage }) {
  return (
    <div className="flex h-screen bg-background font-sans text-gray-800">
      
      {/* SIDEBAR */}
      <aside className="w-64 bg-brand text-white flex flex-col">
        {/* Logo */}
        <div className="p-6 flex items-center justify-center border-b border-white/10">
          <img src={logoImg} alt="Logo SAÚDE+VC" className="w-32 object-contain" />
        </div>

        {/* Menu Items */}
        <nav className="flex-1 px-4 space-y-1 overflow-y-auto">
          <div className="text-xs font-semibold text-brand-light mb-2 mt-4">GERAL</div>
          <a href="../" className={`flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors ${activePage === 'dashboard' ? 'bg-brand-light text-white font-bold' : 'text-white/70 hover:bg-white/5'}`}>
            <LayoutDashboard size={18} />
            <span>Dashboard</span>
          </a>

          <div className="text-xs font-semibold text-brand-light mb-2 mt-6">DESTINY</div>
          <a href="#" className={`flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors ${activePage === 'usuarios' ? 'bg-brand-light text-white font-bold' : 'text-white/70 hover:bg-white/5'}`}>
            <Users size={18} />
            <span>Acesso & Usuários</span>
          </a>
          <a href="#" className={`flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors ${activePage === 'agenda' ? 'bg-brand-light text-white font-bold' : 'text-white/70 hover:bg-white/5'}`}>
            <CalendarDays size={18} />
            <span>Recepção & Agenda</span>
          </a>

          <div className="text-xs font-semibold text-brand-light mb-2 mt-6">CLÍNICO</div>
          <a href="prontuario" className={`flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors ${activePage === 'prontuario' ? 'bg-brand-light text-white font-bold' : 'text-white/70 hover:bg-white/5'}`}>
            <FileText size={18} />
            <span>Prontuário (PEP)</span>
          </a>
          <a href="#" className={`flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors ${activePage === 'farmacia' ? 'bg-brand-light text-white font-bold' : 'text-white/70 hover:bg-white/5'}`}>
            <Pill size={18} />
            <span>Farmácia</span>
          </a>
          <a href="#" className={`flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors ${activePage === 'laboratorio' ? 'bg-brand-light text-white font-bold' : 'text-white/70 hover:bg-white/5'}`}>
            <TestTube size={18} />
            <span>Laboratório</span>
          </a>

          <div className="text-xs font-semibold text-brand-light mb-2 mt-6">FINANCEIRO</div>
          <a href="#" className={`flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors ${activePage === 'faturamento' ? 'bg-brand-light text-white font-bold' : 'text-white/70 hover:bg-white/5'}`}>
            <DollarSign size={18} />
            <span>Faturamento</span>
          </a>
        </nav>
      </aside>

      {/* MAIN CONTENT AREA */}
      <main className="flex-1 flex flex-col overflow-hidden">
        
        {/* HEADER */}
        <header className="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
          <div className="flex items-center bg-gray-100 px-3 py-2 rounded-lg w-96">
            <Search size={18} className="text-gray-400 mr-2" />
            <input 
              type="text" 
              placeholder="Buscar paciente, médico..." 
              className="bg-transparent border-none outline-none w-full text-sm placeholder-gray-400"
            />
          </div>

          <div className="flex items-center space-x-4">
            <div className="text-right mr-2">
              <p className="text-sm font-bold text-gray-700">Dr. Admin</p>
              <p className="text-xs text-gray-500">Administrador</p>
            </div>
            <div className="w-10 h-10 bg-brand-light rounded-full text-white flex items-center justify-center font-bold">
              DA
            </div>
          </div>
        </header>

        {/* PAGE CONTENT */}
        <div className="flex-1 overflow-auto p-8">
          {children}
        </div>
      </main>
    </div>
  );
}