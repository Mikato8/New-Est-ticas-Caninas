/* Service worker de Mikato CRM (PWA).
   Cachea el shell y los assets para que la app abra sin internet después de
   la primera visita. Los datos viven en IndexedDB, no en este caché. */

const VERSION = "mikato-crm-pwa-1";
const BASE = "/crm/";
const SHELL = [
  BASE,
  BASE + "manifest.webmanifest",
  BASE + "icons/icon-192.png",
  BASE + "icons/icon-512.png",
];

self.addEventListener("install", (evento) => {
  evento.waitUntil(
    caches
      .open(VERSION)
      .then((cache) => cache.addAll(SHELL))
      .then(() => self.skipWaiting()),
  );
});

self.addEventListener("activate", (evento) => {
  evento.waitUntil(
    caches
      .keys()
      .then((claves) =>
        Promise.all(claves.filter((clave) => clave !== VERSION).map((clave) => caches.delete(clave))),
      )
      .then(() => self.clients.claim()),
  );
});

self.addEventListener("fetch", (evento) => {
  const solicitud = evento.request;
  if (solicitud.method !== "GET") return;
  const url = new URL(solicitud.url);
  if (url.origin !== self.location.origin) return;
  if (url.pathname.startsWith("/api/")) return;

  if (solicitud.mode === "navigate") {
    evento.respondWith(
      fetch(solicitud)
        .then((respuesta) => {
          const copia = respuesta.clone();
          caches.open(VERSION).then((cache) => cache.put(BASE, copia));
          return respuesta;
        })
        .catch(() => caches.match(BASE)),
    );
    return;
  }

  evento.respondWith(
    caches.match(solicitud).then((guardada) => {
      if (guardada) return guardada;
      return fetch(solicitud).then((respuesta) => {
        if (respuesta.ok) {
          const copia = respuesta.clone();
          caches.open(VERSION).then((cache) => cache.put(solicitud, copia));
        }
        return respuesta;
      });
    }),
  );
});
