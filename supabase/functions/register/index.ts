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

Deno.serve(async (req) => {
  if (req.method === "OPTIONS") {
    return new Response("ok", { headers: corsHeaders });
  }
  if (req.method !== "POST") {
    return json({ error: "Método no permitido" }, 405);
  }

  try {
    const { business_name, user_name, email, password } = await req.json();

    if (!business_name || !user_name || !email || !password) {
      return json({ error: "Todos los campos son requeridos" }, 400);
    }
    if (String(password).length < 6) {
      return json(
        { error: "La contraseña debe tener al menos 6 caracteres" },
        400,
      );
    }

    const normalizedEmail = String(email).trim().toLowerCase();

    const admin = createClient(
      Deno.env.get("SUPABASE_URL") ?? "",
      Deno.env.get("SUPABASE_SERVICE_ROLE_KEY") ?? "",
      { auth: { autoRefreshToken: false, persistSession: false } },
    );

    const { data: existing } = await admin
      .from("users")
      .select("id_user")
      .eq("email", normalizedEmail)
      .maybeSingle();

    if (existing) {
      return json({ error: "Ya existe una cuenta con ese correo" }, 409);
    }

    const { error: createError } = await admin.auth.admin.createUser({
      email: normalizedEmail,
      password: String(password),
      email_confirm: true,
    });

    if (createError) {
      return json({ error: "No se pudo crear la cuenta de acceso" }, 500);
    }

    const { data: business, error: businessError } = await admin
      .from("business")
      .insert({ business_name: String(business_name).trim() })
      .select("id_business, business_name")
      .single();

    if (businessError || !business) {
      return json({ error: "No se pudo crear el negocio" }, 500);
    }

    const { data: user, error: userError } = await admin
      .from("users")
      .insert({
        user_name: String(user_name).trim(),
        email: normalizedEmail,
        password: String(password),
        id_rol: 1,
        id_business: business.id_business,
      })
      .select("id_user, user_name, email, id_rol, id_business")
      .single();

    if (userError || !user) {
      return json({ error: "No se pudo crear el usuario administrador" }, 500);
    }

    return json({ user }, 201);
  } catch {
    return json({ error: "Error interno del servidor" }, 500);
  }
});
