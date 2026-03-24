import { Outlet } from "react-router-dom";
import AppSidebar from "./AppSidebar";

const AppLayout = () => (
  <div className="flex min-h-screen">
    <AppSidebar />
    <main className="ml-60 flex-1 p-8">
      <Outlet />
    </main>
  </div>
);

export default AppLayout;
