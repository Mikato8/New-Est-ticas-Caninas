# Arquitectura

Mikato Estilistas Caninos es un portal PHP 8 sin framework: router propio, vistas PHP, PDO/HTTPS contra PostgreSQL y assets sin build step. La misma base de [Electricistas MX](https://github.com/Mikato8/Electricistas), con dominio, datos y frontend propios.

## Capas

```text
public/index.php
  └─ app/bootstrap.php
       ├─ Env + Config
       ├─ View::setBase
       ├─ Database::configure
       └─ Router (routes/web.php)
            └─ Controller
                 ├─ Domain (cálculos y contenido)
                 ├─ Repository (SQL)
                 └─ View (layout + página)
```

### Core

| Archivo | Responsabilidad |
|---|---|
| `Application` | Arranca sesión, despacha, captura errores y emite cabeceras de seguridad. |
| `Router` | Rutas GET/POST con parámetros `{slug}` y despacho a controladores. |
| `Request` / `Response` | Entrada normalizada y respuesta con cabeceras. |
| `View` | Renderiza plantillas con layout y parciales; escapa en las vistas con `e()`. |
| `Database` | Elige `PdoDatabase` o `HttpDatabase` según `DB_DRIVER`. |
| `PdoDatabase` | PostgreSQL por socket TCP, DSN compatible con Neon. Normaliza booleanos a `t`/`f`. |
| `HttpDatabase` | Respaldo por HTTPS para hosting sin salida al puerto 5432. Una sentencia por llamada. |
| `Auth` / `Session` / `Csrf` | Sesión, login, roles y tokens. |
| `Logger` | Errores a `storage/logs`. |

### Dominio de estética

- `CalculadoraShampoo`: dosis por tamaño/manto/suciedad, agua, rendimiento, costo por baño.
- `CalculadoraPrecio`: costo directo, costo fijo asignado, mano de obra, comisión, margen e IVA.
- `CalculadoraSecado`: minutos estimados, pausas, energía y alertas de seguridad.
- `CalculadoraRentabilidad`: neto, margen real, utilidad/hora, punto de equilibrio y precio meta.
- `Guias`: 12 guías de oficio en PHP versionado; secciones en Markdown seguro, FAQ y fuentes.
- `Glosario`: 32 términos agrupados por manto, herramienta, salud, manejo y negocio.

### Persistencia

Tablas principales: `usuarios`, `clientes`, `mascotas`, `servicios`, `productos`, `paquetes`, `citas`, `ventas`, `venta_items`, `gastos`, `historial_estetica`, `publicaciones`, `comentarios`, `negocios`, `contactos`, `boletin`, `configuracion`.

El esquema es idempotente (`database/schema.sql`). La migración separa sentencias porque el endpoint HTTP de Neon no acepta múltiples comandos.

### Flujo de una cita

1. El tutor elige servicio, fecha y hora en `/agenda`.
2. El controlador valida, busca o crea cliente y mascota, y verifica que el horario esté libre.
3. Se guarda la cita como `pendiente` con un código `MK-XXXXXX`.
4. El salón la confirma desde `/panel/agenda`; al completarla se agrega al historial.
5. El cliente puede consultarla en `/agenda/consultar` o en `/mi-cuenta`.

### Flujo de una venta

1. El personal arma conceptos (servicio, producto, paquete) en `/panel/ventas/nueva`.
2. El servidor recalcula importes, valida stock y cliente, y guarda en una transacción.
3. Los productos descuentan inventario; los servicios no.
4. El ticket se imprime desde `/panel/ventas/{id}` (CSS de impresión).

## Frontend

- `public/assets/css/app.css`: sistema visual completo (crema, tinta, oro Mikato, petróleo y coral).
- Menús móviles con checkbox CSS, agenda por pasos GET/POST y ventas con filas fijas de servidor.
- `PaginasController::asistente`: búsqueda léxica en PHP sobre `public/assets/ia/corpus.json`, sin JavaScript.
- `views/paginas/entrar.php`: único script inline del sitio, para alternar `password`/`text`; se autoriza con hash CSP.
- `app/Core/Confirmacion.php`: intercepta POST destructivos, muestra una página de confirmación y reenvía los campos al confirmar.
- Tipografías locales: Fraunces (títulos), Archivo (texto), IBM Plex Mono (datos).
- Ilustraciones SVG propias: portada, productos, favicon e icono social.

## Decisiones

- **Sin framework:** el hosting compartido solo necesita PHP; no hay Composer ni build.
- **Contenido editorial en PHP:** se versiona con git y no depende de la base; la comunidad sí vive en PostgreSQL.
- **Un solo salón:** el panel gestiona la estética de Mikato; el directorio es para la comunidad de estilistas. No es multi-tenant.
- **Cálculo y búsqueda en servidor:** los resultados son reproducibles y se pueden probar; no hay JavaScript ni modelos externos.
- **Datos de ejemplo marcados:** el catálogo, clientes y fichas demo se pueden borrar desde el panel.
