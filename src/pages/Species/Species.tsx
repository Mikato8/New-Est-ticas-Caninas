import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { Specie } from "../../types";

export default function Species() {
  const { profile } = useAuth();
  const [species, setSpecies] = useState<Specie[]>([]);
  const [name, setName] = useState("");
  const [editing, setEditing] = useState<Specie | null>(null);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const { data, error: err } = await supabase
      .from("species")
      .select("*")
      .order("specie_name");
    if (err) setError(err.message);
    else setSpecies((data as Specie[]) ?? []);
  }

  useEffect(() => {
    load();
  }, []);

  function openEdit(s: Specie) {
    setEditing(s);
    setName(s.specie_name);
  }

  function cancel() {
    setEditing(null);
    setName("");
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    const payload = {
      specie_name: name,
      id_business: profile?.id_business,
    };

    if (editing) {
      const { error: err } = await supabase
        .from("species")
        .update(payload)
        .eq("id_specie", editing.id_specie);
      if (err) return setError(err.message);
    } else {
      const { error: err } = await supabase.from("species").insert(payload);
      if (err) return setError(err.message);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar esta especie?")) return;
    const { error: err } = await supabase
      .from("species")
      .delete()
      .eq("id_specie", id);
    if (err) setError(err.message);
    load();
  }

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 className="h3 mb-0">Especies</h1>
        <button className="btn btn-primary" onClick={cancel}>
          Nueva especie
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      <div className="card shadow-sm mb-4">
        <div className="card-body">
          <form onSubmit={handleSubmit} className="row g-3 align-items-end">
            <div className="col-12 col-md-6">
              <label className="form-label">Nombre de la especie</label>
              <input
                className="form-control"
                value={name}
                onChange={(e) => setName(e.target.value)}
                placeholder="Perro, Gato, Conejo..."
                required
              />
            </div>
            <div className="col-12 col-md-6 d-flex flex-wrap gap-2">
              <button type="submit" className="btn btn-success">
                {editing ? "Guardar cambios" : "Agregar"}
              </button>
              {editing && (
                <button
                  type="button"
                  className="btn btn-outline-secondary"
                  onClick={cancel}
                >
                  Cancelar
                </button>
              )}
            </div>
          </form>
        </div>
      </div>

      <div className="card shadow-sm">
        <div className="table-responsive">
          <table className="table table-hover align-middle mb-0">
            <thead className="table-light">
              <tr>
                <th>Especie</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {species.length === 0 && (
                <tr>
                  <td colSpan={2} className="text-center text-secondary py-4">
                    No hay especies registradas
                  </td>
                </tr>
              )}
              {species.map((s) => (
                <tr key={s.id_specie}>
                  <td className="fw-semibold">{s.specie_name}</td>
                  <td className="text-end">
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={() => openEdit(s)}
                    >
                      Editar
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={() => handleDelete(s.id_specie)}
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
