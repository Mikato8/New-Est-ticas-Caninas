import Header from "../../components/Header/Header";
import { LoginForm } from "./LoginForm";
import { useState } from "react";

function Login() {
    const [userName, setUserName] = useState<string>("");
    const [userPass, setUserPass] = useState<string>("");
    return (
        <>
            <Header title="Esteticas Caninas" />
            <div className="row d-flex justify-content-center">

                <div className="col-lg-4 col-sm-12">

                    <form className="card m-4 p-3" action="">
                        <center>
                            <h2>Inicio de Sesión</h2>
                        </center>
                        <label htmlFor="">Usuario:</label>
                        <input
                            className="form-control"
                            type="text"
                            onChange={(e) => { setUserName(e.target.value) }}
                            placeholder="Ingrese su nombre de usuario"
                        />

                        <label className="mt-4" htmlFor="">Contraseña:</label>
                        <input
                            className="form-control"
                            type="password"
                            onChange={(e) => { setUserPass(e.target.value) }}
                            placeholder="Ingrese su contraseña"
                        />

                        <input
                            className="btn btn-primary mt-4"
                            type="button"
                            value="Ingresar"
                            onClick={() =>
                                LoginForm({
                                    user: userName,
                                    password: userPass
                                })
                            }
                        />
                    </form>
                </div>
            </div>
        </>
    );
}

export default Login;