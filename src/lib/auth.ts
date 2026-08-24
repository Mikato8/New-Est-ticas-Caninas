import { supabase } from "./supabase";
import type {
  AccountRow,
  Payment,
  SubscriptionStatus,
  UserProfile,
} from "../types";

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

export async function register(input: RegisterInput): Promise<void> {
  const res = await fetch(
    `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/register`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(input),
    },
  );

  const data = (await res.json()) as { error?: string };

  if (!res.ok) {
    throw new Error(data.error ?? "No se pudo crear la cuenta");
  }
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

async function getAccountsHeaders() {
  const { data } = await supabase.auth.getSession();
  const accessToken = data.session?.access_token;
  if (!accessToken) {
    throw new Error("No hay una sesión activa");
  }

  return {
    "Content-Type": "application/json",
    Authorization: `Bearer ${accessToken}`,
    apikey: import.meta.env.VITE_SUPABASE_ANON_KEY as string,
  };
}

async function accountsRequest<T>(body: unknown): Promise<T> {
  const res = await fetch(
    `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/accounts`,
    {
      method: "POST",
      headers: await getAccountsHeaders(),
      body: JSON.stringify(body),
    },
  );
  const data = (await res.json()) as T & { error?: string };
  if (!res.ok) {
    throw new Error(data.error ?? "No se pudo completar la solicitud");
  }
  return data;
}

export async function listAccounts(): Promise<AccountRow[]> {
  const data = await accountsRequest<{
    accounts: AccountRow[];
  }>({ action: "list" });
  return data.accounts;
}

export interface UpdateAccountInput {
  id_user: number;
  subscription_status?: SubscriptionStatus;
  access_until?: string | null;
}

export async function updateAccount(
  input: UpdateAccountInput,
): Promise<AccountRow> {
  const data = await accountsRequest<{
    account: AccountRow;
  }>({
    action: "update",
    ...input,
  });
  return data.account;
}

export async function extendAccountMonth(id_user: number): Promise<AccountRow> {
  const data = await accountsRequest<{
    account: AccountRow;
  }>({
    action: "extend_month",
    id_user,
  });
  return data.account;
}

async function paymentsRequest<T>(body: unknown): Promise<T> {
  const res = await fetch(
    `${import.meta.env.VITE_SUPABASE_URL}/functions/v1/payments`,
    {
      method: "POST",
      headers: await getAccountsHeaders(),
      body: JSON.stringify(body),
    },
  );
  const data = (await res.json()) as T & { error?: string };
  if (!res.ok) {
    throw new Error(data.error ?? "No se pudo completar la solicitud");
  }
  return data;
}

export async function listPayments(): Promise<Payment[]> {
  const data = await paymentsRequest<{ payments: Payment[] }>({
    action: "list",
  });
  return data.payments;
}

export interface CreatePaymentInput {
  id_user: number;
  amount: number;
  months: number;
  payment_date: string;
  method?: string | null;
  notes?: string | null;
}

export async function createPayment(
  input: CreatePaymentInput,
): Promise<{ access_until: string }> {
  return paymentsRequest<{ access_until: string }>({
    action: "create",
    ...input,
  });
}

export async function signOut(): Promise<void> {
  await supabase.auth.signOut();
}
