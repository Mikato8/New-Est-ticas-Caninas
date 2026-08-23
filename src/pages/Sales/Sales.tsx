import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type {
  Customer,
  PaymentMethod,
  Product,
  SaleStatus,
  SaleWithDetails,
  Service,
} from "../../types";
import {
  formatDate,
  formatMoney,
  saleStatusBadge,
  saleStatusLabels,
  todayISO,
} from "../../lib/format";

const statuses: SaleStatus[] = ["pending", "paid", "cancelled", "refunded"];

export default function Sales() {
  const { profile } = useAuth();
  const [sales, setSales] = useState<SaleWithDetails[]>([]);
  const [services, setServices] = useState<Service[]>([]);
  const [products, setProducts] = useState<Product[]>([]);
  const [methods, setMethods] = useState<PaymentMethod[]>([]);
  const [customers, setCustomers] = useState<Customer[]>([]);
  const [form, setForm] = useState({
    services: [] as number[],
    products: [] as number[],
    id_payment_method: "",
    costumer_id: "",
    sale_date: todayISO(),
    status: "pending" as SaleStatus,
  });
  const [editing, setEditing] = useState<number | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const [salesRes, servicesRes, productsRes, methodsRes, customersRes] =
      await Promise.all([
        supabase
          .from("sales")
          .select(
            "*, payment_methods(method_name), customers(customer_name), sale_services(services(service_name, price)), sale_products(products(product_name, sale_price))",
          )
          .order("sale_date", { ascending: false }),
        supabase.from("services").select("*").order("service_name"),
        supabase.from("products").select("*").order("product_name"),
        supabase.from("payment_methods").select("*").order("method_name"),
        supabase.from("customers").select("*").order("customer_name"),
      ]);

    if (salesRes.error) setError(salesRes.error.message);
    else setSales((salesRes.data as unknown as SaleWithDetails[]) ?? []);
    if (servicesRes.data) setServices(servicesRes.data as Service[]);
    if (productsRes.data) setProducts(productsRes.data as Product[]);
    if (methodsRes.data) setMethods(methodsRes.data as PaymentMethod[]);
    if (customersRes.data) setCustomers(customersRes.data as Customer[]);
  }

  useEffect(() => {
    load();
  }, []);

  const computedTotal = [
    ...form.services.map(
      (id) => services.find((s) => s.id_service === id)?.price ?? 0,
    ),
    ...form.products.map(
      (id) => products.find((p) => p.id_product === id)?.sale_price ?? 0,
    ),
  ].reduce((a, b) => a + Number(b), 0);

  function openNew() {
    setEditing(null);
    setForm({
      services: [],
      products: [],
      id_payment_method: "",
      costumer_id: "",
      sale_date: todayISO(),
      status: "pending",
    });
    setShowForm(true);
  }

  function openEdit(s: SaleWithDetails) {
    setEditing(s.id_sale);
    setForm({
      services: (s.sale_services ?? []).map((r) => r.id_service),
      products: (s.sale_products ?? []).map((r) => r.id_product),
      id_payment_method: s.id_payment_method
        ? String(s.id_payment_method)
        : "",
      costumer_id: s.costumer_id ? String(s.costumer_id) : "",
      sale_date: s.sale_date ?? todayISO(),
      status: s.status,
    });
    setShowForm(true);
  }

  function toggle(list: "services" | "products", id: number) {
    setForm((f) => ({
      ...f,
      [list]: f[list].includes(id)
        ? f[list].filter((x) => x !== id)
        : [...f[list], id],
    }));
  }

  function cancel() {
    setShowForm(false);
    setEditing(null);
  }

  async function syncItems(idSale: number) {
    await supabase.from("sale_services").delete().eq("id_sales", idSale);
    await supabase.from("sale_products").delete().eq("id_sale", idSale);
    if (form.services.length > 0) {
      await supabase
        .from("sale_services")
        .insert(form.services.map((id_service) => ({ id_service, id_sales: idSale })));
    }
    if (form.products.length > 0) {
      await supabase
        .from("sale_products")
        .insert(form.products.map((id_product) => ({ id_product, id_sale: idSale })));
    }
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    const payload = {
      total_price: computedTotal,
      id_payment_method: form.id_payment_method
        ? Number(form.id_payment_method)
        : null,
      costumer_id: form.costumer_id ? Number(form.costumer_id) : null,
      sale_date: form.sale_date || null,
      status: form.status,
      id_business: profile?.id_business,
    };

    if (editing !== null) {
      const { error: err } = await supabase
        .from("sales")
        .update(payload)
        .eq("id_sale", editing);
      if (err) return setError(err.message);
      await syncItems(editing);
    } else {
      const { data, error: err } = await supabase
        .from("sales")
        .insert(payload)
        .select("id_sale")
        .single();
      if (err) return setError(err.message);
      await syncItems((data as { id_sale: number }).id_sale);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar esta venta?")) return;
    const { error: err } = await supabase
      .from("sales")
      .delete()
      .eq("id_sale", id);
    if (err) setError(err.message);
    load();
  }

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 className="h3 mb-0">Ventas</h1>
        <button className="btn btn-primary" onClick={openNew}>
          Nueva venta
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">
              {editing !== null ? "Editar venta" : "Nueva venta"}
            </h5>
            <form onSubmit={handleSubmit}>
              <div className="row g-3">
                <div className="col-12 col-md-6">
                  <label className="form-label">Servicios</label>
                  <div className="border rounded p-2" style={{ maxHeight: 180, overflowY: "auto" }}>
                    {services.map((s) => (
                      <div className="form-check" key={s.id_service}>
                        <input
                          className="form-check-input"
                          type="checkbox"
                          id={`sale-svc-${s.id_service}`}
                          checked={form.services.includes(s.id_service)}
                          onChange={() => toggle("services", s.id_service)}
                        />
                        <label
                          className="form-check-label"
                          htmlFor={`sale-svc-${s.id_service}`}
                        >
                          {s.service_name} — {formatMoney(s.price)}
                        </label>
                      </div>
                    ))}
                  </div>
                </div>
                <div className="col-12 col-md-6">
                  <label className="form-label">Productos</label>
                  <div className="border rounded p-2" style={{ maxHeight: 180, overflowY: "auto" }}>
                    {products.map((p) => (
                      <div className="form-check" key={p.id_product}>
                        <input
                          className="form-check-input"
                          type="checkbox"
                          id={`sale-prod-${p.id_product}`}
                          checked={form.products.includes(p.id_product)}
                          onChange={() => toggle("products", p.id_product)}
                        />
                        <label
                          className="form-check-label"
                          htmlFor={`sale-prod-${p.id_product}`}
                        >
                          {p.product_name} — {formatMoney(p.sale_price)}
                        </label>
                      </div>
                    ))}
                  </div>
                </div>
                <div className="col-12 col-md-3">
                  <label className="form-label">Cliente</label>
                  <select
                    className="form-select"
                    value={form.costumer_id}
                    onChange={(e) =>
                      setForm({ ...form, costumer_id: e.target.value })
                    }
                  >
                    <option value="">Sin cliente</option>
                    {customers.map((c) => (
                      <option key={c.id_customer} value={c.id_customer}>
                        {c.customer_name}
                      </option>
                    ))}
                  </select>
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
                <div className="col-12 col-md-3">
                  <label className="form-label">Fecha</label>
                  <input
                    type="date"
                    className="form-control"
                    value={form.sale_date}
                    onChange={(e) =>
                      setForm({ ...form, sale_date: e.target.value })
                    }
                  />
                </div>
                <div className="col-12 col-md-3">
                  <label className="form-label">Estado</label>
                  <select
                    className="form-select"
                    value={form.status}
                    onChange={(e) =>
                      setForm({ ...form, status: e.target.value as SaleStatus })
                    }
                  >
                    {statuses.map((st) => (
                      <option key={st} value={st}>
                        {saleStatusLabels[st]}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="col-12 col-md-3">
                  <label className="form-label">Total</label>
                  <div className="form-control bg-light fw-semibold">
                    {formatMoney(computedTotal)}
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
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Método</th>
                <th>Conceptos</th>
                <th>Estado</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {sales.length === 0 && (
                <tr>
                  <td colSpan={7} className="text-center text-secondary py-4">
                    No hay ventas registradas
                  </td>
                </tr>
              )}
              {sales.map((s) => (
                <tr key={s.id_sale}>
                  <td className="fw-semibold">{formatDate(s.sale_date)}</td>
                  <td>{s.customers?.customer_name ?? "—"}</td>
                  <td>{formatMoney(s.total_price)}</td>
                  <td>{s.payment_methods?.method_name ?? "—"}</td>
                  <td>
                    {(s.sale_services ?? []).length +
                      (s.sale_products ?? []).length}{" "}
                    ítems
                  </td>
                  <td>
                    <span
                      className={`badge bg-${saleStatusBadge[s.status] ?? "secondary"}`}
                    >
                      {saleStatusLabels[s.status] ?? s.status}
                    </span>
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
                      onClick={() => handleDelete(s.id_sale)}
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
