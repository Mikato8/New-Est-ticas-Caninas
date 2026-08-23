import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { Product } from "../../types";
import { formatMoney } from "../../lib/format";

const emptyForm = {
  product_name: "",
  purchase_price: "",
  sale_price: "",
  stock: "",
};

export default function Products() {
  const { profile } = useAuth();
  const [products, setProducts] = useState<Product[]>([]);
  const [form, setForm] = useState(emptyForm);
  const [editing, setEditing] = useState<Product | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const { data, error: err } = await supabase
      .from("products")
      .select("*")
      .order("product_name");
    if (err) setError(err.message);
    else setProducts((data as Product[]) ?? []);
  }

  useEffect(() => {
    load();
  }, []);

  function openNew() {
    setEditing(null);
    setForm(emptyForm);
    setShowForm(true);
  }

  function openEdit(p: Product) {
    setEditing(p);
    setForm({
      product_name: p.product_name,
      purchase_price: String(p.purchase_price ?? ""),
      sale_price: String(p.sale_price ?? ""),
      stock: String(p.stock ?? ""),
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
      product_name: form.product_name,
      purchase_price: Number(form.purchase_price || 0),
      sale_price: Number(form.sale_price || 0),
      stock: Number(form.stock || 0),
      id_business: profile?.id_business,
    };

    if (editing) {
      const { error: err } = await supabase
        .from("products")
        .update(payload)
        .eq("id_product", editing.id_product);
      if (err) return setError(err.message);
    } else {
      const { error: err } = await supabase.from("products").insert(payload);
      if (err) return setError(err.message);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar este producto?")) return;
    const { error: err } = await supabase
      .from("products")
      .delete()
      .eq("id_product", id);
    if (err) setError(err.message);
    load();
  }

  return (
    <div>
      <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 className="h3 mb-0">Productos</h1>
        <button className="btn btn-primary" onClick={openNew}>
          Nuevo producto
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">
              {editing ? "Editar producto" : "Nuevo producto"}
            </h5>
            <form onSubmit={handleSubmit}>
              <div className="row g-3">
                <div className="col-12 col-md-4">
                  <label className="form-label">Nombre</label>
                  <input
                    className="form-control"
                    value={form.product_name}
                    onChange={(e) =>
                      setForm({ ...form, product_name: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="col-12 col-md-2">
                  <label className="form-label">Precio compra</label>
                  <input
                    type="number"
                    min="0"
                    step="0.01"
                    className="form-control"
                    value={form.purchase_price}
                    onChange={(e) =>
                      setForm({ ...form, purchase_price: e.target.value })
                    }
                  />
                </div>
                <div className="col-12 col-md-2">
                  <label className="form-label">Precio venta</label>
                  <input
                    type="number"
                    min="0"
                    step="0.01"
                    className="form-control"
                    value={form.sale_price}
                    onChange={(e) =>
                      setForm({ ...form, sale_price: e.target.value })
                    }
                  />
                </div>
                <div className="col-12 col-md-2">
                  <label className="form-label">Stock</label>
                  <input
                    type="number"
                    min="0"
                    className="form-control"
                    value={form.stock}
                    onChange={(e) => setForm({ ...form, stock: e.target.value })}
                  />
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
                <th>Producto</th>
                <th>Precio compra</th>
                <th>Precio venta</th>
                <th>Stock</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {products.length === 0 && (
                <tr>
                  <td colSpan={5} className="text-center text-secondary py-4">
                    No hay productos registrados
                  </td>
                </tr>
              )}
              {products.map((p) => (
                <tr key={p.id_product}>
                  <td className="fw-semibold">{p.product_name}</td>
                  <td>{formatMoney(p.purchase_price)}</td>
                  <td>{formatMoney(p.sale_price)}</td>
                  <td>
                    <span
                      className={`badge ${
                        p.stock <= 5 ? "bg-danger" : "bg-success"
                      }`}
                    >
                      {p.stock} en stock
                    </span>
                  </td>
                  <td className="text-end">
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={() => openEdit(p)}
                    >
                      Editar
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={() => handleDelete(p.id_product)}
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
