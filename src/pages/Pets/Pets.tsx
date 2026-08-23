import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { Customer, PetWithDetails, Specie } from "../../types";

const emptyForm = { pet_name: "", id_specie: "", id_customer: "" };

export default function Pets() {
  const { profile } = useAuth();
  const [pets, setPets] = useState<PetWithDetails[]>([]);
  const [species, setSpecies] = useState<Specie[]>([]);
  const [customers, setCustomers] = useState<Customer[]>([]);
  const [form, setForm] = useState(emptyForm);
  const [editing, setEditing] = useState<number | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const [petsRes, speciesRes, customersRes] = await Promise.all([
      supabase
        .from("pets")
        .select("*, species(specie_name), customers(customer_name)")
        .order("pet_name"),
      supabase.from("species").select("*").order("specie_name"),
      supabase.from("customers").select("*").order("customer_name"),
    ]);

    if (petsRes.error) setError(petsRes.error.message);
    else setPets((petsRes.data as unknown as PetWithDetails[]) ?? []);
    if (speciesRes.data) setSpecies(speciesRes.data as Specie[]);
    if (customersRes.data) setCustomers(customersRes.data as Customer[]);
  }

  useEffect(() => {
    load();
  }, []);

  function openNew() {
    setEditing(null);
    setForm(emptyForm);
    setShowForm(true);
  }

  function openEdit(p: PetWithDetails) {
    setEditing(p.id_pet);
    setForm({
      pet_name: p.pet_name,
      id_specie: String(p.id_specie),
      id_customer: String(p.id_customer),
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
    const payload = {
      pet_name: form.pet_name,
      id_specie: Number(form.id_specie),
      id_customer: Number(form.id_customer),
      id_business: profile?.id_business,
    };

    if (editing !== null) {
      const { error: err } = await supabase
        .from("pets")
        .update(payload)
        .eq("id_pet", editing);
      if (err) return setError(err.message);
    } else {
      const { error: err } = await supabase.from("pets").insert(payload);
      if (err) return setError(err.message);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar esta mascota?")) return;
    const { error: err } = await supabase.from("pets").delete().eq("id_pet", id);
    if (err) setError(err.message);
    load();
  }

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 className="h3 mb-0">Mascotas</h1>
        <button className="btn btn-primary" onClick={openNew}>
          Nueva mascota
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">
              {editing !== null ? "Editar mascota" : "Nueva mascota"}
            </h5>
            <form onSubmit={handleSubmit}>
              <div className="row g-3">
                <div className="col-12 col-md-4">
                  <label className="form-label">Nombre</label>
                  <input
                    className="form-control"
                    value={form.pet_name}
                    onChange={(e) =>
                      setForm({ ...form, pet_name: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="col-12 col-md-4">
                  <label className="form-label">Especie</label>
                  <select
                    className="form-select"
                    value={form.id_specie}
                    onChange={(e) =>
                      setForm({ ...form, id_specie: e.target.value })
                    }
                    required
                  >
                    <option value="">Selecciona...</option>
                    {species.map((s) => (
                      <option key={s.id_specie} value={s.id_specie}>
                        {s.specie_name}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="col-12 col-md-4">
                  <label className="form-label">Dueño</label>
                  <select
                    className="form-select"
                    value={form.id_customer}
                    onChange={(e) =>
                      setForm({ ...form, id_customer: e.target.value })
                    }
                    required
                  >
                    <option value="">Selecciona...</option>
                    {customers.map((c) => (
                      <option key={c.id_customer} value={c.id_customer}>
                        {c.customer_name}
                      </option>
                    ))}
                  </select>
                </div>
              </div>
              <div className="mt-3 d-flex flex-wrap gap-2">
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
                <th>Nombre</th>
                <th>Especie</th>
                <th>Dueño</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {pets.length === 0 && (
                <tr>
                  <td colSpan={4} className="text-center text-secondary py-4">
                    No hay mascotas registradas
                  </td>
                </tr>
              )}
              {pets.map((p) => (
                <tr key={p.id_pet}>
                  <td className="fw-semibold">{p.pet_name}</td>
                  <td>{p.species?.specie_name ?? "—"}</td>
                  <td>{p.customers?.customer_name ?? "—"}</td>
                  <td className="text-end">
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={() => openEdit(p)}
                    >
                      Editar
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={() => handleDelete(p.id_pet)}
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
