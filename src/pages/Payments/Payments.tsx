import { useEffect, useMemo, useState, type FormEvent } from "react";
import { createPayment, listAccounts, listPayments } from "../../lib/auth";
import { formatDate, formatDateTime, formatMoney, todayISO } from "../../lib/format";
import type { AccountRow, Payment } from "../../types";

const paymentMethods = [
  "Efectivo",
  "Transferencia",
  "Tarjeta",
  "Depósito",
  "Otro",
];

function accountLabel(account: AccountRow) {
  const status =
    account.subscription_status === "pending"
      ? "Pendiente de pago"
      : account.subscription_status === "suspended"
        ? "Suspendido"
        : account.access_until && account.access_until < todayISO()
          ? "Expirado"
          : "Activo";
  return `${account.email} — ${account.business_name} (${status})`;
}

export default function Payments() {
  const [accounts, setAccounts] = useState<AccountRow[]>([]);
  const [payments, setPayments] = useState<Payment[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [saved, setSaved] = useState<string | null>(null);

  const [idUser, setIdUser] = useState<string>("");
  const [amount, setAmount] = useState<string>("");
  const [months, setMonths] = useState<string>("1");
  const [paymentDate, setPaymentDate] = useState(todayISO());
  const [method, setMethod] = useState(paymentMethods[1]);
  const [notes, setNotes] = useState("");
  const [submitting, setSubmitting] = useState(false);

  async function load() {
    setLoading(true);
    setError(null);
    try {
      const [accountsRes, paymentsRes] = await Promise.all([
        listAccounts(),
        listPayments(),
      ]);
      setAccounts(accountsRes);
      setPayments(paymentsRes);
    } catch (err) {
      setError(err instanceof Error ? err.message : "No se pudieron cargar los cobros");
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    void load();
  }, []);

  const selectable = useMemo(
    () => accounts.filter((account) => !account.is_super_admin),
    [accounts],
  );

  const totalCollected = useMemo(
    () => payments.reduce((sum, payment) => sum + Number(payment.amount), 0),
    [payments],
  );

  const monthStart = todayISO().slice(0, 8) + "01";
  const collectedThisMonth = useMemo(
    () =>
      payments
        .filter((payment) => payment.payment_date && payment.payment_date >= monthStart)
        .reduce((sum, payment) => sum + Number(payment.amount), 0),
    [payments, monthStart],
  );

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setSaved(null);

    const parsedAmount = Number(amount);
    const parsedMonths = Number(months);
    if (!idUser) {
      setError("Selecciona una cuenta");
      return;
    }
    if (!Number.isFinite(parsedAmount) || parsedAmount < 0) {
      setError("Ingresa un monto válido");
      return;
    }
    if (!Number.isInteger(parsedMonths) || parsedMonths < 1) {
      setError("Ingresa una cantidad de meses válida");
      return;
    }

    setSubmitting(true);
    try {
      const result = await createPayment({
        id_user: Number(idUser),
        amount: parsedAmount,
        months: parsedMonths,
        payment_date: paymentDate || todayISO(),
        method: method || null,
        notes: notes.trim() || null,
      });
      setSaved(
        `Pago registrado. Acceso activo hasta ${formatDate(result.access_until)}.`,
      );
      setIdUser("");
      setAmount("");
      setMonths("1");
      setPaymentDate(todayISO());
      setNotes("");
      await load();
    } catch (err) {
      setError(err instanceof Error ? err.message : "No se pudo registrar el pago");
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <div>
      <div className="mb-4">
        <h1 className="h3 mb-0">Cobros</h1>
        <p className="text-secondary mb-0">
          Registra pagos manuales y da de alta el acceso de las cuentas
        </p>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}
      {saved && <div className="alert alert-success">{saved}</div>}

      <div className="row g-3 mb-4">
        <div className="col-6 col-md-3">
          <div className="card shadow-sm h-100">
            <div className="card-body">
              <div className="text-secondary small text-uppercase">Cobrado este mes</div>
              <div className="h4 fw-bold text-success mb-0">
                {formatMoney(collectedThisMonth)}
              </div>
            </div>
          </div>
        </div>
        <div className="col-6 col-md-3">
          <div className="card shadow-sm h-100">
            <div className="card-body">
              <div className="text-secondary small text-uppercase">Cobrado total</div>
              <div className="h4 fw-bold mb-0">{formatMoney(totalCollected)}</div>
            </div>
          </div>
        </div>
      </div>

      <div className="card shadow-sm mb-4">
        <div className="card-body">
          <h5 className="mb-3">Registrar pago y dar de alta</h5>
          <form onSubmit={handleSubmit}>
            <div className="row g-3">
              <div className="col-12 col-md-6">
                <label htmlFor="account" className="form-label">
                  Cuenta
                </label>
                <select
                  id="account"
                  className="form-select"
                  value={idUser}
                  onChange={(e) => setIdUser(e.target.value)}
                  required
                >
                  <option value="">Selecciona una cuenta...</option>
                  {selectable.map((account) => (
                    <option key={account.id_user} value={account.id_user}>
                      {accountLabel(account)}
                    </option>
                  ))}
                </select>
              </div>
              <div className="col-6 col-md-2">
                <label htmlFor="amount" className="form-label">
                  Monto
                </label>
                <input
                  id="amount"
                  type="number"
                  className="form-control"
                  value={amount}
                  onChange={(e) => setAmount(e.target.value)}
                  min="0"
                  step="0.01"
                  placeholder="0.00"
                  required
                />
              </div>
              <div className="col-6 col-md-1">
                <label htmlFor="months" className="form-label">
                  Meses
                </label>
                <input
                  id="months"
                  type="number"
                  className="form-control"
                  value={months}
                  onChange={(e) => setMonths(e.target.value)}
                  min="1"
                  step="1"
                  required
                />
              </div>
              <div className="col-6 col-md-3">
                <label htmlFor="paymentDate" className="form-label">
                  Fecha
                </label>
                <input
                  id="paymentDate"
                  type="date"
                  className="form-control"
                  value={paymentDate}
                  onChange={(e) => setPaymentDate(e.target.value)}
                  required
                />
              </div>
              <div className="col-6 col-md-3">
                <label htmlFor="method" className="form-label">
                  Método
                </label>
                <select
                  id="method"
                  className="form-select"
                  value={method}
                  onChange={(e) => setMethod(e.target.value)}
                >
                  {paymentMethods.map((option) => (
                    <option key={option} value={option}>
                      {option}
                    </option>
                  ))}
                </select>
              </div>
              <div className="col-12 col-md-9">
                <label htmlFor="notes" className="form-label">
                  Notas
                </label>
                <input
                  id="notes"
                  type="text"
                  className="form-control"
                  value={notes}
                  onChange={(e) => setNotes(e.target.value)}
                  placeholder="Referencia, concepto, etc."
                />
              </div>
              <div className="col-12 col-md-3 d-flex align-items-end">
                <button
                  type="submit"
                  className="btn btn-primary w-100"
                  disabled={submitting}
                >
                  {submitting ? "Registrando..." : "Registrar pago y dar de alta"}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div className="card shadow-sm">
        <div className="card-body">
          <h5 className="mb-3">Historial de cobros</h5>
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light">
                <tr>
                  <th>Fecha</th>
                  <th>Cuenta</th>
                  <th>Negocio</th>
                  <th className="text-end">Monto</th>
                  <th className="text-center">Meses</th>
                  <th>Método</th>
                  <th>Notas</th>
                  <th>Registrado por</th>
                </tr>
              </thead>
              <tbody>
                {loading && (
                  <tr>
                    <td colSpan={8} className="text-center text-secondary py-4">
                      Cargando cobros...
                    </td>
                  </tr>
                )}
                {!loading && payments.length === 0 && (
                  <tr>
                    <td colSpan={8} className="text-center text-secondary py-4">
                      No hay cobros registrados
                    </td>
                  </tr>
                )}
                {!loading &&
                  payments.map((payment) => (
                    <tr key={payment.id_payment}>
                      <td>{formatDate(payment.payment_date)}</td>
                      <td className="fw-semibold">{payment.user_email}</td>
                      <td>{payment.business_name}</td>
                      <td className="text-end fw-semibold">
                        {formatMoney(payment.amount)}
                      </td>
                      <td className="text-center">{payment.months}</td>
                      <td>{payment.method ?? "—"}</td>
                      <td className="text-secondary">{payment.notes ?? "—"}</td>
                      <td className="text-secondary small">
                        {payment.created_by_name
                          ? `${payment.created_by_name} · ${formatDateTime(payment.created_at)}`
                          : formatDateTime(payment.created_at)}
                      </td>
                    </tr>
                  ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}
