import { Navigate, Outlet } from "react-router-dom";
import { useAuth } from "../context/auth";

export default function ProtectedRoute() {
  const { profile, loading } = useAuth();

  if (loading) {
    return (
      <div className="d-flex justify-content-center align-items-center vh-100">
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Cargando...</span>
        </div>
      </div>
    );
  }

  if (!profile) {
    return <Navigate to="/" replace />;
  }

  return <Outlet />;
}
