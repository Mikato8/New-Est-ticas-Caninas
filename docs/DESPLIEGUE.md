# Despliegue en Hostinger

El sitio corre en hosting compartido con PHP 8.3 y PostgreSQL en Neon. El documento raíz es `public_html`; el código vive ahí y `.env` un nivel arriba.

## Estructura remota

```text
/home/u771496034/domains/mikatoestilistascaninos.com/
├── .env                     ← variables de producción (fuera de public_html)
├── backups/
└── public_html/
    ├── .htaccess            ← redirige a public/ y bloquea código
    ├── app/
    ├── bin/
    ├── config/
    ├── database/
    ├── public/              ← index.php, assets, corpus, uploads
    ├── routes/
    ├── storage/
    └── views/
```

## Requisitos

- PHP 8.1+ (probado en 8.3) con `pdo_pgsql`, `curl`, `mbstring`, `gd`, `fileinfo` y `json`.
- Acceso SSH o FTP.
- Base de datos PostgreSQL en Neon y su cadena `postgresql://…?sslmode=require`.
- `rsync`, `ssh` y `scp` locales (o `lftp` si solo hay FTP).

## Variables

Copia `.env.produccion` y ajusta:

```env
APP_URL=https://mikatoestilistascaninos.com
DATABASE_URL="postgresql://…"
DB_DRIVER=pdo
ADMIN_USER=admin
ADMIN_PASSWORD=una-contraseña-larga
```

- Usa `DB_DRIVER=pdo` si el hosting alcanza el puerto 5432 de Neon (este hosting sí lo hace).
- `APP_DEBUG=false` en producción.
- `APP_URL` sin slash final.
- No subas `.env` dentro de `public_html`.

## Despliegue con rsync

```bash
export SSH_KEY=~/.ssh/id_ed25519_mikato
export SITE=mikatoestilistascaninos.com
export REMOTE_USER=u771496034
export REMOTE_HOST=45.93.101.150
export REMOTE_PORT=65002
bash bin/deploy.sh
```

El script:

1. Crea un respaldo `.tar.gz` de `public_html` en `backups/`.
2. Sincroniza el código (excluye `.git`, `.env*`, tests, docs, uploads y logs).
3. Sube `.env.produccion` como `.env` al directorio superior.
4. Ajusta permisos de `public/uploads` y `storage`.
5. Ejecuta `bin/migrate.php` en el servidor.
6. Verifica `/api/salud`.

Para el primer despliegue, ejecuta también:

```bash
ssh -i ~/.ssh/id_ed25519_mikato -p 65002 u771496034@45.93.101.150 \
  'cd ~/domains/mikatoestilistascaninos.com/public_html && php bin/seed.php && php bin/build-ia.php'
```

## Despliegue por FTP (alternativa)

```bash
lftp -u 'u771496034' ftp://45.93.101.150 -e "mirror -R --exclude .git --exclude .env* --exclude docs --exclude tests ./public_html; bye"
```

Sube `.env.produccion` como `.env` al directorio superior con el administrador de archivos de Hostinger.

## SSL, DNS y correo

1. Apunta `mikatoestilistascaninos.com` al hosting y espera la propagación.
2. Activa SSL gratuito en el panel y fuerza HTTPS.
3. Verifica que `APP_URL` use `https://`.
4. Crea el buzón de `CONTACT_EMAIL` y activa `MAIL_ENABLED=true`.
5. Revisa `robots.txt` y `sitemap.xml` con el dominio final.

## Verificación

- `GET /api/salud` responde `200` y `"ok": true`.
- `/` carga con estilos y sin errores en `storage/logs`.
- Login de administrador y `/panel` responden.
- Una calculadora devuelve resultado y la API responde JSON.
- Eliminar un registro del panel muestra la página de confirmación PHP y solo borra al confirmar.
- En `/entrar`, “Ver” muestra la contraseña y “Ocultar” vuelve a taparla.
- `/sitemap.xml` lista guías, comunidad y directorio.
- La imagen de `public/uploads` se sirve y no ejecuta PHP.
- El asistente busca en el corpus desde PHP, sin JavaScript ni llamadas externas.

## Respaldos

- La base vive en Neon: usa su historial de ramas/restauración.
- `public/uploads` y `storage/logs` viven en el hosting: respáldalos por separado.
- El script deja un respaldo de `public_html` antes de cada despliegue; rota los antiguos.

## Problemas comunes

| Síntoma | Causa probable | Solución |
|---|---|---|
| 500 al cargar | `.env` mal ubicado o sin `DATABASE_URL` | Revisa la ruta y `php bin/check.php`. |
| CSS sin cargar | `APP_URL` apunta a otro host | Corrige `APP_URL` y recarga. |
| `pdo_pgsql` no conecta | Puerto 5432 bloqueado | Usa `DB_DRIVER=http`. |
| Migración falla | Una sentencia no es idempotente | Revisa el mensaje y el SQL. |
| Uploads no escriben | Permisos | `chmod -R u+rwX,g+rwX public/uploads`. |
| Sesión no persiste | HTTPS/proxy | Revisa `X-Forwarded-Proto` y cookies. |
| Correo no llega | Buzón no creado o `MAIL_ENABLED=false` | Crea el buzón y activa el envío. |
