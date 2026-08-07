import { LoginController } from "../../controllers/LoginController"

interface FormProps {
    user: string,
    password: string
}
export function LoginForm({ user, password }: FormProps) {
    if (user !== "" && password !== "") {
        LoginController;
    } if (user !== "" && password == "") {
        return alert("Ingrese su contraseña");
    } if (user == "" && password !== "") {
        return alert("Ingrese un usuario");
    } if (user == "" && password == "") {
        return alert("Por favor ingrese un usuario y una contraseña");
    } else {
        return alert("Error al iniciar sesión");
    }
}