import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { ContractWithDetails, Customer } from "../../types";
import { formatDate, todayISO } from "../../lib/format";

export default function Contracts() {
  const { profile } = useAuth();
  const [contracts, setContracts] = useState<ContractWithDetails[]>([]);
  const [customers, setCustomers] = useState<Customer[]>([]);
  const [form, setForm] = useState({
    contract_name: "",
    id_client: "",
    signed: false,
    signed_date: "",
  });
  const [editing, setEditing] = useState<number | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const [contractsRes, customersRes] = await Promise.all([
      supabase
        .from("contracts")
        .select("*, customers(customer_name)")
        .order("contract_name"),
      supabase.from("customers").select("*").order("customer_name"),
    ]);

    if (contractsRes.error) setError(contractsRes.error.message);
    else setContracts((contractsRes.data as unknown as ContractWithDetails[]) ?? []);
    if (customersRes.data) setCustomers(customersRes.data as Customer[]);
  }

  useEffect(() => {
    load();
  }, []);

  function openNew() {
    setEditing(null);
    setForm({
      contract_name: "",
      id_client: "",
      signed: false,
      signed_date: "",
    });
    setShowForm(true);
  }

  function openEdit(c: ContractWithDetails) {
    setEditing(c.id_contract);
    setForm({
      contract_name: c.contract_name,
      id_client: String(c.id_client),
      signed: c.signed,
      signed_date: c.signed_date ?? "",
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
      contract_name: form.contract_name,
      id_client: Number(form.id_client),
      signed: form.signed,
      signed_date: form.signed && form.signed_date ? form.signed_date : null,
      id_business: profile?.id_business,
    };

    if (editing !== null) {
      const { error: err } = await supabase
        .from("contracts")
        .update(payload)
        .eq("id_contract", editing);
      if (err) return setError(err.message);
    } else {
      const { error: err } = await supabase.from("contracts").insert(payload);
      if (err) return setError(err.message);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar este contrato?")) return;
    const { error: err } = await supabase
      .from("contracts")
      .delete()
      .eq("id_contract", id);
    if (err) setError(err.message);
    load();
  }

  async function toggleSigned(c: ContractWithDetails) {
    const { error: err } = await supabase
      .from("contracts")
      .update({
        signed: !c.signed,
        signed_date: !c.signed ? todayISO() : null,
      })
      .eq("id_contract", c.id_contract);
    if (err) setError(err.message);
    load();
  }

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1 className="h3 mb-0">Contratos</h1>
        <button className="btn btn-primary" onClick={openNew}>
          Nuevo contrato
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">
              {editing !== null ? "Editar contrato" : "Nuevo contrato"}
            </h5>
            <form onSubmit={handleSubmit}>
              <div className="row g-3">
                <div className="col-12 col-md-4">
                  <label className="form-label">Nombre del contrato</label>
                  <input
                    className="form-control"
                    value={form.contract_name}
                    onChange={(e) =>
                      setForm({ ...form, contract_name: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="col-12 col-md-4">
                  <label className="form-label">Cliente</label>
                  <select
                    className="form-select"
                    value={form.id_client}
                    onChange={(e) =>
                      setForm({ ...form, id_client: e.target.value })
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
                <div className="col-12 col-md-4">
                  <label className="form-label">Fecha de firma</label>
                  <input
                    type="date"
                    className="form-control"
                    value={form.signed_date}
                    onChange={(e) =>
                      setForm({ ...form, signed_date: e.target.value })
                    }
                  />
                </div>
                <div className="col-12">
                  <div className="form-check">
                    <input
                      className="form-check-input"
                      type="checkbox"
                      id="contract-signed"
                      checked={form.signed}
                      onChange={(e) =>
                        setForm({ ...form, signed: e.target.checked })
                      }
                    />
                    <label
                      className="form-check-label"
                      htmlFor="contract-signed"
                    >
                      Firmado
                    </label>
                  </div>
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
                <th>Contrato</th>
                <th>Cliente</th>
                <th>Estado</th>
                <th>Fecha de firma</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {contracts.length === 0 && (
                <tr>
                  <td colSpan={5} className="text-center text-secondary py-4">
                    No hay contratos registrados
                  </td>
                </tr>
              )}
              {contracts.map((c) => (
                <tr key={c.id_contract}>
                  <td className="fw-semibold">{c.contract_name}</td>
                  <td>{c.customers?.customer_name ?? "—"}</td>
                  <td>
                    <button
                      className={`btn btn-sm ${
                        c.signed ? "btn-success" : "btn-outline-warning"
                      }`}
                      onClick={() => toggleSigned(c)}
                    >
                      {c.signed ? "Firmado" : "Pendiente"}
                    </button>
                  </td>
                  <td>{formatDate(c.signed_date)}</td>
                  <td className="text-end">
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={() => openEdit(c)}
                    >
                      Editar
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={() => handleDelete(c.id_contract)}
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
