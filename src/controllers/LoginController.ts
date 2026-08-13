import { useFetch } from "../hooks/useFetch";

interface LoginProps {
    user_name: string
}
export function LoginController({user_name} : LoginProps) {

    const userExist = useFetch({
        method: "GET",
        url: "users?user_name="+user_name,
    });

    return console.log(userExist);
    
}