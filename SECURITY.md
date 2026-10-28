# Seguridad

## Reportar una vulnerabilidad

Escribe a **hola@mikatoestilistascaninos.com** con:

- Descripción del problema y pasos para reproducirlo.
- Impacto estimado.
- Versión o URL afectada.
- Si lo deseas, una propuesta de solución.

No publiques la vulnerabilidad en redes sociales ni en issues públicos antes de que la atendamos. Respondemos en horario de oficina.

## Medidas implementadas

- Contraseñas con `password_hash` (bcrypt) y rehash automático.
- CSRF en todos los POST; cookies `HttpOnly`, `SameSite=Lax` y `Secure` bajo HTTPS.
- Consultas preparadas; salida escapada; Markdown sin HTML crudo.
- Validación de imágenes por MIME real, tamaño y dimensiones.
- Ejecución de PHP bloqueada en `public/uploads`.
- Límites de login, publicaciones, comentarios y citas.
- `.env` fuera de `public_html`; carpetas de código bloqueadas.
- Cabeceras CSP, HSTS, `X-Frame-Options`, `nosniff`, `Referrer-Policy`, `Permissions-Policy`.
- Registro de errores en `storage/logs` sin contraseñas ni tokens.

## Buenas prácticas para el operador

- Cambia `ADMIN_PASSWORD` después del primer acceso.
- Usa contraseñas únicas y un gestor de contraseñas.
- No compartas el acceso de administrador con todo el equipo.
- Revisa `storage/logs` y los respaldos periódicamente.
- Mantén PHP y las dependencias del hosting actualizadas.
- No subas `.env`, respaldos ni bases de datos al repositorio.
