import { createClient } from "jsr:@supabase/supabase-js@2";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers":
    "authorization, x-client-info, apikey, content-type",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
};

function json(body: unknown, status = 200) {
  return new Response(JSON.stringify(body), {
    status,
    headers: { ...corsHeaders, "Content-Type": "application/json" },
  });
}

function noAuthorization() {
  return json({ error: "No autorizado" }, 403);
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

function todayUtc() {
  return new Date().toISOString().slice(0, 10);
}

function addMonths(iso: string, months: number) {
  const d = new Date(`${iso}T00:00:00Z`);
  d.setUTCMonth(d.getUTCMonth() + months);
  return d.toISOString().slice(0, 10);
}

type UserInfo = { user_name: string; email: string; business_name: string };

function businessNameOf(row: Record<string, unknown>) {
  const business = row.business as
    | { business_name?: string }
    | { business_name?: string }[]
    | null
    | undefined;
  return Array.isArray(business)
    ? business[0]?.business_name ?? "—"
    : business?.business_name ?? "—";
}

function buildUserMap(rows: Record<string, unknown>[]) {
  const map = new Map<number, UserInfo>();
  for (const row of rows) {
    map.set(row.id_user as number, {
      user_name: row.user_name as string,
      email: row.email as string,
      business_name: businessNameOf(row),
    });
  }
  return map;
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
      await admin.auth.getUser(token);
    const callerEmail = authData.user?.email;
    if (authError || !callerEmail) {
      return noAuthorization();
    }

    const { data: caller, error: callerError } = await admin
      .from("users")
      .select("id_user, is_super_admin")
      .eq("email", callerEmail)
      .maybeSingle();
    if (callerError || !caller?.is_super_admin) {
      return noAuthorization();
    }

    const body = await req.json();

    if (body.action === "list") {
      const [paymentsRes, usersRes] = await Promise.all([
        admin
          .from("payments")
          .select("*")
          .order("payment_date", { ascending: false })
          .order("id_payment", { ascending: false }),
        admin
          .from("users")
          .select("id_user, user_name, email, business(business_name)"),
      ]);
      if (paymentsRes.error) {
        throw paymentsRes.error;
      }
      if (usersRes.error) {
        throw usersRes.error;
      }

      const users = buildUserMap(
        (usersRes.data ?? []) as Record<string, unknown>[],
      );

      const payments = ((paymentsRes.data ?? []) as Record<string, unknown>[]).map(
        (p) => {
          const payer = users.get(p.id_user as number);
          const creator = users.get(p.created_by as number);
          return {
            id_payment: p.id_payment,
            id_user: p.id_user,
            amount: Number(p.amount),
            months: Number(p.months),
            payment_date: p.payment_date,
            method: p.method,
            notes: p.notes,
            created_by: p.created_by,
            created_at: p.created_at,
            user_email: payer?.email ?? null,
            user_name: payer?.user_name ?? null,
            business_name: payer?.business_name ?? null,
            created_by_name: creator?.user_name ?? null,
          };
        },
      );

      return json({ payments });
    }

    if (body.action !== "create") {
      return json({ error: "Acción no válida" }, 400);
    }

    if (!Number.isInteger(body.id_user)) {
      return json({ error: "Selecciona una cuenta" }, 400);
    }
    const amount = Number(body.amount);
    if (!Number.isFinite(amount) || amount < 0) {
      return json({ error: "El monto no es válido" }, 400);
    }
    const months = Number(body.months);
    if (!Number.isInteger(months) || months < 1) {
      return json({ error: "La cantidad de meses no es válida" }, 400);
    }
    const paymentDate =
      typeof body.payment_date === "string" && body.payment_date
        ? body.payment_date
        : todayUtc();
    if (!validDate(paymentDate)) {
      return json({ error: "La fecha de pago no es válida" }, 400);
    }
    const method = typeof body.method === "string" ? body.method.trim() : null;
    const notes = typeof body.notes === "string" ? body.notes.trim() : null;

    const { data: target, error: targetError } = await admin
      .from("users")
      .select("id_user, is_super_admin, subscription_status, access_until")
      .eq("id_user", body.id_user)
      .maybeSingle();
    if (targetError) {
      throw targetError;
    }
    if (!target) {
      return json({ error: "Cuenta no encontrada" }, 404);
    }
    if (target.is_super_admin) {
      return json(
        { error: "No puedes registrar pagos de una cuenta super-admin" },
        403,
      );
    }

    const base =
      target.access_until && target.access_until >= todayUtc()
        ? target.access_until
        : todayUtc();
    const access_until = addMonths(base, months);

    const { data: payment, error: insertError } = await admin
      .from("payments")
      .insert({
        id_user: body.id_user,
        amount,
        months,
        payment_date: paymentDate,
        method,
        notes,
        created_by: caller.id_user,
      })
      .select("*")
      .single();
    if (insertError || !payment) {
      throw insertError ?? new Error("No se pudo registrar el pago");
    }

    const { error: updateError } = await admin
      .from("users")
      .update({ subscription_status: "active", access_until })
      .eq("id_user", body.id_user);
    if (updateError) {
      throw updateError;
    }

    return json({
      payment: {
        id_payment: payment.id_payment,
        id_user: payment.id_user,
        amount: Number(payment.amount),
        months: Number(payment.months),
        payment_date: payment.payment_date,
        method: payment.method,
        notes: payment.notes,
        created_by: payment.created_by,
        created_at: payment.created_at,
      },
      access_until,
    });
  } catch (error) {
    console.error("Error al gestionar pagos:", error);
    return json({ error: "Error interno del servidor" }, 500);
  }
});
