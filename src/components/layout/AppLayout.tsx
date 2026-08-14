import { useEffect, useState } from "react";
import { NavLink, Outlet } from "react-router-dom";
import { useAuth } from "../../context/auth";
import { supabase } from "../../lib/supabase";

interface NavItem {
  to: string;
  label: string;
  adminOnly?: boolean;
}

interface NavGroup {
  title: string;
  items: NavItem[];
}

const navGroups: NavGroup[] = [
  {
    title: "Principal",
    items: [{ to: "/home", label: "Inicio" }],
  },
  {
    title: "Gestión",
    items: [
      { to: "/customers", label: "Clientes" },
      { to: "/pets", label: "Mascotas" },
      { to: "/appointments", label: "Citas" },
      { to: "/services", label: "Servicios" },
      { to: "/packages", label: "Paquetes" },
      { to: "/species", label: "Especies" },
      { to: "/contracts", label: "Contratos" },
    ],
  },
  {
    title: "Tienda",
    items: [
      { to: "/sales", label: "Ventas" },
      { to: "/products", label: "Productos" },
    ],
  },
  {
    title: "Finanzas",
    items: [
      { to: "/expenses", label: "Gastos" },
      { to: "/payment-methods", label: "Métodos de pago" },
    ],
  },
  {
    title: "Administración",
    items: [
      { to: "/users", label: "Usuarios", adminOnly: true },
      { to: "/settings", label: "Configuración", adminOnly: true },
    ],
  },
];

export default function AppLayout() {
  const { profile, signOut } = useAuth();
  const isAdmin = profile?.id_rol === 1;
  const [logo, setLogo] = useState<string | null>(null);

  useEffect(() => {
    const idBusiness = profile?.id_business;
    if (!idBusiness) return;
    let active = true;
    supabase
      .from("custom")
      .select("main_color, secondary_color, text_color, logo")
      .eq("id_business", idBusiness)
      .maybeSingle()
      .then(({ data }) => {
        if (!active || !data) return;
        const c = data as {
          main_color: string | null;
          secondary_color: string | null;
          text_color: string | null;
          logo: string | null;
        };
        if (c.main_color) {
          document.documentElement.style.setProperty("--brand-color", c.main_color);
        }
        if (c.secondary_color) {
          document.documentElement.style.setProperty(
            "--brand-secondary",
            c.secondary_color,
          );
        }
        if (c.text_color) {
          document.documentElement.style.setProperty("--brand-text", c.text_color);
        }
        if (c.logo) setLogo(c.logo);
      });
    return () => {
      active = false;
    };
  }, [profile]);

  return (
    <div className="d-flex vh-100">
      <nav
        className="d-flex flex-column text-white p-3 sidebar overflow-auto"
        style={{ backgroundColor: "var(--brand-color, #212529)" }}
      >
        <div className="mb-4 d-flex align-items-center gap-2">
          {logo && (
            <img
              src={logo}
              alt="Logo"
              className="rounded bg-white"
              style={{ width: 40, height: 40, objectFit: "cover" }}
            />
          )}
          <div>
            <h5 className="fw-bold mb-0">Mikato Software</h5>
            <small className="text-white-50">Estética canina</small>
          </div>
        </div>

        {navGroups.map((group) => {
          const visible = group.items.filter(
            (item) => !item.adminOnly || isAdmin,
          );
          if (visible.length === 0) return null;
          return (
            <div className="mb-3" key={group.title}>
              <div className="text-uppercase text-white-50 small fw-semibold mb-1">
                {group.title}
              </div>
              <ul className="nav nav-pills flex-column gap-1">
                {visible.map((item) => (
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
            </div>
          );
        })}

        <div className="mt-auto pt-3 border-top border-secondary">
          <div className="mb-2">
            <div className="fw-semibold">{profile?.user_name}</div>
            <small className="text-white-50 text-break">{profile?.email}</small>
          </div>
          <button
            type="button"
            className="btn btn-outline-light btn-sm w-100"
            onClick={signOut}
          >
            Cerrar sesión
          </button>
          <div className="text-white-50 small text-center mt-2">
            © {new Date().getFullYear()} Mikato Software
          </div>
        </div>
      </nav>

      <main className="flex-grow-1 overflow-auto bg-light">
        <div className="container-fluid py-4 px-4">
          {logo && (
            <div className="d-flex justify-content-center mb-4">
              <img
                src={logo}
                alt="Logo"
                style={{ height: 64, maxWidth: "100%", objectFit: "contain" }}
              />
            </div>
          )}
          <Outlet />
        </div>
      </main>
    </div>
  );
}
