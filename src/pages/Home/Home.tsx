import { useEffect, useState } from "react";
import { supabase } from "../../lib/supabase";

interface Stats {
  customers: number;
  pets: number;
  appointmentsToday: number;
  businessName: string;
}

export default function Home() {
  const [stats, setStats] = useState<Stats | null>(null);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let active = true;
    async function load() {
      const now = new Date();
      const today = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`;
      const [customers, pets, appointments, business] = await Promise.all([
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
          .from("business")
          .select("business_name")
          .limit(1)
          .maybeSingle(),
      ]);

      if (!active) return;

      if (customers.error || pets.error || appointments.error) {
        setError("No se pudieron cargar las estadísticas");
        return;
      }

      setStats({
        customers: customers.count ?? 0,
        pets: pets.count ?? 0,
        appointmentsToday: appointments.count ?? 0,
        businessName: business.data?.business_name ?? "Mi negocio",
      });
    }

    load();
    return () => {
      active = false;
    };
  }, []);

  const cards = [
    { label: "Clientes", value: stats?.customers ?? "—" },
    { label: "Mascotas", value: stats?.pets ?? "—" },
    { label: "Citas hoy", value: stats?.appointmentsToday ?? "—" },
  ];

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 className="h3 mb-0">Inicio</h1>
          <p className="text-secondary mb-0">
            {stats ? `Bienvenido a ${stats.businessName}` : "Cargando..."}
          </p>
        </div>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      <div className="row g-3">
        {cards.map((card) => (
          <div className="col-12 col-md-4" key={card.label}>
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
    </div>
  );
}
