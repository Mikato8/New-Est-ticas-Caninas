---
name: testing-mikato-supabase-web
description: End-to-end testing of the Mikato Software estética canina React 19 + Bootstrap 5.3 + Supabase static app (repo New-Est-ticas-Caninas), including mobile-viewport testing against the production site mikatoestilistascaninos.com.
---

# Testing the Mikato (New-Est-ticas-Caninas) web app

## Where the app runs
- Production (static build on Apache/Hostinger): https://mikatoestilistascaninos.com/
  The first request may show a "Checking your browser before accessing" interstitial for a
  few seconds — always wait ~5-8 s after `goto` before asserting on the DOM.
- Backend is Supabase (no local API server needed); a plain `npm install && npm run dev`
  serves the SPA locally on Vite if you need an unreleased branch.
- Deep routes (`/customers`, `/appointments`, …) rely on `public/.htaccess` rewriting to
  `index.html`. If a hard reload of a deep route 404s, the `.htaccess` was not deployed
  (it must be inside the deployed build output, not only in `public/`).

## Auth
- Login page is `/` with `input[type=email]`, `input[type=password]`, `button[type=submit]`.
- Production credentials are supplied by the user (real data — treat as read-only: open
  forms/modals but never submit, and never press Eliminar / Cancelar cita).
- After login the app lands on `/home`. Session persists across hard reloads (Supabase
  stores it in localStorage), so a new browser context requires logging in again.

## Routes / nav labels (src/routes/AppRoutes.tsx, src/components/layout/AppLayout.tsx)
`/home` Inicio · `/customers` Clientes · `/pets` Mascotas · `/appointments` Citas ·
`/services` Servicios · `/packages` Paquetes · `/species` Especies · `/contracts` Contratos ·
`/sales` Ventas · `/products` Productos · `/expenses` Gastos · `/payment-methods` Métodos de
pago · `/users` Usuarios (admin) · `/settings` Configuración (admin).

## Mobile-viewport testing recipe
Connect Playwright to the visible Chrome over CDP so the run is recordable:

```python
b = p.chromium.connect_over_cdp("http://localhost:29229")
ctx = b.new_context(viewport={"width":390,"height":844}, is_mobile=True,
                    has_touch=True, device_scale_factor=2)
```
`b.new_context(...)` opens a real on-screen window sized to the viewport, so screen
recordings show the phone-sized page. Note the screenshot of the desktop is downscaled
(~0.65x), so when clicking with computer-use coordinates the 390 CSS px page occupies only
~255 screen px — clicks to the right of that land outside the page and silently do nothing.

Useful selectors for the responsive shell:
- hamburger: `[aria-label="Abrir menú de navegación"]` (`aria-expanded` reflects state)
- backdrop: `[aria-label="Cerrar menú de navegación"]`
- drawer: `nav#main-navigation`, open when it has class `sidebar-open` and
  `getBoundingClientRect().x === 0` (closed → x === -240)
- open drawer also sets `document.body.style.overflow === "hidden"`
- mobile top bar: `.mobile-topbar` (must be `display:none` at ≥992px)

Assertions that actually catch regressions:
- page-level horizontal scroll: `document.documentElement.scrollWidth === clientWidth`
  (390). Buttons inside `.table-responsive` legitimately report `right > 390`; check the
  scroll container (`scrollWidth > clientWidth`) instead of flagging them.
- the drawer keeps its internal scroll position between openings, so hard-coded screen
  coordinates for nav links go stale — prefer `#main-navigation a:has-text("Clientes")`.
- to prove a deep route was a real document load, read
  `performance.getEntriesByType('navigation')[0].type === "navigate"`.

## Devin Secrets Needed
- None for the app itself; production login credentials must be provided by the user in the
  task description.
