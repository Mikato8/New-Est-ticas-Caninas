export function formatMoney(value: number | null | undefined): string {
  if (value === null || value === undefined) return "—";
  return new Intl.NumberFormat("es-MX", {
    style: "currency",
    currency: "MXN",
  }).format(value);
}

export function formatDate(date: string | null | undefined): string {
  if (!date) return "—";
  const d = new Date(`${date}T00:00:00`);
  if (Number.isNaN(d.getTime())) return date;
  return d.toLocaleDateString("es-MX", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

export function formatDateTime(iso: string | null | undefined): string {
  if (!iso) return "—";
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return iso;
  return d.toLocaleString("es-MX", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

export function todayISO(): string {
  return new Date().toISOString().slice(0, 10);
}

export function monthStartISO(): string {
  const now = new Date();
  const y = now.getFullYear();
  const m = String(now.getMonth() + 1).padStart(2, "0");
  return `${y}-${m}-01`;
}

export const serviceSizeLabels: Record<string, string> = {
  small: "Chico",
  medium: "Mediano",
  large: "Grande",
  xl: "Extra grande",
};

export const saleStatusLabels: Record<string, string> = {
  pending: "Pendiente",
  paid: "Pagado",
  cancelled: "Cancelado",
  refunded: "Reembolsado",
};

export const saleStatusBadge: Record<string, string> = {
  pending: "warning",
  paid: "success",
  cancelled: "secondary",
  refunded: "info",
};
