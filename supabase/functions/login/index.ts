import { createClient } from "jsr:@supabase/supabase-js@2";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers":
    "authorization, x-client-info, apikey, content-type",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
};

const contactEmail = "clientes@mikatoestilistascaninos.com";

function json(body: unknown, status = 200) {
  return new Response(JSON.stringify(body), {
    status,
    headers: { ...corsHeaders, "Content-Type": "application/json" },
  });
}

Deno.serve(async (req) => {
  if (req.method === "OPTIONS") {
    return new Response("ok", { headers: corsHeaders });
  }
  if (req.method !== "POST") {
    return json({ error: "Método no permitido" }, 405);
  }

  try {
    const { email, password } = await req.json();
    if (!email || !password) {
      return json({ error: "Correo y contraseña son requeridos" }, 400);
    }

    const admin = createClient(
      Deno.env.get("SUPABASE_URL") ?? "",
      Deno.env.get("SUPABASE_SERVICE_ROLE_KEY") ?? "",
      { auth: { autoRefreshToken: false, persistSession: false } },
    );

    const { data: profile, error: profileError } = await admin
      .from("users")
      .select(
        "id_user, user_name, email, id_rol, id_business, password, is_super_admin, subscription_status, access_until, login_count",
      )
      .eq("email", email)
      .maybeSingle();

    if (profileError || !profile) {
      return json({ error: "Correo o contraseña incorrectos" }, 401);
    }
    if (profile.password !== password) {
      return json({ error: "Correo o contraseña incorrectos" }, 401);
    }

    if (!profile.is_super_admin) {
      if (profile.subscription_status === "pending") {
        return json(
          {
            error:
              `Tu cuenta está pendiente de pago. Realiza tu pago mensual para activar el acceso. Contacta a ${contactEmail}`,
          },
          403,
        );
      }
      if (profile.subscription_status === "suspended") {
        return json(
          {
            error: `Tu cuenta está suspendida. Contacta a ${contactEmail}`,
          },
          403,
        );
      }
      const todayUtc = new Date().toISOString().slice(0, 10);
      if (!profile.access_until || todayUtc > profile.access_until) {
        return json(
          {
            error:
              `Tu acceso ha expirado. Renueva tu pago mensual. Contacta a ${contactEmail}`,
          },
          403,
        );
      }
    }

    const { error: createError } = await admin.auth.admin.createUser({
      email,
      password,
      email_confirm: true,
    });
    if (createError) {
      const { data: listed } = await admin.auth.admin.listUsers({
        page: 1,
        perPage: 1000,
      });
      const existing = listed?.users.find((u) => u.email === email);
      if (existing) {
        await admin.auth.admin.updateUserById(existing.id, { password });
      } else {
        return json({ error: "No se pudo iniciar sesión" }, 500);
      }
    }

    await admin
      .from("users")
      .update({
        last_login: new Date().toISOString(),
        login_count: (profile.login_count ?? 0) + 1,
      })
      .eq("id_user", profile.id_user);

    return json({
      user: {
        id_user: profile.id_user,
        user_name: profile.user_name,
        email: profile.email,
        id_rol: profile.id_rol,
        id_business: profile.id_business,
        is_super_admin: profile.is_super_admin,
      },
    });
  } catch {
    return json({ error: "Error interno del servidor" }, 500);
  }
});
