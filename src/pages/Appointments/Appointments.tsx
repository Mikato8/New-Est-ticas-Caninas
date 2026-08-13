import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { AppointmentWithDetails, Pet, Service } from "../../types";

const emptyForm = { id_pet: "", id_service: "", date: "", time: "" };

function formatTimeForInput(iso: string): string {
  const d = new Date(iso);
  const hh = String(d.getHours()).padStart(2, "0");
  const mm = String(d.getMinutes()).padStart(2, "0");
  return `${hh}:${mm}`;
}

function formatTime(iso: string | null): string {
  if (!iso) return "—";
  return new Date(iso).toLocaleTimeString("es-MX", {
    hour: "2-digit",
    minute: "2-digit",
  });
}

function formatDate(date: string | null): string {
  if (!date) return "—";
  return new Date(`${date}T00:00:00`).toLocaleDateString("es-MX", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

export default function Appointments() {
  const { profile } = useAuth();
  const [appointments, setAppointments] = useState<AppointmentWithDetails[]>([]);
  const [pets, setPets] = useState<Pet[]>([]);
  const [services, setServices] = useState<Service[]>([]);
  const [form, setForm] = useState(emptyForm);
  const [editing, setEditing] = useState<number | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const [apptRes, petsRes, servicesRes] = await Promise.all([
      supabase
        .from("appointments")
        .select(
          "*, pets(pet_name, customers(customer_name)), services(service_name)",
        )
        .order("appointment_date", { ascending: false }),
      supabase.from("pets").select("*").order("pet_name"),
      supabase.from("services").select("*").order("service_name"),
    ]);

    if (apptRes.error) setError(apptRes.error.message);
    else
      setAppointments((apptRes.data as unknown as AppointmentWithDetails[]) ?? []);
    if (petsRes.data) setPets(petsRes.data as Pet[]);
    if (servicesRes.data) setServices(servicesRes.data as Service[]);
  }

  useEffect(() => {
    load();
  }, []);

  function openNew() {
    setEditing(null);
    setForm(emptyForm);
    setShowForm(true);
  }

  function openEdit(a: AppointmentWithDetails) {
    setEditing(a.id_appointment);
    setForm({
      id_pet: String(a.id_pet),
      id_service: String(a.id_service),
      date: a.appointment_date ?? "",
      time: a.appointment_time ? formatTimeForInput(a.appointment_time) : "",
    });
    setShowForm(true);
  }

  function cancel() {
    setShowForm(false);
    setEditing(null);
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    const appointment_time =
      form.date && form.time
        ? new Date(`${form.date}T${form.time}:00`).toISOString()
        : null;
    const payload = {
      id_pet: Number(form.id_pet),
      id_service: Number(form.id_service),
      appointment_date: form.date || null,
      appointment_time,
      id_business: profile?.id_business,
    };

    if (editing !== null) {
      const { error: err } = await supabase
        .from("appointments")
        .update(payload)
        .eq("id_appointment", editing);
      if (err) return setError(err.message);
    } else {
      const { error: err } = await supabase.from("appointments").insert(payload);
      if (err) return setError(err.message);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar esta cita?")) return;
    const { error: err } = await supabase
      .from("appointments")
      .delete()
      .eq("id_appointment", id);
    if (err) setError(err.message);
    load();
  }

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1 className="h3 mb-0">Citas</h1>
        <button className="btn btn-primary" onClick={openNew}>
          Nueva cita
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">
              {editing !== null ? "Editar cita" : "Nueva cita"}
            </h5>
            <form onSubmit={handleSubmit}>
              <div className="row g-3">
                <div className="col-12 col-md-4">
                  <label className="form-label">Mascota</label>
                  <select
                    className="form-select"
                    value={form.id_pet}
                    onChange={(e) =>
                      setForm({ ...form, id_pet: e.target.value })
                    }
                    required
                  >
                    <option value="">Selecciona...</option>
                    {pets.map((p) => (
                      <option key={p.id_pet} value={p.id_pet}>
                        {p.pet_name}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="col-12 col-md-4">
                  <label className="form-label">Servicio</label>
                  <select
                    className="form-select"
                    value={form.id_service}
                    onChange={(e) =>
                      setForm({ ...form, id_service: e.target.value })
                    }
                    required
                  >
                    <option value="">Selecciona...</option>
                    {services.map((s) => (
                      <option key={s.id_service} value={s.id_service}>
                        {s.service_name} — ${s.price}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="col-12 col-md-2">
                  <label className="form-label">Fecha</label>
                  <input
                    type="date"
                    className="form-control"
                    value={form.date}
                    onChange={(e) =>
                      setForm({ ...form, date: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="col-12 col-md-2">
                  <label className="form-label">Hora</label>
                  <input
                    type="time"
                    className="form-control"
                    value={form.time}
                    onChange={(e) =>
                      setForm({ ...form, time: e.target.value })
                    }
                    required
                  />
                </div>
              </div>
              <div className="mt-3 d-flex gap-2">
                <button type="submit" className="btn btn-success">
                  Guardar
                </button>
                <button
                  type="button"
                  className="btn btn-outline-secondary"
                  onClick={cancel}
                >
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      <div className="card shadow-sm">
        <div className="table-responsive">
          <table className="table table-hover align-middle mb-0">
            <thead className="table-light">
              <tr>
                <th>Mascota</th>
                <th>Dueño</th>
                <th>Servicio</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {appointments.length === 0 && (
                <tr>
                  <td colSpan={6} className="text-center text-secondary py-4">
                    No hay citas registradas
                  </td>
                </tr>
              )}
              {appointments.map((a) => (
                <tr key={a.id_appointment}>
                  <td className="fw-semibold">{a.pets?.pet_name ?? "—"}</td>
                  <td>{a.pets?.customers?.customer_name ?? "—"}</td>
                  <td>{a.services?.service_name ?? "—"}</td>
                  <td>{formatDate(a.appointment_date)}</td>
                  <td>{formatTime(a.appointment_time)}</td>
                  <td className="text-end">
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={() => openEdit(a)}
                    >
                      Editar
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={() => handleDelete(a.id_appointment)}
                    >
                      Eliminar
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
