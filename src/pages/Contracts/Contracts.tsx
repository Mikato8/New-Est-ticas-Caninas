import {
  useEffect,
  useState,
  type ChangeEvent,
  type FormEvent,
} from "react";
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
    template: "",
  });
  const [file, setFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
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
    setFile(null);
    setForm({
      contract_name: "",
      id_client: "",
      signed: false,
      signed_date: "",
      template: "",
    });
    setShowForm(true);
  }

  function openEdit(c: ContractWithDetails) {
    setEditing(c.id_contract);
    setFile(null);
    setForm({
      contract_name: c.contract_name,
      id_client: String(c.id_client),
      signed: c.signed,
      signed_date: c.signed_date ?? "",
      template: c.template ?? "",
    });
    setShowForm(true);
  }

  function cancel() {
    setShowForm(false);
    setEditing(null);
    setFile(null);
  }

  function handleFileChange(e: ChangeEvent<HTMLInputElement>) {
    setFile(e.target.files?.[0] ?? null);
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setUploading(true);
    try {
      let templateUrl = form.template;

      if (file) {
        const idBusiness = profile?.id_business ?? 0;
        const ext = file.name.split(".").pop() || "pdf";
        const path = `${idBusiness}/contract-${Date.now()}.${ext}`;
        const { error: upErr } = await supabase.storage
          .from("contracts")
          .upload(path, file, { upsert: false, contentType: file.type });
        if (upErr) throw upErr;
        const { data } = supabase.storage.from("contracts").getPublicUrl(path);
        templateUrl = data.publicUrl;
      }

      const payload = {
        contract_name: form.contract_name,
        id_client: Number(form.id_client),
        signed: form.signed,
        signed_date: form.signed && form.signed_date ? form.signed_date : null,
        template: templateUrl || null,
        id_business: profile?.id_business,
      };

      if (editing !== null) {
        const { error: err } = await supabase
          .from("contracts")
          .update(payload)
          .eq("id_contract", editing);
        if (err) throw err;
      } else {
        const { error: err } = await supabase.from("contracts").insert(payload);
        if (err) throw err;
      }

      cancel();
      load();
    } catch (err) {
      setError(err instanceof Error ? err.message : "No se pudo guardar");
    } finally {
      setUploading(false);
    }
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

  function handlePrint(url: string) {
    const iframe = document.createElement("iframe");
    iframe.src = url;
    iframe.style.position = "fixed";
    iframe.style.right = "0";
    iframe.style.bottom = "0";
    iframe.style.width = "0";
    iframe.style.height = "0";
    iframe.style.border = "0";
    iframe.onload = () => {
      try {
        iframe.contentWindow?.focus();
        iframe.contentWindow?.print();
      } catch {
        window.open(url, "_blank", "noopener,noreferrer");
      }
      setTimeout(() => iframe.remove(), 1000);
    };
    document.body.appendChild(iframe);
  }

  async function handleShare(url: string) {
    try {
      if (navigator.share) {
        await navigator.share({ url });
        return;
      }
      throw new Error("share-no-soporta");
    } catch {
      try {
        await navigator.clipboard.writeText(url);
        window.alert("Enlace copiado al portapapeles");
      } catch {
        window.prompt("Copia el enlace:", url);
      }
    }
  }

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
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
                <div className="col-12">
                  <label className="form-label">Plantilla (archivo)</label>
                  <input
                    type="file"
                    className="form-control"
                    onChange={handleFileChange}
                    disabled={uploading}
                  />
                  {form.template && (
                    <small className="text-secondary text-break d-block mt-1">
                      Actual: {form.template}
                    </small>
                  )}
                </div>
              </div>
              <div className="mt-3 d-flex flex-wrap gap-2">
                <button
                  type="submit"
                  className="btn btn-success"
                  disabled={uploading}
                >
                  {uploading ? "Guardando..." : "Guardar"}
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
                <th>Archivo</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {contracts.length === 0 && (
                <tr>
                  <td colSpan={6} className="text-center text-secondary py-4">
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
                  <td>
                    {c.template ? (
                      <div className="d-flex flex-column gap-1">
                        <a
                          href={c.template}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="text-break small"
                        >
                          {c.template}
                        </a>
                        <div className="d-flex gap-1">
                          <button
                            className="btn btn-sm btn-outline-secondary"
                            onClick={() => handlePrint(c.template!)}
                          >
                            Imprimir
                          </button>
                          <button
                            className="btn btn-sm btn-outline-secondary"
                            onClick={() => handleShare(c.template!)}
                          >
                            Compartir
                          </button>
                        </div>
                      </div>
                    ) : (
                      "—"
                    )}
                  </td>
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
