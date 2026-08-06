import Header from "../../components/Header/Header";

function Login() {
    return (
        <>
            <Header title="Esteticas Caninas" />
            <div className="row">

                <div className="col-md-4"></div>

                <form className="w-25 card m-4 p-3" action="">
                    <center>
                        <h2>Inicio de Sesión</h2>
                    </center>
                    <label htmlFor="">Usuario:</label>
                    <input className="form-control" type="text" />

                    <label className="mt-4" htmlFor="">Contraseña:</label>
                    <input className="form-control" type="password" />

                    <input className="btn btn-primary mt-4" type="button" value="Ingresar" />
                </form>
            </div>
        </>
    );
}

export default Login;