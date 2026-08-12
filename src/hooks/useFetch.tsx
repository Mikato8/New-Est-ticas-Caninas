import { API_URL } from "../api/config";
import { API_KEY} from "../api/config";

interface FetchProps{
    body?: BodyInit | null,
    method: string,
    url: string
}
export async function useFetch({ body, method, url} : FetchProps) {
    const response = await fetch(
        API_URL + url,
        {
            method: method,
            headers: {
                apikey: API_KEY
            },
            body: body
        }
    );

    return response.json();
}