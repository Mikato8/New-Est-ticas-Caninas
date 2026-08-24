import { useEffect, useState, type FormEvent } from "react";
import { Link, useNavigate } from "react-router-dom";
import { supabase } from "../../lib/supabase";

type ResetStatus = "checking" | "ready" | "success" | "invalid";

function hasRecoveryTypeInUrl() {
  const hashParams = new URLSearchParams(window.location.hash.slice(1));
  const searchParams = new URLSearchParams(window.location.search);
  return (
    hashParams.get("type") === "recovery" ||
    searchParams.get("type") === "recovery"
  );
}

export default function ResetPassword() {
  const navigate = useNavigate();
  const [status, setStatus] = useState<ResetStatus>("checking");
  const [password, setPassword] = useState("");
  const [confirmation, setConfirmation] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    let mounted = true;
    let recoveryEventReceived = false;
    const recoveryUrl = hasRecoveryTypeInUrl();

    const { data } = supabase.auth.onAuthStateChange((event, session) => {
      if (event === "PASSWORD_RECOVERY" && session) {
        recoveryEventReceived = true;
        if (mounted) {
          setStatus("ready");
        }
      }
    });

    async function resolveSession() {
      const { data: sessionData } = await supabase.auth.getSession();
      if (!mounted) {
        return;
      }

      if (sessionData.session && recoveryEventReceived) {
        setStatus("ready");
        return;
      }

      if (!recoveryUrl && !recoveryEventReceived) {
        setStatus("invalid");
        return;
      }

      window.setTimeout(async () => {
        const { data: delayedSessionData } = await supabase.auth.getSession();
        if (!mounted) {
          return;
        }
        setStatus(
          delayedSessionData.session && recoveryEventReceived
            ? "ready"
            : "invalid",
        );
      }, 1500);
    }

    void resolveSession();

    return () => {
      mounted = false;
      data.subscription.unsubscribe();
    };
  }, []);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);

    if (password.length < 6) {
      setError("La contraseña debe tener al menos 6 caracteres");
      return;
    }
    if (password !== confirmation) {
      setError("Las contraseñas no coinciden");
      return;
    }

    setSubmitting(true);
    try {
      const { error: updateError } = await supabase.auth.updateUser({
        password,
      });
      if (updateError) {
        throw new Error(updateError.message);
      }
      setStatus("success");
    } catch (err) {
      setError(
        err instanceof Error
          ? err.message
          : "No se pudo actualizar la contraseña",
      );
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <div className="d-flex justify-content-center align-items-center vh-100 bg-light">
      <div className="card shadow" style={{ width: "24rem", maxWidth: "92vw" }}>
        <div className="card-body p-4">
          <div className="text-center mb-4">
            <img
              src="/logo-mikato.png"
              alt="Mikato"
              className="mx-auto mb-3 d-block"
              style={{ maxWidth: "14rem", width: "80%", height: "auto" }}
            />
            <h4 className="fw-bold mb-1">Restablecer contraseña</h4>
            <p className="text-secondary mb-0 small">
              Mikato Software
            </p>
          </div>

          {status === "checking" && (
            <div className="text-center text-secondary">
              <div className="spinner-border spinner-border-sm me-2" role="status">
                <span className="visually-hidden">Comprobando enlace...</span>
              </div>
              Comprobando enlace...
            </div>
          )}

          {status === "invalid" && (
            <div className="text-center">
              <div className="alert alert-danger">
                El enlace de recuperación no es válido o ha expirado.
              </div>
              <Link to="/" className="btn btn-link p-0">
                Volver al inicio de sesión
              </Link>
            </div>
          )}

          {status === "ready" && (
            <form onSubmit={handleSubmit}>
              {error && <div className="alert alert-danger py-2">{error}</div>}
              <div className="mb-3">
                <label htmlFor="newPassword" className="form-label">
                  Nueva contraseña
                </label>
                <input
                  id="newPassword"
                  type="password"
                  className="form-control"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  minLength={6}
                  required
                  autoFocus
                />
              </div>
              <div className="mb-3">
                <label htmlFor="confirmPassword" className="form-label">
                  Confirmar contraseña
                </label>
                <input
                  id="confirmPassword"
                  type="password"
                  className="form-control"
                  value={confirmation}
                  onChange={(e) => setConfirmation(e.target.value)}
                  minLength={6}
                  required
                />
              </div>
              <button
                type="submit"
                className="btn btn-primary w-100"
                disabled={submitting}
              >
                {submitting ? "Guardando..." : "Restablecer contraseña"}
              </button>
            </form>
          )}

          {status === "success" && (
            <div className="text-center">
              <div className="alert alert-success">
                Tu contraseña se actualizó correctamente.
              </div>
              <button
                type="button"
                className="btn btn-primary w-100"
                onClick={() => navigate("/home", { replace: true })}
              >
                Ir al inicio
              </button>
            </div>
          )}
        </div>

        <div className="card-footer text-center text-secondary small bg-white">
          © {new Date().getFullYear()} Mikato Software. Todos los derechos
          reservados.
        </div>
      </div>
    </div>
  );
}
