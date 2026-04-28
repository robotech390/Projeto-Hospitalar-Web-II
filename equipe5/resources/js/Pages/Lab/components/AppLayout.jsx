import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { FlaskConical, ClipboardList, Upload, BarChart3, LayoutDashboard } from 'lucide-react';


function SidebarGroup({ label, children }) {
  return (
    <div className="mb-2">
      <span className="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-secondary-foreground/60">{label}</span>
      {children}
    </div>
  );
}

function SidebarItem({ href, icon, label, url }) {
  const active = url.startsWith(href);
  return (
    <Link
      href={href}
      className={`flex items-center gap-3 px-4 py-2 rounded-lg text-sm font-medium transition-colors
        ${active ? 'bg-secondary/20 text-secondary-foreground' : 'text-secondary-foreground/80 hover:bg-secondary/10'}
      `}
    >
      {icon}
      {label}
    </Link>
  );
}

export default function AppLayout({ children }) {
  const { url } = usePage();
  return (
    <div className="min-h-screen flex bg-slate-50">
      {/* Sidebar */}
      <aside className="hidden md:flex flex-col w-64 bg-primary border-r border-gray-200 shadow-sm">
        <div className="h-16 flex items-center justify-start px-6 font-bold text-xl tracking-tight text-secondary-foreground">
          Saúde + Você
        </div>
        <nav className="flex-1 py-6 px-2 space-y-1">
          <SidebarGroup label="Geral">
            <SidebarItem
              href="/lab/dashboard"
              icon={<LayoutDashboard className="h-5 w-5" />}
              label="Dashboard"
              url={url}
            />
          </SidebarGroup>
          <SidebarGroup label="Laboratório">
            <SidebarItem
              href="/lab/exams"
              icon={<FlaskConical className="h-5 w-5" />}
              label="Catálogo de Exames"
              url={url}
            />
            <SidebarItem
              href="/lab/solicitations"
              icon={<ClipboardList className="h-5 w-5" />}
              label="Solicitações de Exame"
              url={url}
            />
            <SidebarItem
              href="/lab/collection-queue"
              icon={<ClipboardList className="h-5 w-5" />}
              label="Fila de Coleta"
              url={url}
            />
            <SidebarItem
              href="/lab/result-entry"
              icon={<Upload className="h-5 w-5" />}
              label="Lançar Resultados"
              url={url}
            />
            <SidebarItem
              href="/lab/exam-status"
              icon={<BarChart3 className="h-5 w-5" />}
              label="Status dos Exames"
              url={url}
            />
          </SidebarGroup>
        </nav>
      </aside>
      {/* Main content */}
      <main className="flex-1 flex flex-col min-w-0">
        {/* Topbar (mobile) */}
        <div className="md:hidden flex items-center h-14 px-4 bg-white border-b border-gray-200 shadow-sm font-bold text-lg">
          Saúde + Você
        </div>
        <div className="flex-1 flex flex-col items-stretch">
          <div className="w-full mx-auto flex-1 flex flex-col px-8 py-8">
            {children}
          </div>
        </div>
      </main>
    </div>
  );
}
