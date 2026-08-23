import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { PackageWithDetails, Product, Service } from "../../types";
import { formatMoney, serviceSizeLabels } from "../../lib/format";

const sizes = ["small", "medium", "large", "xl"] as const;

export default function Packages() {
  const { profile } = useAuth();
  const [packages, setPackages] = useState<PackageWithDetails[]>([]);
  const [services, setServices] = useState<Service[]>([]);
  const [products, setProducts] = useState<Product[]>([]);
  const [form, setForm] = useState({
    package_name: "",
    description: "",
    price: "",
    size: "",
    services: [] as number[],
    products: [] as number[],
  });
  const [editing, setEditing] = useState<number | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const [pkgsRes, servicesRes, productsRes] = await Promise.all([
      supabase
        .from("packages")
        .select(
          "*, package_services(services(id_service, service_name, price)), package_products(products(id_product, product_name, sale_price))",
        )
        .order("package_name"),
      supabase.from("services").select("*").order("service_name"),
      supabase.from("products").select("*").order("product_name"),
    ]);

    if (pkgsRes.error) setError(pkgsRes.error.message);
    else setPackages((pkgsRes.data as unknown as PackageWithDetails[]) ?? []);
    if (servicesRes.data) setServices(servicesRes.data as Service[]);
    if (productsRes.data) setProducts(productsRes.data as Product[]);
  }

  useEffect(() => {
    load();
  }, []);

  const computedTotal = [
    ...form.services
      .map((id) => services.find((s) => s.id_service === id)?.price ?? 0)
      .map(Number),
    ...form.products
      .map((id) => products.find((p) => p.id_product === id)?.sale_price ?? 0)
      .map(Number),
  ].reduce((a, b) => a + b, 0);

  function openNew() {
    setEditing(null);
    setForm({
      package_name: "",
      description: "",
      price: "",
      size: "",
      services: [],
      products: [],
    });
    setShowForm(true);
  }

  function openEdit(p: PackageWithDetails) {
    setEditing(p.id_package);
    setForm({
      package_name: p.package_name,
      description: p.description ?? "",
      price: String(p.price ?? ""),
      size: p.size ?? "",
      services: (p.package_services ?? []).map((r) => r.services.id_service),
      products: (p.package_products ?? []).map((r) => r.products.id_product),
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

  async function syncItems(idPackage: number) {
    await supabase
      .from("package_services")
      .delete()
      .eq("id_package", idPackage);
    await supabase
      .from("package_products")
      .delete()
      .eq("id_package", idPackage);
    if (form.services.length > 0) {
      await supabase
        .from("package_services")
        .insert(form.services.map((id_service) => ({ id_service, id_package: idPackage })));
    }
    if (form.products.length > 0) {
      await supabase
        .from("package_products")
        .insert(form.products.map((id_product) => ({ id_product, id_package: idPackage })));
    }
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    const payload = {
      package_name: form.package_name,
      description: form.description || null,
      price: Number(form.price || 0),
      size: form.size || null,
      id_business: profile?.id_business,
    };

    if (editing !== null) {
      const { error: err } = await supabase
        .from("packages")
        .update(payload)
        .eq("id_package", editing);
      if (err) return setError(err.message);
      await syncItems(editing);
    } else {
      const { data, error: err } = await supabase
        .from("packages")
        .insert(payload)
        .select("id_package")
        .single();
      if (err) return setError(err.message);
      await syncItems((data as { id_package: number }).id_package);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar este paquete?")) return;
    const { error: err } = await supabase
      .from("packages")
      .delete()
      .eq("id_package", id);
    if (err) setError(err.message);
    load();
  }

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 className="h3 mb-0">Paquetes</h1>
        <button className="btn btn-primary" onClick={openNew}>
          Nuevo paquete
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">
              {editing !== null ? "Editar paquete" : "Nuevo paquete"}
            </h5>
            <form onSubmit={handleSubmit}>
              <div className="row g-3">
                <div className="col-12 col-md-3">
                  <label className="form-label">Nombre</label>
                  <input
                    className="form-control"
                    value={form.package_name}
                    onChange={(e) =>
                      setForm({ ...form, package_name: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="col-12 col-md-3">
                  <label className="form-label">Descripción</label>
                  <input
                    className="form-control"
                    value={form.description}
                    onChange={(e) =>
                      setForm({ ...form, description: e.target.value })
                    }
                  />
                </div>
                <div className="col-12 col-md-3">
                  <label className="form-label">Precio</label>
                  <input
                    type="number"
                    min="0"
                    step="0.01"
                    className="form-control"
                    value={form.price}
                    onChange={(e) => setForm({ ...form, price: e.target.value })}
                  />
                  <div className="form-text">
                    Total sugerido: {formatMoney(computedTotal)}
                  </div>
                </div>
                <div className="col-12 col-md-3">
                  <label className="form-label">Talla</label>
                  <select
                    className="form-select"
                    value={form.size}
                    onChange={(e) => setForm({ ...form, size: e.target.value })}
                  >
                    <option value="">Sin talla</option>
                    {sizes.map((s) => (
                      <option key={s} value={s}>
                        {serviceSizeLabels[s]}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="col-12 col-md-6">
                  <label className="form-label">Servicios incluidos</label>
                  <div className="border rounded p-2" style={{ maxHeight: 180, overflowY: "auto" }}>
                    {services.map((s) => (
                      <div className="form-check" key={s.id_service}>
                        <input
                          className="form-check-input"
                          type="checkbox"
                          id={`pkg-svc-${s.id_service}`}
                          checked={form.services.includes(s.id_service)}
                          onChange={() => toggle("services", s.id_service)}
                        />
                        <label
                          className="form-check-label"
                          htmlFor={`pkg-svc-${s.id_service}`}
                        >
                          {s.service_name} — {formatMoney(s.price)}
                        </label>
                      </div>
                    ))}
                  </div>
                </div>
                <div className="col-12 col-md-6">
                  <label className="form-label">Productos incluidos</label>
                  <div className="border rounded p-2" style={{ maxHeight: 180, overflowY: "auto" }}>
                    {products.map((p) => (
                      <div className="form-check" key={p.id_product}>
                        <input
                          className="form-check-input"
                          type="checkbox"
                          id={`pkg-prod-${p.id_product}`}
                          checked={form.products.includes(p.id_product)}
                          onChange={() => toggle("products", p.id_product)}
                        />
                        <label
                          className="form-check-label"
                          htmlFor={`pkg-prod-${p.id_product}`}
                        >
                          {p.product_name} — {formatMoney(p.sale_price)}
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

      <div className="row g-3">
        {packages.length === 0 && (
          <div className="col-12">
            <div className="card shadow-sm">
              <div className="card-body text-center text-secondary py-4">
                No hay paquetes registrados
              </div>
            </div>
          </div>
        )}
        {packages.map((p) => (
          <div className="col-12 col-md-6 col-lg-4" key={p.id_package}>
            <div className="card shadow-sm h-100">
              <div className="card-body d-flex flex-column">
                <div className="d-flex flex-wrap justify-content-between align-items-start gap-2">
                  <h5 className="mb-1 text-break">{p.package_name}</h5>
                  <span className="badge bg-primary">
                    {formatMoney(p.price)}
                  </span>
                </div>
                {p.description && (
                  <p className="text-secondary small mb-3">{p.description}</p>
                )}
                <div className="small mb-3">
                  <div className="text-secondary text-uppercase small">
                    Servicios
                  </div>
                  <ul className="mb-2 ps-3">
                    {(p.package_services ?? []).map((r) => (
                      <li key={r.services.id_service}>
                        {r.services.service_name}
                      </li>
                    ))}
                  </ul>
                  <div className="text-secondary text-uppercase small">
                    Productos
                  </div>
                  <ul className="mb-0 ps-3">
                    {(p.package_products ?? []).map((r) => (
                      <li key={r.products.id_product}>
                        {r.products.product_name}
                      </li>
                    ))}
                  </ul>
                </div>
                <div className="mt-auto d-flex flex-wrap gap-2">
                  <button
                    className="btn btn-sm btn-outline-secondary"
                    onClick={() => openEdit(p)}
                  >
                    Editar
                  </button>
                  <button
                    className="btn btn-sm btn-outline-danger"
                    onClick={() => handleDelete(p.id_package)}
                  >
                    Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
