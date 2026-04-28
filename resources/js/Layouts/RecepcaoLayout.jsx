import { Link, usePage } from '@inertiajs/react';
import { CalendarDays, UserCheck, Clock } from 'lucide-react';

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
            <aside className="w-56 bg-teal-900 flex flex-col shrink-0">
                <div className="px-5 py-6">
                    <p className="text-white font-semibold text-sm leading-none">Recepção</p>
                    <p className="text-teal-400/60 text-[10px] mt-1">Módulo de atendimento</p>
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
                <header className="h-14 bg-white border-b border-gray-200 flex items-center justify-between px-6 shrink-0">
                    <span className="text-sm font-medium text-gray-700">Recepção</span>
                    <span className="text-sm text-gray-500">{user?.name}</span>
                </header>

                <main className="flex-1 p-6 overflow-auto">
                    {children}
                </main>
            </div>
        </div>
    );
}
