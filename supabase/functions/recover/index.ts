import { createClient } from "jsr:@supabase/supabase-js@2";
import { SMTPClient } from "https://deno.land/x/denomailer@1.6.0/mod.ts";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers":
    "authorization, x-client-info, apikey, content-type",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
};

const genericMessage =
  "Si el correo está registrado, te enviamos un enlace de recuperación";
const productionResetUrl =
  "https://mikatoestilistascaninos.com/reset-password";
const allowedOrigins = new Set([
  "https://mikatoestilistascaninos.com",
  "http://localhost:5173",
]);

function json(body: unknown, status = 200) {
  return new Response(JSON.stringify(body), {
    status,
    headers: { ...corsHeaders, "Content-Type": "application/json" },
  });
}

function getRedirectTo(value: unknown) {
  if (typeof value !== "string") {
    return productionResetUrl;
  }

  try {
    const requested = new URL(value);
    if (allowedOrigins.has(requested.origin)) {
      return `${requested.origin}/reset-password`;
    }
  } catch {
    return productionResetUrl;
  }

  return productionResetUrl;
}

function escapeHtml(value: string) {
  return value
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

Deno.serve(async (req) => {
  if (req.method === "OPTIONS") {
    return new Response("ok", { headers: corsHeaders });
  }
  if (req.method !== "POST") {
    return json({ error: "Método no permitido" }, 405);
  }

  try {
    const { email, redirect_to } = await req.json();
    if (!email || typeof email !== "string") {
      return json({ error: "El correo es requerido" }, 400);
    }

    const normalizedEmail = email.trim().toLowerCase();
    const redirectTo = getRedirectTo(redirect_to);
    const admin = createClient(
      Deno.env.get("SUPABASE_URL") ?? "",
      Deno.env.get("SUPABASE_SERVICE_ROLE_KEY") ?? "",
      { auth: { autoRefreshToken: false, persistSession: false } },
    );

    const { data: user, error: userError } = await admin
      .from("users")
      .select("id_user, email")
      .eq("email", normalizedEmail)
      .maybeSingle();

    if (userError) {
      throw userError;
    }
    if (!user) {
      return json({ message: genericMessage });
    }

    const { data: linkData, error: linkError } =
      await admin.auth.admin.generateLink({
        type: "recovery",
        email: normalizedEmail,
        options: { redirectTo },
      });

    if (linkError || !linkData?.properties?.action_link) {
      throw linkError ?? new Error("No se generó el enlace de recuperación");
    }

    const smtpHost = Deno.env.get("SMTP_HOST") ?? "smtp.hostinger.com";
    const smtpPort = Number(Deno.env.get("SMTP_PORT") ?? "465");
    const smtpUser =
      Deno.env.get("SMTP_USER") ?? "clientes@mikatoestilistascaninos.com";
    const smtpPass = Deno.env.get("SMTP_PASS");
    if (!smtpPass) {
      throw new Error("SMTP_PASS no está configurado");
    }

    const actionLink = linkData.properties.action_link;
    const client = new SMTPClient({
      connection: {
        hostname: smtpHost,
        port: smtpPort,
        tls: true,
        auth: {
          username: smtpUser,
          password: smtpPass,
        },
      },
    });

    try {
      await client.send({
        from: "Mikato Estilistas Caninos <clientes@mikatoestilistascaninos.com>",
        to: normalizedEmail,
        subject: "Restablece tu contraseña de Mikato",
        content: `Restablece tu contraseña de Mikato aquí: ${actionLink}\n\nEste enlace expirará pronto por seguridad.`,
        html: `
          <p>Hola,</p>
          <p>Recibimos una solicitud para restablecer tu contraseña de Mikato.</p>
          <p>
            <a href="${escapeHtml(actionLink)}">Restablecer contraseña</a>
          </p>
          <p>Por seguridad, este enlace expirará pronto.</p>
          <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
        `,
      });
    } finally {
      await client.close();
    }

    return json({ message: genericMessage });
  } catch (error) {
    console.error("Error al procesar recuperación de contraseña:", error);
    return json({ error: "No se pudo procesar la solicitud" }, 500);
  }
});
