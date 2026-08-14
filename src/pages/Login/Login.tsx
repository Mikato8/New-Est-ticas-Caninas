import { useState, type FormEvent } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../../context/auth";

type Mode = "login" | "register";

export default function Login() {
  const { signIn, signUp } = useAuth();
  const navigate = useNavigate();
  const [mode, setMode] = useState<Mode>("login");
  const [businessName, setBusinessName] = useState("");
  const [userName, setUserName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  function switchMode(next: Mode) {
    setMode(next);
    setError(null);
  }

  async function handleLogin(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setSubmitting(true);
    try {
      await signIn(email, password);
      navigate("/home", { replace: true });
    } catch (err) {
      setError(err instanceof Error ? err.message : "Error al iniciar sesión");
    } finally {
      setSubmitting(false);
    }
  }

  async function handleRegister(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setSubmitting(true);
    try {
      await signUp({
        business_name: businessName,
        user_name: userName,
        email,
        password,
      });
      navigate("/home", { replace: true });
    } catch (err) {
      setError(err instanceof Error ? err.message : "Error al registrarse");
    } finally {
      setSubmitting(false);
    }
  }

  return (
    <div className="d-flex justify-content-center align-items-center vh-100 bg-light">
      <div className="card shadow" style={{ width: "24rem", maxWidth: "92vw" }}>
        <div className="card-body p-4">
          <div className="text-center mb-4">
            <div
              className="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
              style={{
                width: 56,
                height: 56,
                fontSize: "1.6rem",
                backgroundColor: "var(--brand-color, #6c5ce7)",
              }}
            >
              M
            </div>
            <h4 className="fw-bold mb-1">Mikato Software</h4>
            <p className="text-secondary mb-0 small">
              Sistema de gestión para estética canina
            </p>
          </div>

          <ul className="nav nav-pills nav-fill mb-4">
            <li className="nav-item">
              <button
                type="button"
                className={`nav-link ${mode === "login" ? "active" : ""}`}
                onClick={() => switchMode("login")}
              >
                Iniciar sesión
              </button>
            </li>
            <li className="nav-item">
              <button
                type="button"
                className={`nav-link ${mode === "register" ? "active" : ""}`}
                onClick={() => switchMode("register")}
              >
                Registrarse
              </button>
            </li>
          </ul>

          {error && <div className="alert alert-danger py-2">{error}</div>}

          {mode === "login" ? (
            <form onSubmit={handleLogin}>
              <div className="mb-3">
                <label htmlFor="email" className="form-label">
                  Correo
                </label>
                <input
                  id="email"
                  type="email"
                  className="form-control"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  autoFocus
                />
              </div>
              <div className="mb-3">
                <label htmlFor="password" className="form-label">
                  Contraseña
                </label>
                <input
                  id="password"
                  type="password"
                  className="form-control"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                />
              </div>
              <button
                type="submit"
                className="btn btn-primary w-100"
                disabled={submitting}
              >
                {submitting ? "Entrando..." : "Entrar"}
              </button>
            </form>
          ) : (
            <form onSubmit={handleRegister}>
              <div className="mb-3">
                <label htmlFor="businessName" className="form-label">
                  Nombre del negocio
                </label>
                <input
                  id="businessName"
                  type="text"
                  className="form-control"
                  value={businessName}
                  onChange={(e) => setBusinessName(e.target.value)}
                  required
                  autoFocus
                />
              </div>
              <div className="mb-3">
                <label htmlFor="userName" className="form-label">
                  Tu nombre
                </label>
                <input
                  id="userName"
                  type="text"
                  className="form-control"
                  value={userName}
                  onChange={(e) => setUserName(e.target.value)}
                  required
                />
              </div>
              <div className="mb-3">
                <label htmlFor="regEmail" className="form-label">
                  Correo
                </label>
                <input
                  id="regEmail"
                  type="email"
                  className="form-control"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                />
              </div>
              <div className="mb-3">
                <label htmlFor="regPassword" className="form-label">
                  Contraseña
                </label>
                <input
                  id="regPassword"
                  type="password"
                  className="form-control"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  minLength={6}
                  required
                />
                <div className="form-text">Mínimo 6 caracteres</div>
              </div>
              <button
                type="submit"
                className="btn btn-primary w-100"
                disabled={submitting}
              >
                {submitting ? "Creando cuenta..." : "Crear cuenta de administrador"}
              </button>
            </form>
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
