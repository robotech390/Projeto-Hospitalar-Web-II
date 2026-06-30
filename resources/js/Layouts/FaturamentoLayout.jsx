// import { Link } from '@inertiajs/react';

// export default function FaturamentoLayout({ children, currentPage }) {
//   const menuItems = [
//     {
//       name: 'Dashboard',
//       href: '/faturamento/dashboard',
//       active: currentPage === 'dashboard',
//       icon: (
//         <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//           <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h6v6H4V6zm10 0h6v6h-6V6zM4 16h6v4H4v-4zm10-4h6v8h-6v-8z" />
//         </svg>
//       ),
//     },
//     {
//       name: 'Conta Hospitalar',
//       href: '/faturamento/conta-hospitalar',
//       active: currentPage === 'conta-hospitalar',
//       icon: (
//         <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//           <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 14h6m-6 4h6M7 4h10a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V6a2 2 0 012-2z" />
//         </svg>
//       ),
//     },
//     {
//       name: 'Faturamento',
//       href: '/faturamento/tipo-cobranca',
//       active: currentPage === 'tipo-cobranca',
//       icon: (
//         <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//           <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
//         </svg>
//       ),
//     },
//     {
//       name: 'Convênios',
//       href: '/faturamento/convenio',
//       active: currentPage === 'convenio',
//       icon: (
//         <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//           <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m6-14h6m-6 4h6m-6 4h6" />
//         </svg>
//       ),
//     },
//     {
//       name: 'Planos',
//       href: '/faturamento/plano',
//       active: currentPage === 'plano',
//       icon: (
//         <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//           <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
//         </svg>
//       ),
//     },
//   ];

//   return (
//     <div className="flex h-screen bg-[#eef5f3] font-sans text-slate-700">
//       <aside className="w-64 bg-[#005f5f] text-white flex flex-col shadow-lg">
//         <div className="px-6 py-6 border-b border-white/10 flex justify-center">
//           <div className="w-32 h-28 rounded-xl overflow-hidden bg-white/5 flex items-center justify-center">
//             <img
//               src="/logo-saude-vc.png"
//               alt="Saúde VC"
//               className="w-full h-full object-contain"
//               onError={(e) => {
//                 e.currentTarget.style.display = 'none';
//                 e.currentTarget.parentElement.innerHTML =
//                   '<div style="color:white;text-align:center;font-weight:700;font-size:24px;line-height:1.1">Saúde+<div style="font-size:12px;font-weight:400;margin-top:6px">Sistema Hospitalar</div></div>';
//               }}
//             />
//           </div>
//         </div>

//         <nav className="flex-1 overflow-y-auto px-3 py-5 space-y-1">
//           <div className="px-3 mb-3 text-xs font-semibold text-teal-200 tracking-wider uppercase">
//             Grupo 6
//           </div>

//           {menuItems.map((item) => (
//             <Link
//               key={item.name}
//               href={item.href}
//               className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition ${
//                 item.active
//                   ? 'bg-white text-[#006b6b] shadow'
//                   : 'text-teal-50 hover:bg-white/10'
//               }`}
//             >
//               {item.icon}
//               <span>{item.name}</span>
//             </Link>
//           ))}
//         </nav>

//         <div className="px-6 py-4 text-xs text-teal-100 border-t border-white/10">
//           Faturamento e Convênios
//         </div>
//       </aside>

//       <div className="flex-1 flex flex-col overflow-hidden">
//         <header className="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8">
//           <div className="relative w-96">
//             <svg
//               className="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"
//               fill="none"
//               stroke="currentColor"
//               viewBox="0 0 24 24"
//             >
//               <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
//             </svg>

//             <input
//               type="text"
//               placeholder="Buscar paciente, fatura ou convênio..."
//               className="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-100 border border-transparent focus:border-[#008080] focus:ring-0 text-sm"
//             />
//           </div>

//           <div className="flex items-center gap-4">
//             <button className="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
//               <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
//                 <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 01-6 0m6 0H9" />
//               </svg>
//             </button>

//             <div className="flex items-center gap-3">
//               <div className="w-10 h-10 rounded-full bg-[#007f7f] text-white flex items-center justify-center font-semibold">
//                 A
//               </div>

//               <div>
//                 <div className="text-sm font-semibold text-slate-800">
//                   Dr. Admin
//                 </div>

//                 <div className="text-xs text-slate-500">
//                   Administrador
//                 </div>
//               </div>
//             </div>
//           </div>
//         </header>

//         <main className="flex-1 overflow-x-hidden overflow-y-auto bg-[#eef5f3] p-8">
//           {children}
//         </main>
//       </div>
//     </div>
//   );
// }

import { Link } from '@inertiajs/react';

export default function FaturamentoLayout({ children, currentPage }) {
  const menuItems = [
    {
      name: 'Dashboard',
      href: '/faturamento/dashboard',
      active: currentPage === 'dashboard',
      icon: (
        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h6v6H4V6zm10 0h6v6h-6V6zM4 16h6v4H4v-4zm10-4h6v8h-6v-8z" />
        </svg>
      ),
    },
    {
      name: 'Conta Hospitalar',
      href: '/faturamento/conta-hospitalar',
      active: currentPage === 'conta-hospitalar',
      icon: (
        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 14h6m-6 4h6M7 4h10a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V6a2 2 0 012-2z" />
        </svg>
      ),
    },
    {
      name: 'Faturamento',
      href: '/faturamento/tipo-cobranca',
      active: currentPage === 'tipo-cobranca',
      icon: (
        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      ),
    },
    {
      name: 'Convênios',
      href: '/faturamento/convenio',
      active: currentPage === 'convenio',
      icon: (
        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m6-14h6m-6 4h6m-6 4h6" />
        </svg>
      ),
    },
    {
      name: 'Planos',
      href: '/faturamento/plano',
      active: currentPage === 'plano',
      icon: (
        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      ),
    },
  ];

  return (
    <div className="flex h-screen bg-[#eef5f3] font-sans text-slate-700">
      <aside className="w-64 bg-[#005f5f] text-white flex flex-col shadow-lg">
        <div className="px-6 py-6 border-b border-white/10 flex justify-center">
          <div className="w-32 h-28 rounded-xl overflow-hidden bg-white/5 flex items-center justify-center">
            <img
              src="/logo-saude-vc.png"
              alt="Saúde VC"
              className="w-full h-full object-contain"
              onError={(e) => {
                e.currentTarget.style.display = 'none';
                e.currentTarget.parentElement.innerHTML =
                  '<div style="color:white;text-align:center;font-weight:700;font-size:24px;line-height:1.1">Saúde+<div style="font-size:12px;font-weight:400;margin-top:6px">Sistema Hospitalar</div></div>';
              }}
            />
          </div>
        </div>

        <nav className="flex-1 overflow-y-auto px-3 py-5 space-y-1">
          <div className="px-3 mb-3 text-xs font-semibold text-teal-200 tracking-wider uppercase">
            Grupo 6
          </div>

          {menuItems.map((item) => (
            <Link
              key={item.name}
              href={item.href}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition ${
                item.active
                  ? 'bg-white text-[#006b6b] shadow'
                  : 'text-teal-50 hover:bg-white/10'
              }`}
            >
              {item.icon}
              <span>{item.name}</span>
            </Link>
          ))}
        </nav>

        <div className="px-6 py-4 text-xs text-teal-100 border-t border-white/10">
          Faturamento e Convênios
        </div>
      </aside>

      <div className="flex-1 flex flex-col overflow-hidden">
        <header className="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8">
          <div className="relative w-96">
            <svg
              className="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>

            <input
              type="text"
              placeholder="Buscar paciente, fatura ou convênio..."
              className="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-100 border border-transparent focus:border-[#008080] focus:ring-0 text-sm"
            />
          </div>

          <div className="flex items-center gap-4">
            <button className="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 01-6 0m6 0H9" />
              </svg>
            </button>

            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-full bg-[#007f7f] text-white flex items-center justify-center font-semibold">
                A
              </div>

              <div>
                <div className="text-sm font-semibold text-slate-800">
                  Dr. Admin
                </div>

                <div className="text-xs text-slate-500">
                  Administrador
                </div>
              </div>
            </div>
          </div>
        </header>

        <main className="flex-1 overflow-x-hidden overflow-y-auto bg-[#eef5f3] p-8">
          {children}
        </main>
      </div>
    </div>
  );
}