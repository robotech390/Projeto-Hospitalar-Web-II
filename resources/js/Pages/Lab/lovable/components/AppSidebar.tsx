import { NavLink, useLocation } from "react-router-dom";
import { FlaskConical, ClipboardList, Upload, BarChart3, LayoutDashboard } from "lucide-react";

const navSections = [
  {
    label: "GERAL",
    items: [
      { to: "/", icon: LayoutDashboard, label: "Dashboard" },
    ],
  },
  {
    label: "LABORATÓRIO",
    items: [
      { to: "/catalogo", icon: FlaskConical, label: "Catálogo de Exames" },
      { to: "/fila-coleta", icon: ClipboardList, label: "Fila de Coleta" },
      { to: "/resultados", icon: Upload, label: "Lançar Resultados" },
      { to: "/status", icon: BarChart3, label: "Status dos Exames" },
    ],
  },
];

const AppSidebar = () => {
  const location = useLocation();

  return (
    <aside className="fixed left-0 top-0 z-40 flex h-screen w-60 flex-col bg-sidebar text-sidebar-foreground">
      <div className="flex h-16 items-center gap-2 px-5">
        <FlaskConical className="h-6 w-6 text-sidebar-primary" />
        <span className="text-lg font-bold tracking-tight text-sidebar-primary-foreground">
          SAÚDE<span className="text-sidebar-primary">+</span>LAB
        </span>
      </div>

      <nav className="flex-1 space-y-6 overflow-y-auto px-3 py-4">
        {navSections.map((section) => (
          <div key={section.label}>
            <p className="mb-2 px-3 text-[11px] font-semibold uppercase tracking-widest text-sidebar-muted">
              {section.label}
            </p>
            <ul className="space-y-0.5">
              {section.items.map((item) => {
                const isActive = location.pathname === item.to;
                return (
                  <li key={item.to}>
                    <NavLink
                      to={item.to}
                      className={`flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors ${
                        isActive
                          ? "bg-sidebar-accent text-sidebar-accent-foreground"
                          : "text-sidebar-foreground hover:bg-sidebar-accent/50"
                      }`}
                    >
                      <item.icon className="h-[18px] w-[18px]" />
                      {item.label}
                    </NavLink>
                  </li>
                );
              })}
            </ul>
          </div>
        ))}
      </nav>

      <div className="border-t border-sidebar-border px-5 py-3">
        <p className="text-xs text-sidebar-muted">LabSystem v1.0</p>
        <p className="text-[10px] text-sidebar-muted">© 2026 Saúde+VC</p>
      </div>
    </aside>
  );
};

export default AppSidebar;
