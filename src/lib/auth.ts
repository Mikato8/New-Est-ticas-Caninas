import { supabase } from "./supabase";
import type { UserProfile } from "../types";

export interface RegisterInput {
  business_name: string;
  user_name: string;
  email: string;
  password: string;
}

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

export async function register(input: RegisterInput): Promise<UserProfile> {
  const res = await fetch(
    `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/register`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(input),
    },
  );

  const data = (await res.json()) as { user?: UserProfile; error?: string };

  if (!res.ok || !data.user) {
    throw new Error(data.error ?? "No se pudo crear la cuenta");
  }

  const { error } = await supabase.auth.signInWithPassword({
    email: input.email,
    password: input.password,
  });
  if (error) {
    throw new Error(error.message);
  }

  return data.user;
}

export async function requestPasswordReset(email: string): Promise<void> {
  const res = await fetch(
    `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/recover`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        email,
        redirect_to: `${window.location.origin}/reset-password`,
      }),
    },
  );

  const data = (await res.json()) as { error?: string };

  if (!res.ok) {
    throw new Error(data.error ?? "No se pudo solicitar la recuperación");
  }
}

export async function signOut(): Promise<void> {
  await supabase.auth.signOut();
}
