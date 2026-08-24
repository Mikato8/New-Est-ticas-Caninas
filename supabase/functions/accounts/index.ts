import { createClient } from "jsr:@supabase/supabase-js@2";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers":
    "authorization, x-client-info, apikey, content-type",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
};

const accountFields =
  "id_user, user_name, email, id_rol, id_business, business(business_name), is_active, access_until, last_login, login_count, is_super_admin";

function json(body: unknown, status = 200) {
  return new Response(JSON.stringify(body), {
    status,
    headers: { ...corsHeaders, "Content-Type": "application/json" },
  });
}

function noAuthorization() {
  return json({ error: "No autorizado" }, 403);
}

function flattenAccount(row: Record<string, unknown>) {
  const business = row.business as
    | { business_name?: string }
    | { business_name?: string }[]
    | null
    | undefined;
  const businessName = Array.isArray(business)
    ? business[0]?.business_name
    : business?.business_name;
  const { business: _business, ...account } = row;
  return { ...account, business_name: businessName ?? "—" };
}

function validDate(value: string) {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
    return false;
  }
  const parsed = new Date(`${value}T00:00:00Z`);
  return (
    !Number.isNaN(parsed.getTime()) &&
    parsed.toISOString().slice(0, 10) === value
  );
}

Deno.serve(async (req) => {
  if (req.method === "OPTIONS") {
    return new Response("ok", { headers: corsHeaders });
  }
  if (req.method !== "POST") {
    return json({ error: "Método no permitido" }, 405);
  }

  const authorization = req.headers.get("Authorization");
  const token = authorization?.match(/^Bearer\s+(.+)$/i)?.[1];
  if (!token) {
    return noAuthorization();
  }

  try {
    const admin = createClient(
      Deno.env.get("SUPABASE_URL") ?? "",
      Deno.env.get("SUPABASE_SERVICE_ROLE_KEY") ?? "",
      { auth: { autoRefreshToken: false, persistSession: false } },
    );
    const { data: authData, error: authError } =
      await admin.auth.admin.getUser(token);
    const callerEmail = authData.user?.email;
    if (authError || !callerEmail) {
      return noAuthorization();
    }

    const { data: caller, error: callerError } = await admin
      .from("users")
      .select("is_super_admin")
      .eq("email", callerEmail)
      .maybeSingle();
    if (callerError || !caller?.is_super_admin) {
      return noAuthorization();
    }

    const body = await req.json();
    if (body.action === "list") {
      const { data, error } = await admin
        .from("users")
        .select(accountFields)
        .order("id_user");
      if (error) {
        throw error;
      }
      return json({
        accounts: ((data ?? []) as Record<string, unknown>[]).map(
          flattenAccount,
        ),
      });
    }

    if (body.action !== "update") {
      return json({ error: "Acción no válida" }, 400);
    }
    if (!Number.isInteger(body.id_user)) {
      return json({ error: "El usuario es requerido" }, 400);
    }

    const hasActive = Object.prototype.hasOwnProperty.call(body, "is_active");
    const hasAccessUntil = Object.prototype.hasOwnProperty.call(
      body,
      "access_until",
    );
    if (!hasActive && !hasAccessUntil) {
      return json({ error: "No hay cambios para guardar" }, 400);
    }
    if (hasActive && typeof body.is_active !== "boolean") {
      return json({ error: "El estado no es válido" }, 400);
    }
    if (
      hasAccessUntil &&
      body.access_until !== null &&
      (typeof body.access_until !== "string" || !validDate(body.access_until))
    ) {
      return json({ error: "La fecha de acceso no es válida" }, 400);
    }

    const { data: target, error: targetError } = await admin
      .from("users")
      .select("is_super_admin")
      .eq("id_user", body.id_user)
      .maybeSingle();
    if (targetError) {
      throw targetError;
    }
    if (!target) {
      return json({ error: "Cuenta no encontrada" }, 404);
    }
    if (target.is_super_admin) {
      return json({ error: "No puedes modificar una cuenta super-admin" }, 403);
    }

    const changes: { is_active?: boolean; access_until?: string | null } = {};
    if (hasActive) {
      changes.is_active = body.is_active;
    }
    if (hasAccessUntil) {
      changes.access_until = body.access_until;
    }
    const { error: updateError } = await admin
      .from("users")
      .update(changes)
      .eq("id_user", body.id_user);
    if (updateError) {
      throw updateError;
    }

    const { data: updated, error: updatedError } = await admin
      .from("users")
      .select(accountFields)
      .eq("id_user", body.id_user)
      .single();
    if (updatedError || !updated) {
      throw updatedError ?? new Error("No se pudo cargar la cuenta actualizada");
    }

    return json({ account: flattenAccount(updated as Record<string, unknown>) });
  } catch (error) {
    console.error("Error al gestionar cuentas:", error);
    return json({ error: "Error interno del servidor" }, 500);
  }
});
