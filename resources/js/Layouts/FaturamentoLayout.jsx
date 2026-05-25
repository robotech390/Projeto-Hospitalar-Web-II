import { Link } from '@inertiajs/react';
import { useState } from 'react';

export default function FaturamentoLayout({ header, children, currentPage }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const menuItems = [
    { name: 'Tipo de Cobrança', href: route('faturamento.tipo-cobranca'), active: currentPage === 'tipo-cobranca' },
    { name: 'Convênio', href: route('faturamento.convenio'), active: currentPage === 'convenio' },
    { name: 'Plano', href: route('faturamento.plano'), active: currentPage === 'plano' },
  ];

  return (
    <div className="flex h-screen bg-gray-50 font-sans">
      <aside className="w-64 bg-[#00767F] text-white flex flex-col shadow-lg">
        <div className="p-6 flex items-center gap-3 border-b border-[#00989F]">
          <div className="w-8 h-8 bg-white rounded-md flex items-center justify-center text-[#00767F] font-bold">
            +
          </div>
          <span className="font-bold text-xl tracking-wide">SAÚDE+VC</span>
        </div>

        <nav className="flex-1 overflow-y-auto py-4">
          <div className="px-6 mb-2 text-xs font-semibold text-teal-200 tracking-wider">
            FINANCEIRO
          </div>
          
          <ul className="space-y-1">
            {menuItems.map((item) => (
              <li key={item.name}>
                <Link
                  href={item.href}
                  className={`flex items-center px-6 py-3 text-sm transition-colors ${
                    item.active
                      ? 'bg-[#00989F] border-l-4 border-white'
                      : 'hover:bg-[#008D94] border-l-4 border-transparent'
                  }`}
                >
                  {item.name}
                </Link>
              </li>
            ))}
          </ul>
        </nav>
      </aside>

      <div className="flex-1 flex flex-col overflow-hidden">
        <header className="h-16 bg-white flex items-center justify-between px-8 border-b border-gray-200">
          <div className="flex items-center bg-gray-100 rounded-lg px-4 py-2 w-96">
            <svg className="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
              type="text" 
              placeholder="Buscar paciente, médico..." 
              className="bg-transparent border-none focus:ring-0 text-sm w-full outline-none text-gray-700"
            />
          </div>

          <div className="flex items-center gap-3">
            <div className="text-right">
              <p className="text-sm font-bold text-gray-800">Dr. Admin</p>
              <p className="text-xs text-gray-500">Administrador</p>
            </div>
            <div className="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-[#00767F] font-bold border border-teal-200">
              AD
            </div>
          </div>
        </header>

        <main className="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">
          {children}
        </main>
      </div>
    </div>
  );
}