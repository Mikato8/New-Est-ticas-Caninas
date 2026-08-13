import { useEffect, useState, type FormEvent } from "react";
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
    logo: "",
  });
  const [loading, setLoading] = useState(true);
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
          logo: c.logo ?? "",
        });
        if (c.main_color) {
          document.documentElement.style.setProperty(
            "--brand-color",
            c.main_color,
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
    setSaved(true);
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
                <div className="d-flex gap-2">
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
                <div className="d-flex gap-2">
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
                <label className="form-label">Logo (URL)</label>
                <input
                  className="form-control"
                  value={customForm.logo}
                  placeholder="https://..."
                  onChange={(e) =>
                    setCustomForm({ ...customForm, logo: e.target.value })
                  }
                />
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
