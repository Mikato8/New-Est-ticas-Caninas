import { useEffect, useState, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { Business, Role, User } from "../../types";

const emptyForm = {
  user_name: "",
  email: "",
  password: "",
  id_rol: "",
  id_business: "",
};

export default function Users() {
  const { profile } = useAuth();
  const [users, setUsers] = useState<User[]>([]);
  const [roles, setRoles] = useState<Role[]>([]);
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [form, setForm] = useState(emptyForm);
  const [editing, setEditing] = useState<User | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    const [usersRes, rolesRes, businessesRes] = await Promise.all([
      supabase.from("users").select("*").order("user_name"),
      supabase.from("roles").select("*").order("id_role"),
      supabase.from("business").select("*").order("business_name"),
    ]);

    if (usersRes.error) setError(usersRes.error.message);
    else setUsers((usersRes.data as User[]) ?? []);
    if (rolesRes.data) setRoles(rolesRes.data as Role[]);
    if (businessesRes.data) setBusinesses(businessesRes.data as Business[]);
  }

  useEffect(() => {
    load();
  }, []);

  function openNew() {
    setEditing(null);
    setForm({ ...emptyForm, id_business: String(profile?.id_business ?? "") });
    setShowForm(true);
  }

  function openEdit(u: User) {
    setEditing(u);
    setForm({
      user_name: u.user_name,
      email: u.email,
      password: "",
      id_rol: String(u.id_rol),
      id_business: String(u.id_business),
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

    if (editing) {
      const payload: Record<string, string | number> = {
        user_name: form.user_name,
        email: form.email,
        id_rol: Number(form.id_rol),
        id_business: Number(form.id_business),
      };
      if (form.password) payload.password = form.password;

      const { error: err } = await supabase
        .from("users")
        .update(payload)
        .eq("id_user", editing.id_user);
      if (err) return setError(err.message);
    } else {
      const payload = {
        user_name: form.user_name,
        email: form.email,
        password: form.password,
        id_rol: Number(form.id_rol),
        id_business: Number(form.id_business),
      };
      const { error: err } = await supabase.from("users").insert(payload);
      if (err) return setError(err.message);
    }

    cancel();
    load();
  }

  async function handleDelete(id: number) {
    if (!window.confirm("¿Eliminar este usuario?")) return;
    const { error: err } = await supabase
      .from("users")
      .delete()
      .eq("id_user", id);
    if (err) setError(err.message);
    load();
  }

  function roleName(id: number): string {
    return roles.find((r) => r.id_role === id)?.rol_name ?? "—";
  }

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h1 className="h3 mb-0">Usuarios</h1>
        <button className="btn btn-primary" onClick={openNew}>
          Nuevo usuario
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">
              {editing ? "Editar usuario" : "Nuevo usuario"}
            </h5>
            <form onSubmit={handleSubmit}>
              <div className="row g-3">
                <div className="col-12 col-md-3">
                  <label className="form-label">Nombre</label>
                  <input
                    className="form-control"
                    value={form.user_name}
                    onChange={(e) =>
                      setForm({ ...form, user_name: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="col-12 col-md-3">
                  <label className="form-label">Correo</label>
                  <input
                    type="email"
                    className="form-control"
                    value={form.email}
                    onChange={(e) =>
                      setForm({ ...form, email: e.target.value })
                    }
                    required
                  />
                </div>
                <div className="col-12 col-md-2">
                  <label className="form-label">
                    Contraseña {editing && "(opcional)"}
                  </label>
                  <input
                    type="password"
                    className="form-control"
                    value={form.password}
                    onChange={(e) =>
                      setForm({ ...form, password: e.target.value })
                    }
                    required={!editing}
                  />
                </div>
                <div className="col-12 col-md-2">
                  <label className="form-label">Rol</label>
                  <select
                    className="form-select"
                    value={form.id_rol}
                    onChange={(e) =>
                      setForm({ ...form, id_rol: e.target.value })
                    }
                    required
                  >
                    <option value="">Selecciona...</option>
                    {roles.map((r) => (
                      <option key={r.id_role} value={r.id_role}>
                        {r.rol_name}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="col-12 col-md-2">
                  <label className="form-label">Negocio</label>
                  <select
                    className="form-select"
                    value={form.id_business}
                    onChange={(e) =>
                      setForm({ ...form, id_business: e.target.value })
                    }
                    required
                  >
                    <option value="">Selecciona...</option>
                    {businesses.map((b) => (
                      <option key={b.id_business} value={b.id_business}>
                        {b.business_name}
                      </option>
                    ))}
                  </select>
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
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {users.length === 0 && (
                <tr>
                  <td colSpan={4} className="text-center text-secondary py-4">
                    No hay usuarios registrados
                  </td>
                </tr>
              )}
              {users.map((u) => (
                <tr key={u.id_user}>
                  <td className="fw-semibold">{u.user_name}</td>
                  <td>{u.email}</td>
                  <td>
                    <span className="badge bg-secondary">{roleName(u.id_rol)}</span>
                  </td>
                  <td className="text-end">
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={() => openEdit(u)}
                    >
                      Editar
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={() => handleDelete(u.id_user)}
                      disabled={u.id_user === profile?.id_user}
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
