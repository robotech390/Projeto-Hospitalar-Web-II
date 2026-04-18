import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
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
    <AuthenticatedLayout header={header}>
      <div className="flex">
        
        <aside className="w-64 bg-teal-900 text-white min-h-screen">
          <div className="p-6">
            <h2 className="text-xl font-bold mb-8">Faturamento</h2>
            <nav className="space-y-2">
              {menuItems.map((item) => (
                <Link
                  key={item.name}
                  href={item.href}
                  className={`block px-4 py-3 rounded-lg transition-colors ${
                    item.active
                      ? 'bg-teal-700 text-white'
                      : 'text-teal-100 hover:bg-teal-800'
                  }`}
                >
                  {item.name}
                </Link>
              ))}
            </nav>
          </div>
        </aside>

        
        <main className="flex-1 p-8">
          {children}
        </main>
      </div>
    </AuthenticatedLayout>
  );
}
