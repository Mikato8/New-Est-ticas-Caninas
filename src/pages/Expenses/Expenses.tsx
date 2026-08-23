import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { ExpenseWithDetails, PaymentMethod } from "../../types";
import { formatDate, formatMoney, todayISO } from "../../lib/format";

export default function Expenses() {
  const { profile } = useAuth();
  const [expenses, setExpenses] = useState<ExpenseWithDetails[]>([]);
  const [methods, setMethods] = useState<PaymentMethod[]>([]);
  const [form, setForm] = useState({
    expense_name: "",
    price: "",
    payment_date: todayISO(),
    id_payment_method: "",
  });
  const [editing, setEditing] = useState<number | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const [expensesRes, methodsRes] = await Promise.all([
      supabase
        .from("expenses")
        .select("*, payment_methods(method_name)")
        .order("payment_date", { ascending: false }),
      supabase.from("payment_methods").select("*").order("method_name"),
    ]);

    if (expensesRes.error) setError(expensesRes.error.message);
    else setExpenses((expensesRes.data as unknown as ExpenseWithDetails[]) ?? []);
    if (methodsRes.data) setMethods(methodsRes.data as PaymentMethod[]);
  }

  useEffect(() => {
    load();
  }, []);

  function openNew() {
    setEditing(null);
    setForm({
      expense_name: "",
      price: "",
      payment_date: todayISO(),
      id_payment_method: "",
    });
    setShowForm(true);
  }

  function openEdit(exp: ExpenseWithDetails) {
    setEditing(exp.id_expense);
    setForm({
      expense_name: exp.expense_name,
      price: String(exp.price ?? ""),
      payment_date: exp.payment_date ?? todayISO(),
      id_payment_method: exp.id_payment_method
        ? String(exp.id_payment_method)
        : "",
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
      expense_name: form.expense_name,
      price: Number(form.price || 0),
      payment_date: form.payment_date || null,
      id_payment_method: form.id_payment_method
        ? Number(form.id_payment_method)
        : null,
      id_business: profile?.id_business,
    };

    if (editing !== null) {
      const { error: err } = await supabase
        .from("expenses")
        .update(payload)
        .eq("id_expense", editing);
      if (err) return setError(err.message);
    } else {
      const { error: err } = await supabase.from("expenses").insert(payload);
      if (err) return setError(err.message);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar este gasto?")) return;
    const { error: err } = await supabase
      .from("expenses")
      .delete()
      .eq("id_expense", id);
    if (err) setError(err.message);
    load();
  }

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 className="h3 mb-0">Gastos</h1>
        <button className="btn btn-primary" onClick={openNew}>
          Nuevo gasto
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">
              {editing !== null ? "Editar gasto" : "Nuevo gasto"}
            </h5>
            <form onSubmit={handleSubmit}>
              <div className="row g-3">
                <div className="col-12 col-md-4">
                  <label className="form-label">Concepto</label>
                  <input
                    className="form-control"
                    value={form.expense_name}
                    onChange={(e) =>
                      setForm({ ...form, expense_name: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="col-12 col-md-2">
                  <label className="form-label">Monto</label>
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
                <div className="col-12 col-md-3">
                  <label className="form-label">Fecha</label>
                  <input
                    type="date"
                    className="form-control"
                    value={form.payment_date}
                    onChange={(e) =>
                      setForm({ ...form, payment_date: e.target.value })
                    }
                  />
                </div>
                <div className="col-12 col-md-3">
                  <label className="form-label">Método de pago</label>
                  <select
                    className="form-select"
                    value={form.id_payment_method}
                    onChange={(e) =>
                      setForm({ ...form, id_payment_method: e.target.value })
                    }
                  >
                    <option value="">Sin método</option>
                    {methods.map((m) => (
                      <option key={m.id_payment_method} value={m.id_payment_method}>
                        {m.method_name}
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
                <th>Concepto</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Método</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {expenses.length === 0 && (
                <tr>
                  <td colSpan={5} className="text-center text-secondary py-4">
                    No hay gastos registrados
                  </td>
                </tr>
              )}
              {expenses.map((exp) => (
                <tr key={exp.id_expense}>
                  <td className="fw-semibold">{exp.expense_name}</td>
                  <td>{formatMoney(exp.price)}</td>
                  <td>{formatDate(exp.payment_date)}</td>
                  <td>{exp.payment_methods?.method_name ?? "—"}</td>
                  <td className="text-end">
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={() => openEdit(exp)}
                    >
                      Editar
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={() => handleDelete(exp.id_expense)}
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
