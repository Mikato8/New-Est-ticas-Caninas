import { useNavigate } from "react-router-dom";
import { GoogleLogin } from "@react-oauth/google";
import { useAuth } from "../../context/useAuth";
import type { CredentialResponse } from "@react-oauth/google";

function Login() {
  const navigate = useNavigate();
  const { handleGoogleLogin } = useAuth();

  const onSuccess = (response: CredentialResponse) => {
    handleGoogleLogin(response);
    navigate("/home");
  };

  return (
    <div style={{ display: "flex", flexDirection: "column", alignItems: "center", justifyContent: "center", minHeight: "100vh", gap: "1rem" }}>
      <h1>Nuevas Esteticas Caninas</h1>
      <p>Inicia sesion para continuar</p>
      <GoogleLogin
        onSuccess={onSuccess}
        onError={() => console.error("Error al iniciar sesion con Google")}
        size="large"
        shape="pill"
        text="signin_with"

      />
    </div>
  );
}

export default Login;
