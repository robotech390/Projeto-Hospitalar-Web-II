import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Link } from '@inertiajs/react';

export default function RecepcaoLayout({ children }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Recepção
                </h2>
            }
        >
            <div className="flex min-h-screen">
                <aside className="w-56 bg-teal-700 text-white flex flex-col py-6 px-4 space-y-2">
                    <p className="text-xs uppercase tracking-widest text-teal-300 mb-4 px-2">
                        Recepção
                    </p>

                    <Link
                        href={route('recepcao.agendamento')}
                        className={
                            'rounded px-3 py-2 text-sm font-medium transition-colors hover:bg-teal-600 ' +
                            (route().current('recepcao.agendamento')
                                ? 'bg-teal-600'
                                : '')
                        }
                    >
                        Agendamento
                    </Link>
                </aside>

                <main className="flex-1 bg-gray-100 p-6">{children}</main>
            </div>
        </AuthenticatedLayout>
    );
}
