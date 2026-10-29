#!/usr/bin/env bash
# Despliegue a Hostinger por rsync sobre SSH.
#
# Variables:
#   SITE, REMOTE_USER, REMOTE_HOST, REMOTE_PORT, REMOTE_ROOT
#   SSH_KEY        ruta de la llave privada (recomendado)
#   SSH_PASSWORD   alternativa con sshpass (si está instalado)
#   ENV_FILE       archivo de entorno (por defecto .env.produccion)
#   SEED           1 para correr bin/seed.php después de migrar
#   BACKUP         0 para omitir el respaldo de public_html
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SITE="${SITE:-mikatoestilistascaninos.com}"
REMOTE_USER="${REMOTE_USER:-u771496034}"
REMOTE_HOST="${REMOTE_HOST:-45.93.101.150}"
REMOTE_PORT="${REMOTE_PORT:-65002}"
REMOTE_ROOT="${REMOTE_ROOT:-/home/$REMOTE_USER/domains/$SITE}"
ENV_FILE="${ENV_FILE:-$ROOT/.env.produccion}"
SEED="${SEED:-0}"
BACKUP="${BACKUP:-1}"

[[ -f "$ENV_FILE" ]] || { echo "Falta $ENV_FILE"; exit 1; }

SSH_OPTS=(-T -p "$REMOTE_PORT" -o StrictHostKeyChecking=accept-new -o ConnectTimeout=20)
SCP_OPTS=(-P "$REMOTE_PORT" -o StrictHostKeyChecking=accept-new -o ConnectTimeout=20)
if [[ -n "${SSH_KEY:-}" ]]; then
  SSH_OPTS+=(-i "$SSH_KEY")
  SCP_OPTS+=(-i "$SSH_KEY")
fi

if [[ -n "${SSH_KEY:-}" ]]; then
  SSH=(ssh "${SSH_OPTS[@]}")
  SCP=(scp "${SCP_OPTS[@]}")
  RSYNC_RSH=(ssh "${SSH_OPTS[@]}")
elif [[ -n "${SSH_PASSWORD:-}" ]]; then
  command -v sshpass >/dev/null || { echo "sshpass no está instalado"; exit 1; }
  SSH=(sshpass -p "$SSH_PASSWORD" ssh "${SSH_OPTS[@]}")
  SCP=(sshpass -p "$SSH_PASSWORD" scp "${SCP_OPTS[@]}")
  RSYNC_RSH=(sshpass -p "$SSH_PASSWORD" ssh "${SSH_OPTS[@]}")
else
  echo "Define SSH_KEY o SSH_PASSWORD"; exit 1
fi

echo "→ Verificando conexión con $REMOTE_USER@$REMOTE_HOST"
"${SSH[@]}" "$REMOTE_USER@$REMOTE_HOST" 'php -r "echo PHP_VERSION;"' | sed 's/^/  PHP remoto: /'
echo

if [[ "$BACKUP" == "1" ]]; then
  echo "→ Respaldo de public_html"
  "${SSH[@]}" "$REMOTE_USER@$REMOTE_HOST" "mkdir -p '$REMOTE_ROOT/backups' && cd '$REMOTE_ROOT' && tar -czf 'backups/public_html-$(date +%Y%m%d-%H%M%S).tar.gz' public_html 2>/dev/null || true"
fi

echo "→ Sincronizando código a $SITE"
rsync -az --delete --timeout=90 \
  --exclude '.git' --exclude '.env' --exclude '.env.*' \
  --exclude 'tests' --exclude 'docs' --exclude '.github' \
  --exclude 'public/uploads' --exclude 'storage/logs' --exclude '.well-known' \
  --exclude 'node_modules' --exclude 'vendor' --exclude '*.log' \
  -e "${RSYNC_RSH[*]}" \
  "$ROOT/" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_ROOT/public_html/"

echo "→ Subiendo .env fuera del directorio público"
"${SCP[@]}" "$ENV_FILE" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_ROOT/.env"

echo "→ Carpetas de escritura y protección de uploads"
"${SSH[@]}" "$REMOTE_USER@$REMOTE_HOST" \
  "mkdir -p '$REMOTE_ROOT/public_html/public/uploads/mascotas' '$REMOTE_ROOT/public_html/public/uploads/publicaciones' '$REMOTE_ROOT/public_html/public/uploads/negocios' '$REMOTE_ROOT/public_html/public/uploads/avatares' '$REMOTE_ROOT/public_html/public/uploads/guias' '$REMOTE_ROOT/public_html/public/uploads/productos' '$REMOTE_ROOT/public_html/public/uploads/negocio' '$REMOTE_ROOT/public_html/storage/logs' '$REMOTE_ROOT/public_html/storage/solicitudes'"

"${SCP[@]}" "$ROOT/public/uploads/.htaccess" "$REMOTE_USER@$REMOTE_HOST:$REMOTE_ROOT/public_html/public/uploads/.htaccess"

echo "→ Permisos"
"${SSH[@]}" "$REMOTE_USER@$REMOTE_HOST" \
  "chmod 600 '$REMOTE_ROOT/.env' && chmod -R u+rwX,go+rX '$REMOTE_ROOT/public_html' && chmod -R u+rwX,g+rwX '$REMOTE_ROOT/public_html/public/uploads' '$REMOTE_ROOT/public_html/storage'"

echo "→ Migración idempotente"
"${SSH[@]}" "$REMOTE_USER@$REMOTE_HOST" "cd '$REMOTE_ROOT/public_html' && php bin/migrate.php"

if [[ "$SEED" == "1" ]]; then
  echo "→ Seed inicial"
  "${SSH[@]}" "$REMOTE_USER@$REMOTE_HOST" "cd '$REMOTE_ROOT/public_html' && php bin/seed.php"
fi

echo "→ Reconstruyendo corpus del asistente"
"${SSH[@]}" "$REMOTE_USER@$REMOTE_HOST" "cd '$REMOTE_ROOT/public_html' && php bin/build-ia.php" || echo "(el corpus se puede reconstruir después)"

echo "→ Salud"
curl -fsS --max-time 20 "https://$SITE/api/salud" || echo "(revisa SSL/URL)"
echo
echo "Listo: https://$SITE"
echo "Panel: https://$SITE/panel"
