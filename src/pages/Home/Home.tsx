import { useEffect, useState } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { AppointmentWithDetails } from "../../types";
import {
  formatDate,
  formatMoney,
  monthStartISO,
  todayISO,
} from "../../lib/format";

interface Stats {
  customers: number;
  pets: number;
  appointmentsToday: number;
  pendingSales: number;
  incomeMonth: number;
  expensesMonth: number;
  businessName: string;
}

export default function Home() {
  const { profile } = useAuth();
  const [stats, setStats] = useState<Stats | null>(null);
  const [upcoming, setUpcoming] = useState<AppointmentWithDetails[]>([]);
  const [error, setError] = useState<string | null>(null);

  const roleLabel =
    profile?.id_rol === 1
      ? "Administrador"
      : profile?.id_rol === 2
        ? "Capturista de datos"
        : "Usuario";

  useEffect(() => {
    let active = true;
    async function load() {
      const today = todayISO();
      const monthStart = monthStartISO();

      const [
        customers,
        pets,
        appointmentsToday,
        pendingSales,
        income,
        expenses,
        upcomingRes,
        business,
      ] = await Promise.all([
        supabase
          .from("customers")
          .select("id_customer", { count: "exact", head: true }),
        supabase
          .from("pets")
          .select("id_pet", { count: "exact", head: true }),
        supabase
          .from("appointments")
          .select("id_appointment", { count: "exact", head: true })
          .eq("appointment_date", today),
        supabase
          .from("sales")
          .select("id_sale", { count: "exact", head: true })
          .eq("status", "pending"),
        supabase
          .from("sales")
          .select("total_price")
          .eq("status", "paid")
          .gte("sale_date", monthStart),
        supabase
          .from("expenses")
          .select("price")
          .gte("payment_date", monthStart),
        supabase
          .from("appointments")
          .select(
            "*, pets(pet_name, customers(customer_name)), services(service_name)",
          )
          .gte("appointment_date", today)
          .order("appointment_date")
          .limit(5),
        supabase
          .from("business")
          .select("business_name")
          .limit(1)
          .maybeSingle(),
      ]);

      if (!active) return;

      if (customers.error || pets.error || appointmentsToday.error) {
        setError("No se pudieron cargar las estadísticas");
        return;
      }

      const incomeMonth = ((income.data as { total_price: number }[]) ?? []).reduce(
        (sum, s) => sum + Number(s.total_price),
        0,
      );
      const expensesMonth = (
        (expenses.data as { price: number }[]) ?? []
      ).reduce((sum, e) => sum + Number(e.price), 0);

      setStats({
        customers: customers.count ?? 0,
        pets: pets.count ?? 0,
        appointmentsToday: appointmentsToday.count ?? 0,
        pendingSales: pendingSales.count ?? 0,
        incomeMonth,
        expensesMonth,
        businessName: business.data?.business_name ?? "Mi negocio",
      });

      if (!upcomingRes.error) {
        setUpcoming(
          (upcomingRes.data as unknown as AppointmentWithDetails[]) ?? [],
        );
      }
    }

    load();
    return () => {
      active = false;
    };
  }, []);

  const cards = [
    { label: "Clientes", value: stats ? String(stats.customers) : "—" },
    { label: "Mascotas", value: stats ? String(stats.pets) : "—" },
    { label: "Citas hoy", value: stats ? String(stats.appointmentsToday) : "—" },
    { label: "Ventas pendientes", value: stats ? String(stats.pendingSales) : "—" },
  ];

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
          <h1 className="h3 mb-0">Inicio</h1>
          <p className="text-secondary mb-0">
            {stats ? `Bienvenido a ${stats.businessName}` : "Cargando..."}
          </p>
        </div>
        <div className="text-end">
          <div className="fw-semibold">{profile?.user_name}</div>
          <span
            className={`badge ${profile?.id_rol === 1 ? "bg-primary" : "bg-secondary"}`}
          >
            {roleLabel}
          </span>
        </div>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      <div className="row g-3 mb-4">
        {cards.map((card) => (
          <div className="col-6 col-md-3" key={card.label}>
            <div className="card shadow-sm h-100">
              <div className="card-body">
                <div className="text-secondary small text-uppercase">
                  {card.label}
                </div>
                <div className="display-6 fw-bold">{card.value}</div>
              </div>
            </div>
          </div>
        ))}
      </div>

      <div className="row g-3 mb-4">
        <div className="col-12 col-md-6">
          <div className="card shadow-sm h-100">
            <div className="card-body">
              <div className="text-secondary small text-uppercase">
                Ingresos del mes
              </div>
              <div className="h3 fw-bold text-success mb-0">
                {formatMoney(stats?.incomeMonth ?? 0)}
              </div>
            </div>
          </div>
        </div>
        <div className="col-12 col-md-6">
          <div className="card shadow-sm h-100">
            <div className="card-body">
              <div className="text-secondary small text-uppercase">
                Gastos del mes
              </div>
              <div className="h3 fw-bold text-danger mb-0">
                {formatMoney(stats?.expensesMonth ?? 0)}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="card shadow-sm">
        <div className="card-body">
          <h5 className="mb-3">Próximas citas</h5>
          {upcoming.length === 0 ? (
            <p className="text-secondary mb-0">No hay citas próximas</p>
          ) : (
            <div className="list-group list-group-flush">
              {upcoming.map((a) => (
                <div
                  className="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-2 px-0"
                  key={a.id_appointment}
                >
                  <div>
                    <div className="fw-semibold">
                      {a.pets?.pet_name ?? "Mascota"}
                    </div>
                    <small className="text-secondary">
                      {a.pets?.customers?.customer_name ?? "—"} ·{" "}
                      {a.services?.service_name ?? "—"}
                    </small>
                  </div>
                  <div className="text-end">
                    <div className="fw-semibold">
                      {formatDate(a.appointment_date)}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
