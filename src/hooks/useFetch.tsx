import { API_URL } from "../api/config";

interface FetchProps{
    header: HeadersInit,
    body?: BodyInit | null,
    method: string,
    url: string
}
export async function useFetch({header, body, method, url} : FetchProps) {
    const response = await fetch(
        API_URL + url,
        {
            method: method,
            headers: header,
            body: body
        }
    );

    return response.json();
}