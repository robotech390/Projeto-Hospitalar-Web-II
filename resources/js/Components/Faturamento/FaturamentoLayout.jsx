import { Link } from '@inertiajs/react';
import { useState } from 'react';

export default function FaturamentoLayout({ children, currentPage }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  const menuSections = [
    {
      title: 'GERAL',
      items: [
        {
          name: 'Dashboard',
          href: '/dashboard',
          active: currentPage === 'dashboard',
          icon: '▦',
        },
      ],
    },
    {
      title: 'GESTÃO',
      items: [
        {
          name: 'Acesso & Usuários',
          href: '#',
          active: false,
          icon: '◉',
        },
        {
          name: 'Recepção & Agenda',
          href: '#',
          active: false,
          icon: '▤',
        },
      ],
    },
    {
      title: 'CLÍNICO',
      items: [
        {
          name: 'Prontuário (PEP)',
          href: '#',
          active: false,
          icon: '▧',
        },
        {
          name: 'Farmácia',
          href: '#',
          active: false,
          icon: '◇',
        },
        {
          name: 'Laboratório',
          href: '#',
          active: false,
          icon: '△',
        },
      ],
    },
    {
      title: 'FINANCEIRO',
      items: [
        {
          name: 'Tipo de Cobrança',
          href: '/faturamento/tipo-cobranca',
          active: currentPage === 'tipo-cobranca',
          icon: '$',
        },
        {
          name: 'Convênios',
          href: '/faturamento/convenio',
          active: currentPage === 'convenio',
          icon: '◎',
        },
        {
          name: 'Planos',
          href: '/faturamento/plano',
          active: currentPage === 'plano',
          icon: '▣',
        },
      ],
    },
  ];

  return (
    <div className="flex min-h-screen bg-[#F4F6F8] font-sans text-slate-800">
      <aside className="hidden md:flex w-64 bg-[#103F3C] text-white flex-col shadow-xl">
        <div className="px-5 py-4 border-b border-white/10 flex justify-center">
          <img
            src="/logo-saude-vc.png"
            alt="Saúde+VC"
            className="w-24 h-auto object-contain"
          />
        </div>

        <nav className="flex-1 overflow-y-auto px-3 py-4">
          {menuSections.map((section) => (
            <div key={section.title} className="mb-3">
              <p className="px-3 mb-1.5 text-[10px] font-bold text-teal-200/80 tracking-[0.18em]">
                {section.title}
              </p>

              <ul className="space-y-1">
                {section.items.map((item) => (
                  <li key={item.name}>
                    {item.href === '#' ? (
                      <div className="flex items-center gap-3 px-3 py-1.5 rounded-xl text-sm text-teal-50/55 cursor-not-allowed">
                        <span className="w-5 text-center text-teal-200/60">
                          {item.icon}
                        </span>
                        <span>{item.name}</span>
                      </div>
                    ) : (
                      <Link
                        href={item.href}
                        className={`flex items-center gap-3 px-3 py-1.5 rounded-xl text-sm transition-all ${
                          item.active
                            ? 'bg-[#00767F] text-white shadow-md'
                            : 'text-teal-50/80 hover:bg-white/10 hover:text-white'
                        }`}
                      >
                        <span className="w-5 text-center text-teal-100">
                          {item.icon}
                        </span>
                        <span>{item.name}</span>
                      </Link>
                    )}
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </nav>

        <div className="px-3 pb-4">
          <div className="rounded-2xl bg-white/10 p-3 border border-white/10">
            <p className="text-xs text-teal-100">MedSystem v1.0</p>
            <p className="text-[11px] text-teal-200/70 mt-1">
              Sistema hospitalar
            </p>
          </div>
        </div>
      </aside>

      <div className="flex-1 flex flex-col min-w-0">
        <header className="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-5 md:px-8 shadow-sm">
          <div className="flex items-center gap-4 flex-1">
            <button
              onClick={() => setSidebarOpen(!sidebarOpen)}
              className="md:hidden w-10 h-10 rounded-xl border border-slate-200 flex items-center justify-center text-slate-700"
            >
              ☰
            </button>

            <div className="hidden sm:flex items-center bg-[#F2F5F5] rounded-xl px-4 py-2 w-full max-w-md border border-slate-100">
              <svg
                className="w-5 h-5 text-slate-400 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>

              <input
                type="text"
                placeholder="Buscar paciente, médico..."
                className="bg-transparent border-none focus:ring-0 text-sm w-full outline-none text-slate-600 placeholder:text-slate-400"
              />
            </div>
          </div>

          <div className="flex items-center gap-5">
            <button className="relative text-slate-500 hover:text-[#00767F] transition">
              <svg
                className="w-6 h-6"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
              </svg>
              <span className="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
            </button>

            <div className="flex items-center gap-3">
              <div className="text-right hidden sm:block">
                <p className="text-sm font-bold text-slate-800">Dr. Admin</p>
                <p className="text-xs text-slate-500">Administrador</p>
              </div>

              <div className="w-10 h-10 rounded-full bg-[#E6F7F7] flex items-center justify-center text-[#00767F] font-black border border-teal-100">
                A
              </div>
            </div>
          </div>
        </header>

        {sidebarOpen && (
          <div className="md:hidden bg-[#103F3C] text-white px-4 py-4">
            <div className="flex justify-center mb-4">
              <img
                src="/logo-saude-vc.png"
                alt="Saúde+VC"
                className="w-24 h-auto object-contain"
              />
            </div>

            {menuSections.map((section) => (
              <div key={section.title} className="mb-4">
                <p className="text-[11px] font-bold text-teal-200 mb-2 tracking-widest">
                  {section.title}
                </p>

                {section.items.map((item) =>
                  item.href === '#' ? null : (
                    <Link
                      key={item.name}
                      href={item.href}
                      className={`block px-3 py-2 rounded-lg text-sm mb-1 ${
                        item.active ? 'bg-[#00767F]' : 'hover:bg-white/10'
                      }`}
                    >
                      {item.name}
                    </Link>
                  )
                )}
              </div>
            ))}
          </div>
        )}

        <main className="flex-1 overflow-x-hidden overflow-y-auto p-5 md:p-6 bg-[#F4F6F8]">
          {children}
        </main>
      </div>
    </div>
  );
}