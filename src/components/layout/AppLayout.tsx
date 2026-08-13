import { NavLink, Outlet } from "react-router-dom";
import { useAuth } from "../../context/auth";

const navItems = [
  { to: "/home", label: "Inicio" },
  { to: "/customers", label: "Clientes" },
  { to: "/pets", label: "Mascotas" },
  { to: "/appointments", label: "Citas" },
];

export default function AppLayout() {
  const { profile, signOut } = useAuth();

  return (
    <div className="d-flex vh-100">
      <nav className="d-flex flex-column bg-dark text-white p-3 sidebar">
        <div className="mb-4">
          <h5 className="fw-bold mb-0">Estética Canina</h5>
          <small className="text-secondary">Panel de administración</small>
        </div>

        <ul className="nav nav-pills flex-column gap-1">
          {navItems.map((item) => (
            <li className="nav-item" key={item.to}>
              <NavLink
                to={item.to}
                className={({ isActive }) =>
                  `nav-link text-white ${isActive ? "active" : ""}`
                }
              >
                {item.label}
              </NavLink>
            </li>
          ))}
        </ul>

        <div className="mt-auto pt-3 border-top border-secondary">
          <div className="mb-2">
            <div className="fw-semibold">{profile?.user_name}</div>
            <small className="text-secondary text-break">{profile?.email}</small>
          </div>
          <button
            type="button"
            className="btn btn-outline-light btn-sm w-100"
            onClick={signOut}
          >
            Cerrar sesión
          </button>
        </div>
      </nav>

      <main className="flex-grow-1 overflow-auto bg-light">
        <div className="container-fluid py-4 px-4">
          <Outlet />
        </div>
      </main>
    </div>
  );
}
