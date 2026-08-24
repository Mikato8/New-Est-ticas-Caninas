import { useEffect, useState } from "react";
import { extendAccountMonth, listAccounts, updateAccount } from "../../lib/auth";
import { formatDate, formatDateTime, todayISO } from "../../lib/format";
import type { AccountRow } from "../../types";

function accountStatus(account: AccountRow) {
  if (account.subscription_status === "pending") {
    return { label: "Pendiente de pago", color: "warning" };
  }
  if (account.subscription_status === "suspended") {
    return { label: "Suspendido", color: "secondary" };
  }
  if (account.access_until && account.access_until < todayISO()) {
    return { label: "Expirado", color: "danger" };
  }
  return { label: "Activo", color: "success" };
}

export default function Accounts({ embedded = false }: { embedded?: boolean }) {
  const [accounts, setAccounts] = useState<AccountRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [savingId, setSavingId] = useState<number | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [savedId, setSavedId] = useState<number | null>(null);

  async function load() {
    setLoading(true);
    setError(null);
    try {
      setAccounts(await listAccounts());
    } catch (err) {
      setError(err instanceof Error ? err.message : "No se pudieron cargar las cuentas");
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    void load();
  }, []);

  function changeAccount(id: number, changes: Partial<AccountRow>) {
    setAccounts((current) =>
      current.map((account) =>
        account.id_user === id ? { ...account, ...changes } : account,
      ),
    );
    setSavedId(null);
  }

  async function saveAccount(account: AccountRow) {
    setSavingId(account.id_user);
    setError(null);
    setSavedId(null);
    try {
      const updated = await updateAccount({
        id_user: account.id_user,
        subscription_status: account.subscription_status,
        access_until: account.access_until || null,
      });
      setAccounts((current) =>
        current.map((item) =>
          item.id_user === updated.id_user ? updated : item,
        ),
      );
      setSavedId(account.id_user);
    } catch (err) {
      setError(err instanceof Error ? err.message : "No se pudo guardar la cuenta");
    } finally {
      setSavingId(null);
    }
  }

  async function extendMonth(account: AccountRow) {
    setSavingId(account.id_user);
    setError(null);
    setSavedId(null);
    try {
      const updated = await extendAccountMonth(account.id_user);
      setAccounts((current) =>
        current.map((item) =>
          item.id_user === updated.id_user ? updated : item,
        ),
      );
      setSavedId(account.id_user);
    } catch (err) {
      setError(err instanceof Error ? err.message : "No se pudo extender el acceso");
    } finally {
      setSavingId(null);
    }
  }

  return (
    <div>
      {!embedded && (
        <div className="mb-4">
          <h1 className="h3 mb-0">Cuentas</h1>
          <p className="text-secondary mb-0">
            Administra el acceso de las cuentas de la plataforma
          </p>
        </div>
      )}

      {error && <div className="alert alert-danger">{error}</div>}
      {savedId !== null && (
        <div className="alert alert-success">Cambios guardados correctamente</div>
      )}

      <div className="card shadow-sm">
        <div className="table-responsive">
          <table className="table table-hover align-middle mb-0">
            <thead className="table-light">
              <tr>
                <th>Correo</th>
                <th>Nombre</th>
                <th>Negocio</th>
                <th>Último acceso</th>
                <th>Accesos</th>
                <th>Estado</th>
                <th>Acceso hasta</th>
                <th className="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              {loading && (
                <tr>
                  <td colSpan={8} className="text-center text-secondary py-4">
                    Cargando cuentas...
                  </td>
                </tr>
              )}
              {!loading && accounts.length === 0 && (
                <tr>
                  <td colSpan={8} className="text-center text-secondary py-4">
                    No hay cuentas registradas
                  </td>
                </tr>
              )}
              {!loading &&
                accounts.map((account) => {
                  const status = accountStatus(account);
                  const isPending = account.subscription_status === "pending";
                  const isSuspended = account.subscription_status === "suspended";
                  return (
                    <tr key={account.id_user}>
                      <td>{account.email}</td>
                      <td className="fw-semibold">{account.user_name}</td>
                      <td>{account.business_name}</td>
                      <td>{formatDateTime(account.last_login)}</td>
                      <td>{account.login_count}</td>
                      <td>
                        <span className={`badge bg-${status.color}`}>
                          {status.label}
                        </span>
                      </td>
                      <td>
                        {account.is_super_admin ? (
                          formatDate(account.access_until)
                        ) : (
                          <input
                            type="date"
                            className="form-control form-control-sm"
                            value={account.access_until ?? ""}
                            onChange={(e) =>
                              changeAccount(account.id_user, {
                                access_until: e.target.value || null,
                              })
                            }
                            aria-label={`Acceso hasta de ${account.email}`}
                          />
                        )}
                      </td>
                      <td className="text-end">
                        {account.is_super_admin ? (
                          <span className="text-secondary small">Super-admin</span>
                        ) : (
                          <div className="d-flex justify-content-end align-items-center gap-2 flex-wrap">
                            <button
                              type="button"
                              className="btn btn-sm btn-success"
                              onClick={() => void extendMonth(account)}
                              disabled={savingId === account.id_user}
                              title="Activa y extiende el acceso por un mes"
                            >
                              +1 mes
                            </button>
                            <div className="form-check form-switch mb-0">
                              <input
                                className="form-check-input"
                                type="checkbox"
                                role="switch"
                                checked={!isPending && !isSuspended}
                                disabled={isPending}
                                onChange={(e) =>
                                  changeAccount(account.id_user, {
                                    subscription_status: e.target.checked
                                      ? "active"
                                      : "suspended",
                                  })
                                }
                                aria-label={`Suspender a ${account.email}`}
                                title={
                                  isPending
                                    ? "Usa '+1 mes' para activar la cuenta"
                                    : "Activar o suspender la cuenta"
                                }
                              />
                            </div>
                            <button
                              type="button"
                              className="btn btn-sm btn-primary"
                              onClick={() => void saveAccount(account)}
                              disabled={savingId === account.id_user}
                            >
                              {savingId === account.id_user
                                ? "Guardando..."
                                : "Guardar"}
                            </button>
                          </div>
                        )}
                      </td>
                    </tr>
                  );
                })}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
