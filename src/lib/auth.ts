import { supabase } from "./supabase";
import type { UserProfile } from "../types";

export async function signIn(email: string, password: string): Promise<UserProfile> {
  const res = await fetch(
    `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/login`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password }),
    },
  );

  const data = (await res.json()) as { user?: UserProfile; error?: string };

  if (!res.ok || !data.user) {
    throw new Error(data.error ?? "No se pudo iniciar sesión");
  }

  const { error } = await supabase.auth.signInWithPassword({ email, password });
  if (error) {
    throw new Error(error.message);
  }

  return data.user;
}

export async function signOut(): Promise<void> {
  await supabase.auth.signOut();
}
