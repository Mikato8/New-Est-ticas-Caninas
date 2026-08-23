import { useEffect, useState, type ChangeEvent, type FormEvent } from "react";
import { supabase } from "../../lib/supabase";
import { useAuth } from "../../context/auth";
import type { Business, Custom } from "../../types";

export default function Settings() {
  const { profile } = useAuth();
  const [businessForm, setBusinessForm] = useState({
    business_name: "",
    address: "",
    phone: "",
    email: "",
  });
  const [customForm, setCustomForm] = useState({
    main_color: "",
    secondary_color: "",
    text_color: "",
    logo: "",
  });
  const [loading, setLoading] = useState(true);
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [saved, setSaved] = useState(false);

  useEffect(() => {
    let active = true;
    async function load() {
      const idBusiness = profile?.id_business;
      const [businessRes, customRes] = await Promise.all([
        supabase
          .from("business")
          .select("*")
          .eq("id_business", idBusiness ?? 0)
          .maybeSingle(),
        supabase
          .from("custom")
          .select("*")
          .eq("id_business", idBusiness ?? 0)
          .maybeSingle(),
      ]);

      if (!active) return;

      if (businessRes.data) {
        const b = businessRes.data as Business;
        setBusinessForm({
          business_name: b.business_name,
          address: b.address ?? "",
          phone: b.phone ?? "",
          email: b.email ?? "",
        });
      }
      if (customRes.data) {
        const c = customRes.data as Custom;
        setCustomForm({
          main_color: c.main_color ?? "",
          secondary_color: c.secondary_color ?? "",
          text_color: c.text_color ?? "",
          logo: c.logo ?? "",
        });
        if (c.main_color) {
          document.documentElement.style.setProperty(
            "--brand-color",
            c.main_color,
          );
        }
        if (c.secondary_color) {
          document.documentElement.style.setProperty(
            "--brand-secondary",
            c.secondary_color,
          );
        }
        if (c.text_color) {
          document.documentElement.style.setProperty(
            "--brand-text",
            c.text_color,
          );
        }
      }
      setLoading(false);
    }

    load();
    return () => {
      active = false;
    };
  }, [profile]);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setError(null);
    setSaved(false);
    const idBusiness = profile?.id_business;

    const businessPayload = {
      business_name: businessForm.business_name,
      address: businessForm.address || null,
      phone: businessForm.phone || null,
      email: businessForm.email || null,
    };
    const customPayload = {
      main_color: customForm.main_color || null,
      secondary_color: customForm.secondary_color || null,
      text_color: customForm.text_color || null,
      logo: customForm.logo || null,
      id_business: idBusiness,
    };

    const { error: bErr } = await supabase
      .from("business")
      .update(businessPayload)
      .eq("id_business", idBusiness ?? 0);

    let cErr: { message: string } | null = null;
    const { data: existing } = await supabase
      .from("custom")
      .select("id_custom")
      .eq("id_business", idBusiness ?? 0)
      .maybeSingle();
    if (existing) {
      const res = await supabase
        .from("custom")
        .update(customPayload)
        .eq("id_business", idBusiness ?? 0);
      cErr = res.error;
    } else {
      const res = await supabase.from("custom").insert(customPayload);
      cErr = res.error;
    }

    if (bErr || cErr) {
      setError((bErr?.message ?? "") + (cErr?.message ?? ""));
      return;
    }

    if (customForm.main_color) {
      document.documentElement.style.setProperty(
        "--brand-color",
        customForm.main_color,
      );
    }
    if (customForm.secondary_color) {
      document.documentElement.style.setProperty(
        "--brand-secondary",
        customForm.secondary_color,
      );
    }
    if (customForm.text_color) {
      document.documentElement.style.setProperty(
        "--brand-text",
        customForm.text_color,
      );
    }
    setSaved(true);
  }

  async function handleLogoChange(e: ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    setError(null);
    setUploading(true);
    try {
      const idBusiness = profile?.id_business ?? 0;
      const ext = file.name.split(".").pop() ?? "png";
      const path = `${idBusiness}/logo-${Date.now()}.${ext}`;
      const { error: upErr } = await supabase.storage
        .from("logos")
        .upload(path, file, { upsert: true, contentType: file.type });
      if (upErr) throw upErr;
      const { data } = supabase.storage.from("logos").getPublicUrl(path);
      setCustomForm((prev) => ({ ...prev, logo: data.publicUrl }));
    } catch (err) {
      setError(err instanceof Error ? err.message : "No se pudo subir el logo");
    } finally {
      setUploading(false);
      e.target.value = "";
    }
  }

  function handleLogoRemove() {
    setCustomForm((prev) => ({ ...prev, logo: "" }));
  }

  if (loading) {
    return <div className="text-center text-secondary py-5">Cargando...</div>;
  }

  return (
    <div>
      <div className="mb-4">
        <h1 className="h3 mb-0">Configuración</h1>
        <p className="text-secondary mb-0">Datos del negocio y personalización</p>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}
      {saved && (
        <div className="alert alert-success">Cambios guardados correctamente</div>
      )}

      <form onSubmit={handleSubmit}>
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">Datos del negocio</h5>
            <div className="row g-3">
              <div className="col-12 col-md-6">
                <label className="form-label">Nombre</label>
                <input
                  className="form-control"
                  value={businessForm.business_name}
                  onChange={(e) =>
                    setBusinessForm({
                      ...businessForm,
                      business_name: e.target.value,
                    })
                  }
                  required
                />
              </div>
              <div className="col-12 col-md-6">
                <label className="form-label">Teléfono</label>
                <input
                  className="form-control"
                  value={businessForm.phone}
                  onChange={(e) =>
                    setBusinessForm({ ...businessForm, phone: e.target.value })
                  }
                />
              </div>
              <div className="col-12 col-md-6">
                <label className="form-label">Correo</label>
                <input
                  type="email"
                  className="form-control"
                  value={businessForm.email}
                  onChange={(e) =>
                    setBusinessForm({ ...businessForm, email: e.target.value })
                  }
                />
              </div>
              <div className="col-12 col-md-6">
                <label className="form-label">Dirección</label>
                <input
                  className="form-control"
                  value={businessForm.address}
                  onChange={(e) =>
                    setBusinessForm({ ...businessForm, address: e.target.value })
                  }
                />
              </div>
            </div>
          </div>
        </div>

        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5 className="mb-3">Personalización</h5>
            <div className="row g-3">
              <div className="col-12 col-md-4">
                <label className="form-label">Color principal</label>
                <div className="d-flex gap-2 min-w-0">
                  <input
                    type="color"
                    className="form-control form-control-color"
                    value={customForm.main_color || "#212529"}
                    onChange={(e) =>
                      setCustomForm({
                        ...customForm,
                        main_color: e.target.value,
                      })
                    }
                  />
                  <input
                    className="form-control"
                    value={customForm.main_color}
                    placeholder="#212529"
                    onChange={(e) =>
                      setCustomForm({
                        ...customForm,
                        main_color: e.target.value,
                      })
                    }
                  />
                </div>
              </div>
              <div className="col-12 col-md-4">
                <label className="form-label">Color secundario</label>
                <div className="d-flex gap-2 min-w-0">
                  <input
                    type="color"
                    className="form-control form-control-color"
                    value={customForm.secondary_color || "#ffffff"}
                    onChange={(e) =>
                      setCustomForm({
                        ...customForm,
                        secondary_color: e.target.value,
                      })
                    }
                  />
                  <input
                    className="form-control"
                    value={customForm.secondary_color}
                    placeholder="#ffffff"
                    onChange={(e) =>
                      setCustomForm({
                        ...customForm,
                        secondary_color: e.target.value,
                      })
                    }
                  />
                </div>
              </div>
              <div className="col-12 col-md-4">
                <label className="form-label">Color de letra</label>
                <div className="d-flex gap-2 min-w-0">
                  <input
                    type="color"
                    className="form-control form-control-color"
                    value={customForm.text_color || "#ffffff"}
                    onChange={(e) =>
                      setCustomForm({
                        ...customForm,
                        text_color: e.target.value,
                      })
                    }
                  />
                  <input
                    className="form-control"
                    value={customForm.text_color}
                    placeholder="#ffffff"
                    onChange={(e) =>
                      setCustomForm({
                        ...customForm,
                        text_color: e.target.value,
                      })
                    }
                  />
                </div>
              </div>
              <div className="col-12">
                <label className="form-label">Logo</label>
                <div className="d-flex flex-wrap align-items-center gap-3">
                  {customForm.logo ? (
                    <img
                      src={customForm.logo}
                      alt="Logo"
                      className="rounded border"
                      style={{
                        width: 64,
                        height: 64,
                        objectFit: "cover",
                        backgroundColor: "#fff",
                      }}
                    />
                  ) : (
                    <div
                      className="rounded border d-flex align-items-center justify-content-center text-secondary"
                      style={{ width: 64, height: 64 }}
                    >
                      Sin logo
                    </div>
                  )}
                  <div className="d-flex flex-column gap-2">
                    <input
                      type="file"
                      accept="image/*"
                      className="form-control form-control-sm"
                      onChange={handleLogoChange}
                      disabled={uploading}
                    />
                    <div className="d-flex flex-wrap gap-2">
                      {customForm.logo && (
                        <button
                          type="button"
                          className="btn btn-sm btn-outline-danger"
                          onClick={handleLogoRemove}
                        >
                          Quitar logo
                        </button>
                      )}
                      <small className="text-secondary align-self-center">
                        {uploading ? "Subiendo..." : "PNG, JPG o SVG"}
                      </small>
                    </div>
                  </div>
                </div>
                {customForm.logo && (
                  <small className="text-secondary text-break d-block mt-1">
                    {customForm.logo}
                  </small>
                )}
              </div>
            </div>
          </div>
        </div>

        <button type="submit" className="btn btn-success">
          Guardar cambios
        </button>
      </form>
    </div>
  );
}
