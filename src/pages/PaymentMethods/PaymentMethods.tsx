import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { PaymentMethod } from "../../types";

export default function PaymentMethods() {
  const { profile } = useAuth();
  const [methods, setMethods] = useState<PaymentMethod[]>([]);
  const [form, setForm] = useState({ method_name: "", account_number: "" });
  const [editing, setEditing] = useState<PaymentMethod | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const { data, error: err } = await supabase
      .from("payment_methods")
      .select("*")
      .order("method_name");
    if (err) setError(err.message);
    else setMethods((data as PaymentMethod[]) ?? []);
  }

  useEffect(() => {
    load();
  }, []);

  function openNew() {
    setEditing(null);
    setForm({ method_name: "", account_number: "" });
    setShowForm(true);
  }

  function openEdit(m: PaymentMethod) {
    setEditing(m);
    setForm({
      method_name: m.method_name,
      account_number: m.account_number ?? "",
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
      method_name: form.method_name,
      account_number: form.account_number || null,
      id_business: profile?.id_business,
    };

    if (editing) {
      const { error: err } = await supabase
        .from("payment_methods")
        .update(payload)
        .eq("id_payment_method", editing.id_payment_method);
      if (err) return setError(err.message);
    } else {
      const { error: err } = await supabase
        .from("payment_methods")
        .insert(payload);
      if (err) return setError(err.message);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar este método de pago?")) return;
    const { error: err } = await supabase
      .from("payment_methods")
      .delete()
      .eq("id_payment_method", id);
    if (err) setError(err.message);
    load();
  }

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1 className="h3 mb-0">Métodos de pago</h1>
        <button className="btn btn-primary" onClick={openNew}>
          Nuevo método
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">
              {editing ? "Editar método" : "Nuevo método"}
            </h5>
            <form onSubmit={handleSubmit}>
              <div className="row g-3">
                <div className="col-12 col-md-6">
                  <label className="form-label">Nombre</label>
                  <input
                    className="form-control"
                    value={form.method_name}
                    onChange={(e) =>
                      setForm({ ...form, method_name: e.target.value })
                    }
                    placeholder="Efectivo, Tarjeta, Transferencia..."
                    required
                  />
                </div>
                <div className="col-12 col-md-6">
                  <label className="form-label">Número de cuenta</label>
                  <input
                    className="form-control"
                    value={form.account_number}
                    onChange={(e) =>
                      setForm({ ...form, account_number: e.target.value })
                    }
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
                <th>Método</th>
                <th>Número de cuenta</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {methods.length === 0 && (
                <tr>
                  <td colSpan={3} className="text-center text-secondary py-4">
                    No hay métodos de pago registrados
                  </td>
                </tr>
              )}
              {methods.map((m) => (
                <tr key={m.id_payment_method}>
                  <td className="fw-semibold">{m.method_name}</td>
                  <td>{m.account_number ?? "—"}</td>
                  <td className="text-end">
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={() => openEdit(m)}
                    >
                      Editar
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={() => handleDelete(m.id_payment_method)}
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
