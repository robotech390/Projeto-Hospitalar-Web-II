import { Link, usePage } from '@inertiajs/react';
import { CalendarDays, UserCheck, Clock, Search, Bell } from 'lucide-react';

function SidebarGroup({ label, children }) {
    return (
        <div className="mb-4">
            <span className="px-3 py-1 text-xs font-semibold uppercase tracking-widest text-teal-300/60">
                {label}
            </span>
            <div className="mt-1 space-y-0.5">{children}</div>
        </div>
    );
}

function SidebarItem({ href, icon: Icon, label }) {
    const { url } = usePage();
    const active = url.startsWith(href);

    return (
        <Link
            href={href}
            className={
                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors ' +
                (active
                    ? 'bg-white/10 text-white'
                    : 'text-teal-100/70 hover:text-white hover:bg-white/5')
            }
        >
            <Icon size={16} />
            {label}
        </Link>
    );
}

export default function RecepcaoLayout({ children }) {
    const user = usePage().props.auth.user;

    return (
        <div className="flex min-h-screen bg-gray-50">
            <aside className="w-56 flex flex-col shrink-0" style={{ backgroundColor: '#0a3732' }}>
                <div className="flex flex-col items-center px-5 py-6 border-b border-white/10">
                    <img src="/img/logo.png" alt="Logo" className="w-14 h-14 object-contain mb-2" />
                    <p className="text-white font-bold text-base tracking-wide">SAÚDE+VC</p>
                </div>

                <nav className="flex-1 px-3 pb-4">
                    <SidebarGroup label="Agendamento">
                        <SidebarItem
                            href="/recepcao/agendamento"
                            icon={CalendarDays}
                            label="Agendamento"
                        />
                    </SidebarGroup>

                    <SidebarGroup label="Em breve">
                        <SidebarItem
                            href="/recepcao/checkin"
                            icon={UserCheck}
                            label="Check-in"
                        />
                        <SidebarItem
                            href="/recepcao/fila"
                            icon={Clock}
                            label="Fila de Espera"
                        />
                    </SidebarGroup>
                </nav>

                <div className="px-5 py-4 border-t border-white/10">
                    <p className="text-white/25 text-[10px]">Saúde+Vc v1.0</p>
                </div>
            </aside>

            <div className="flex-1 flex flex-col min-w-0">
                <header className="h-14 bg-white border-b border-gray-200 flex items-center justify-between px-6 shrink-0 gap-4">
                    {/* Barra de busca */}
                    <div className="flex items-center gap-2 bg-gray-100 rounded-md px-2.5 py-1.5 w-64">
                        <Search size={14} className="text-gray-400 shrink-0" />
                        <input
                            type="text"
                            placeholder="Buscar..."
                            className="bg-transparent text-sm text-gray-600 placeholder-gray-400 outline-none border-none w-full"
                        />
                    </div>

                    {/* Direita */}
                    <div className="flex items-center gap-4">
                        {/* Sininho */}
                        <button className="relative text-gray-400 hover:text-gray-600 transition-colors">
                            <Bell size={18} />
                        </button>

                        {/* Avatar + nome */}
                        <div className="flex items-center gap-2">
                            <div className="relative">
                                <div className="w-8 h-8 rounded-full bg-white border-2 border-green-400 flex items-center justify-center shadow-sm">
                                    <span className="text-gray-500 text-xs font-semibold">
                                        {user?.name?.charAt(0).toUpperCase()}
                                    </span>
                                </div>
                                <span className="absolute bottom-0 right-0 w-2 h-2 bg-green-400 rounded-full border border-white" />
                            </div>
                            <span className="text-sm font-medium text-gray-700">{user?.name}</span>
                        </div>
                    </div>
                </header>

                <main className="flex-1 p-6 overflow-auto">
                    {children}
                </main>
            </div>
        </div>
    );
}
