# Licencias de Mikato CRM

El sitio expone `POST /api/licencias/activar` para que la app de escritorio
active una clave `MKTO-XXXX-XXXX-XXXX` y reciba un token firmado con ECDSA P-256 (SHA-256).
La app verifica la firma sin conexión con la llave pública embebida: después de
activar, agenda, clientes y ventas funcionan sin internet.

## Piezas

| Pieza | Dónde vive |
|---|---|
| Registro de claves | `storage/licencias.json` (fuera de `public_html`, no se versiona) |
| Semilla de demostración | `config/licencias/demo.json` |
| Llave privada | `config/licencias/demo-privada.pem` (demostración) o `LICENCIA_PRIVATE_KEY` en el entorno |
| Llave pública | Embebida en la app (`src-tauri/src/licencia.rs`) |

## Emitir y revocar claves

```bash
php bin/licencias.php listar
php bin/licencias.php crear "Estética Luna" comercial 2027-12-31
php bin/licencias.php revocar MKTO-XXXX-XXXX-XXXX
```

La clave se entrega al cliente; el registro solo guarda la clave, el titular, el
plan, el vencimiento y el equipo que la activó. No se guardan clientes, mascotas
ni ventas del negocio.

El endpoint responde `200` con `{estado, token, plan, titular, expira_en}` y
errores `400/404/409/410/429` con `{codigo, mensaje}`. El contrato completo está
en `mikato-crm-app/docs/LICENCIA.md`.

## Antes de vender

1. Genera un par ECDSA P-256 propio (`openssl ecparam -name prime256v1 -genkey -noout`) y guarda
   la privada en el entorno del servidor (`LICENCIA_PRIVATE_KEY` en base64) o en
   `config/licencias/privada.pem`, fuera de `public_html`.
2. Reemplaza la llave pública en `src-tauri/src/licencia.rs`, recompila y firma
   los instaladores.
3. Borra la clave de demostración del registro y emite las claves reales.

La llave de demostración permite activar el build de prueba; cualquiera con el
repositorio puede firmar con ella. **No se usa para distribuir el programa.**
