import { useNavigate } from "react-router-dom";
import { useAuth } from "../../context/useAuth";

function Home() {
  const { user, handleLogout } = useAuth();
  const navigate = useNavigate();

  const onLogout = () => {
    handleLogout();
    navigate("/");
  };

  return (
    <div style={{ display: "flex", flexDirection: "column", alignItems: "center", justifyContent: "center", minHeight: "100vh", gap: "1rem" }}>
      <h1>Pantalla de inicio</h1>
      {user && (
        <>
          <img src={user.picture} alt={user.name} style={{ borderRadius: "50%", width: 80, height: 80 }} />
          <p>Bienvenido, {user.name}</p>
          <p>{user.email}</p>
        </>
      )}
      <button onClick={onLogout}>Cerrar sesion</button>
    </div>
  );
}

export default Home;
