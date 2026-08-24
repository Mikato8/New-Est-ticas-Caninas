import { useState } from "react";
import Accounts from "../Accounts/Accounts";
import Payments from "../Payments/Payments";

type Tab = "accounts" | "payments";

export default function Admin() {
  const [tab, setTab] = useState<Tab>("accounts");

  return (
    <div>
      <div className="mb-4">
        <h1 className="h3 mb-0">Administración</h1>
        <p className="text-secondary mb-0">
          Control de accesos y pagos de la plataforma
        </p>
      </div>

      <ul className="nav nav-pills mb-4">
        <li className="nav-item">
          <button
            type="button"
            className={`nav-link ${tab === "accounts" ? "active" : ""}`}
            onClick={() => setTab("accounts")}
          >
            Accesos
          </button>
        </li>
        <li className="nav-item">
          <button
            type="button"
            className={`nav-link ${tab === "payments" ? "active" : ""}`}
            onClick={() => setTab("payments")}
          >
            Cobros
          </button>
        </li>
      </ul>

      <div className={tab === "accounts" ? "" : "d-none"}>
        <Accounts embedded />
      </div>
      <div className={tab === "payments" ? "" : "d-none"}>
        <Payments embedded />
      </div>
    </div>
  );
}
