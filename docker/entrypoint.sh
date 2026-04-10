#!/bin/sh
set -e

# ─────────────────────────────────────────────
# 1. Copy .env if not present
# ─────────────────────────────────────────────
if [ ! -f /var/www/.env ]; then
    echo "[entrypoint] .env not found — copying from .env.example"
    cp /var/www/.env.example /var/www/.env
fi

# ─────────────────────────────────────────────
# 2. Install Composer dependencies if vendor is missing
# ─────────────────────────────────────────────
if [ ! -f /var/www/vendor/autoload.php ]; then
    echo "[entrypoint] vendor/autoload.php missing — running composer install"
    composer install --no-dev --optimize-autoloader --no-interaction --no-progress
fi

# ─────────────────────────────────────────────
# 3. Generate application key if not set
# ─────────────────────────────────────────────
APP_KEY_VALUE=$(grep -E '^APP_KEY=' /var/www/.env | cut -d'=' -f2 | tr -d '"')
if [ -z "$APP_KEY_VALUE" ]; then
    echo "[entrypoint] APP_KEY is empty — generating key"
    php /var/www/artisan key:generate --force
fi

# ─────────────────────────────────────────────
# 4. Wait for database to be ready
# ─────────────────────────────────────────────
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
echo "[entrypoint] Waiting for database at ${DB_HOST}:${DB_PORT}..."

# Write a temporary MySQL defaults file to avoid exposing credentials in the process list
MYSQL_CNF=$(mktemp)
chmod 600 "$MYSQL_CNF"
cat > "$MYSQL_CNF" << EOF
[client]
host=${DB_HOST}
port=${DB_PORT}
user=${DB_USERNAME:-talian}
password=${DB_PASSWORD:-secret}
EOF

until mysql --defaults-extra-file="$MYSQL_CNF" -e "SELECT 1" > /dev/null 2>&1; do
    echo "[entrypoint] Database not ready yet — retrying in 3s..."
    sleep 3
done
rm -f "$MYSQL_CNF"
echo "[entrypoint] Database is ready."

# ─────────────────────────────────────────────
# 5. Run migrations (idempotent — safe to re-run)
# ─────────────────────────────────────────────
echo "[entrypoint] Running migrations..."
php /var/www/artisan migrate --force

# ─────────────────────────────────────────────
# 6. Seed in non-production (safe — uses firstOrCreate)
# ─────────────────────────────────────────────
APP_ENV_VALUE=$(grep -E '^APP_ENV=' /var/www/.env | cut -d'=' -f2 | tr -d '"')
if [ "$APP_ENV_VALUE" != "production" ]; then
    echo "[entrypoint] Non-production environment — running database seeder..."
    php /var/www/artisan db:seed --force
fi

# ─────────────────────────────────────────────
# 7. Create storage symlink
# ─────────────────────────────────────────────
php /var/www/artisan storage:link --force 2>/dev/null || true

# ─────────────────────────────────────────────
# 8. Clear and cache config for production
# ─────────────────────────────────────────────
if [ "$APP_ENV_VALUE" = "production" ]; then
    php /var/www/artisan config:cache
    php /var/www/artisan route:cache
fi

echo "[entrypoint] Startup complete. Starting PHP-FPM..."
exec "$@"
