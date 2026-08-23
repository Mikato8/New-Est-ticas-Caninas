import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { ServiceWithDetails, Specie } from "../../types";
import { formatMoney, serviceSizeLabels } from "../../lib/format";

const sizes = ["small", "medium", "large", "xl"] as const;

export default function Services() {
  const { profile } = useAuth();
  const [services, setServices] = useState<ServiceWithDetails[]>([]);
  const [species, setSpecies] = useState<Specie[]>([]);
  const [form, setForm] = useState({
    service_name: "",
    size: "",
    price: "",
    species: [] as number[],
  });
  const [editing, setEditing] = useState<number | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const [servicesRes, speciesRes] = await Promise.all([
      supabase
        .from("services")
        .select("*, service_species(species(id_specie, specie_name))")
        .order("service_name"),
      supabase.from("species").select("*").order("specie_name"),
    ]);

    if (servicesRes.error) setError(servicesRes.error.message);
    else setServices((servicesRes.data as unknown as ServiceWithDetails[]) ?? []);
    if (speciesRes.data) setSpecies(speciesRes.data as Specie[]);
  }

  useEffect(() => {
    load();
  }, []);

  function openNew() {
    setEditing(null);
    setForm({ service_name: "", size: "", price: "", species: [] });
    setShowForm(true);
  }

  function openEdit(s: ServiceWithDetails) {
    setEditing(s.id_service);
    setForm({
      service_name: s.service_name,
      size: s.size ?? "",
      price: String(s.price ?? ""),
      species: (s.service_species ?? []).map((r) => r.species.id_specie),
    });
    setShowForm(true);
  }

  function toggleSpecie(id: number) {
    setForm((f) => ({
      ...f,
      species: f.species.includes(id)
        ? f.species.filter((x) => x !== id)
        : [...f.species, id],
    }));
  }

  function cancel() {
    setShowForm(false);
    setEditing(null);
  }

  async function syncSpecies(idService: number) {
    await supabase
      .from("service_species")
      .delete()
      .eq("id_service", idService);
    if (form.species.length > 0) {
      await supabase.from("service_species").insert(
        form.species.map((id_specie) => ({ id_service: idService, id_specie })),
      );
    }
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    const payload = {
      service_name: form.service_name,
      size: form.size || null,
      price: Number(form.price || 0),
      id_business: profile?.id_business,
    };

    if (editing !== null) {
      const { error: err } = await supabase
        .from("services")
        .update(payload)
        .eq("id_service", editing);
      if (err) return setError(err.message);
      await syncSpecies(editing);
    } else {
      const { data, error: err } = await supabase
        .from("services")
        .insert(payload)
        .select("id_service")
        .single();
      if (err) return setError(err.message);
      await syncSpecies((data as { id_service: number }).id_service);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar este servicio?")) return;
    const { error: err } = await supabase
      .from("services")
      .delete()
      .eq("id_service", id);
    if (err) setError(err.message);
    load();
  }

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 className="h3 mb-0">Servicios</h1>
        <button className="btn btn-primary" onClick={openNew}>
          Nuevo servicio
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">
              {editing !== null ? "Editar servicio" : "Nuevo servicio"}
            </h5>
            <form onSubmit={handleSubmit}>
              <div className="row g-3">
                <div className="col-12 col-md-4">
                  <label className="form-label">Nombre</label>
                  <input
                    className="form-control"
                    value={form.service_name}
                    onChange={(e) =>
                      setForm({ ...form, service_name: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="col-12 col-md-4">
                  <label className="form-label">Tamaño</label>
                  <select
                    className="form-select"
                    value={form.size}
                    onChange={(e) => setForm({ ...form, size: e.target.value })}
                  >
                    <option value="">Todos los tamaños</option>
                    {sizes.map((s) => (
                      <option key={s} value={s}>
                        {serviceSizeLabels[s]}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="col-12 col-md-4">
                  <label className="form-label">Precio</label>
                  <input
                    type="number"
                    min="0"
                    step="0.01"
                    className="form-control"
                    value={form.price}
                    onChange={(e) => setForm({ ...form, price: e.target.value })}
                    required
                  />
                </div>
                <div className="col-12">
                  <label className="form-label">Especies compatibles</label>
                  <div className="d-flex flex-wrap gap-2">
                    {species.map((s) => (
                      <div className="form-check" key={s.id_specie}>
                        <input
                          className="form-check-input"
                          type="checkbox"
                          id={`specie-${s.id_specie}`}
                          checked={form.species.includes(s.id_specie)}
                          onChange={() => toggleSpecie(s.id_specie)}
                        />
                        <label
                          className="form-check-label"
                          htmlFor={`specie-${s.id_specie}`}
                        >
                          {s.specie_name}
                        </label>
                      </div>
                    ))}
                  </div>
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
                <th>Servicio</th>
                <th>Tamaño</th>
                <th>Precio</th>
                <th>Especies</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {services.length === 0 && (
                <tr>
                  <td colSpan={5} className="text-center text-secondary py-4">
                    No hay servicios registrados
                  </td>
                </tr>
              )}
              {services.map((s) => (
                <tr key={s.id_service}>
                  <td className="fw-semibold">{s.service_name}</td>
                  <td>{s.size ? serviceSizeLabels[s.size] : "Todos"}</td>
                  <td>{formatMoney(s.price)}</td>
                  <td>
                    {(s.service_species ?? []).length === 0
                      ? "Todas"
                      : (s.service_species ?? [])
                          .map((r) => r.species.specie_name)
                          .join(", ")}
                  </td>
                  <td className="text-end">
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={() => openEdit(s)}
                    >
                      Editar
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={() => handleDelete(s.id_service)}
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
