import React, { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { FlaskConical, ClipboardList, Upload, BarChart3, LayoutDashboard, ListChecks, Menu, X } from 'lucide-react';

function SidebarGroup({ label, children }) {
  return (
    <div className="mb-2">
      <span className="px-4 py-2 text-xs font-semibold uppercase tracking-wider text-secondary-foreground/60">{label}</span>
      {children}
    </div>
  );
}

function SidebarItem({ href, icon, label, url, onClick }) {
  const active = url.startsWith(href);
  return (
    <Link
      href={href}
      onClick={onClick}
      className={`flex items-center gap-3 px-4 py-2 rounded-lg text-sm font-medium transition-colors
        ${active ? 'bg-secondary/20 text-secondary-foreground' : 'text-secondary-foreground/80 hover:bg-secondary/10'}
      `}
    >
      {icon}
      {label}
    </Link>
  );
}

function SidebarContent({ url, onItemClick }) {
  return (
    <nav className="flex-1 py-6 px-2 space-y-1">
      <SidebarGroup label="Geral">
        <SidebarItem
          href="/lab/dashboard"
          icon={<LayoutDashboard className="h-5 w-5" />}
          label="Dashboard"
          url={url}
          onClick={onItemClick}
        />
      </SidebarGroup>
      <SidebarGroup label="Laboratório">
        <SidebarItem
          href="/lab/exams"
          icon={<FlaskConical className="h-5 w-5" />}
          label="Catálogo de Exames"
          url={url}
          onClick={onItemClick}
        />
        <SidebarItem
          href="/lab/solicitations"
          icon={<ClipboardList className="h-5 w-5" />}
          label="Solicitações de Exame"
          url={url}
          onClick={onItemClick}
        />
        <SidebarItem
          href="/lab/collection-queue"
          icon={<ListChecks className="h-5 w-5" />}
          label="Fila de Coleta"
          url={url}
          onClick={onItemClick}
        />
        <SidebarItem
          href="/lab/result-entry"
          icon={<Upload className="h-5 w-5" />}
          label="Lançar Resultados"
          url={url}
          onClick={onItemClick}
        />
        <SidebarItem
          href="/lab/exam-status"
          icon={<BarChart3 className="h-5 w-5" />}
          label="Status dos Exames"
          url={url}
          onClick={onItemClick}
        />
      </SidebarGroup>
    </nav>
  );
}

export default function AppLayout({ children }) {
  const { url } = usePage();
  const [isMobileOpen, setIsMobileOpen] = useState(false);

  return (
    <div className="min-h-screen flex bg-slate-50 relative overflow-x-hidden">
      {/* Desktop Sidebar */}
      <aside className="hidden md:flex flex-col w-64 bg-primary border-r border-gray-200 shadow-sm flex-shrink-0">
        <div className="h-16 flex items-center justify-start px-6 font-bold text-xl tracking-tight text-secondary-foreground">
          Saúde + Você
        </div>
        <SidebarContent url={url} />
      </aside>

      {/* Mobile Sidebar Overlay (Backdrop) */}
      <div 
        className={`fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm md:hidden transition-opacity duration-300 ease-in-out ${
          isMobileOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'
        }`}
        onClick={() => setIsMobileOpen(false)}
      />

      {/* Mobile Sidebar Drawer */}
      <aside 
        className={`fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-primary border-r border-gray-200 shadow-lg transform transition-transform duration-300 ease-in-out md:hidden ${
          isMobileOpen ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        <div className="h-16 flex items-center justify-between px-6 font-bold text-xl tracking-tight text-secondary-foreground border-b border-gray-700/20">
          <span>Saúde + Você</span>
          <button 
            onClick={() => setIsMobileOpen(false)}
            className="p-1.5 rounded-lg text-secondary-foreground/80 hover:bg-secondary/20 hover:text-secondary-foreground transition-colors"
          >
            <X className="h-5 w-5" />
          </button>
        </div>
        <SidebarContent url={url} onItemClick={() => setIsMobileOpen(false)} />
      </aside>

      {/* Main content */}
      <main className="flex-1 flex flex-col min-w-0">
        {/* Topbar (mobile) */}
        <div className="md:hidden flex items-center gap-3 h-14 px-4 bg-white border-b border-gray-200 shadow-sm font-bold text-lg">
          <button 
            onClick={() => setIsMobileOpen(true)}
            className="p-1.5 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20"
            aria-label="Abrir menu"
          >
            <Menu className="h-6 w-6" />
          </button>
          <span className="text-gray-900">Saúde + Você</span>
        </div>
        <div className="flex-1 flex flex-col items-stretch">
          <div className="w-full mx-auto flex-1 flex flex-col px-4 sm:px-8 py-8">
            {children}
          </div>
        </div>
      </main>
    </div>
  );
}
