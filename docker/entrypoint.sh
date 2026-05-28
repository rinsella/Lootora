#!/usr/bin/env bash
# Lootora container entrypoint
# Safe to run on every boot — no destructive ops by default.
set -e

cd /var/www/html

log() { echo "[lootora-entrypoint] $*"; }

# ---------- 1. Bootstrap .env ----------
if [[ ! -f .env ]]; then
    if [[ -f .env.docker.example ]]; then
        log "No .env found, copying from .env.docker.example"
        cp .env.docker.example .env
    elif [[ -f .env.example ]]; then
        log "No .env found, copying from .env.example"
        cp .env.example .env
    fi
fi

# ---------- 2. APP_KEY ----------
if ! grep -qE '^APP_KEY=.+' .env 2>/dev/null || grep -qE '^APP_KEY=$' .env 2>/dev/null; then
    log "Generating APP_KEY"
    php artisan key:generate --force || true
fi

# ---------- 3. Wait for MySQL ----------
DB_CONNECTION="${DB_CONNECTION:-mysql}"
if [[ "$DB_CONNECTION" == "mysql" ]]; then
    DB_HOST="${DB_HOST:-mysql}"
    DB_PORT="${DB_PORT:-3306}"
    log "Waiting for MySQL at ${DB_HOST}:${DB_PORT} ..."
    tries=0
    until mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u"${DB_USERNAME:-root}" -p"${DB_PASSWORD:-}" --silent >/dev/null 2>&1; do
        tries=$((tries + 1))
        if (( tries >= 60 )); then
            log "MySQL not reachable after 60 attempts — continuing anyway"
            break
        fi
        sleep 1
    done
fi

# ---------- 4. Storage link ----------
if [[ ! -e public/storage ]]; then
    log "Creating storage symlink"
    php artisan storage:link || true
fi

# ---------- 4b. Sync public/ assets into shared volume ----------
# When public/ is bind-mounted as a named volume, an image update would
# otherwise leave stale assets. Copy newer files from the baked pristine
# copy on every boot.
if [[ -d /opt/lootora-public ]]; then
    log "Syncing public/ assets from image"
    cp -aR /opt/lootora-public/. /var/www/html/public/ 2>/dev/null || true
fi

# ---------- 5. Permissions ----------
log "Fixing permissions on storage/ and bootstrap/cache/"
mkdir -p storage/framework/{cache,sessions,views,testing} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

# ---------- 6. Migrations (opt-in) ----------
if [[ "${AUTO_MIGRATE:-false}" == "true" ]]; then
    log "Running migrations (AUTO_MIGRATE=true)"
    php artisan migrate --force || log "migrate failed"
fi

# ---------- 7. Seeders (opt-in) ----------
if [[ "${AUTO_SEED:-false}" == "true" ]]; then
    log "Running seeders (AUTO_SEED=true)"
    php artisan db:seed --force || log "seed failed"
fi

# ---------- 8. Cache for production ----------
if [[ "${APP_ENV:-production}" == "production" ]]; then
    log "Caching config / routes / views"
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

log "Boot complete. Executing: $*"
exec "$@"
