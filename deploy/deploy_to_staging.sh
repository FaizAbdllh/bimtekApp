#!/usr/bin/env bash
set -euo pipefail

# deploy_to_staging.sh
# Usage: ./deploy_to_staging.sh <ssh_user>@<host> [deploy_path]
# This script is a template — customize paths and commands for your server.

REMOTE=${1:-}
DEPLOY_PATH=${2:-/var/www/html/bimtekApp}

if [ -z "$REMOTE" ]; then
  echo "Usage: $0 <ssh_user>@<host> [deploy_path]" >&2
  exit 2
fi

echo "Deploying to $REMOTE:$DEPLOY_PATH"

ssh -o StrictHostKeyChecking=no "$REMOTE" bash -s <<'SSH'
set -euo pipefail
cd "$DEPLOY_PATH"
echo "Fetching latest changes"
git fetch --all
git checkout feature/revisi-bimtek
git reset --hard origin/feature/revisi-bimtek
echo "Installing composer deps"
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
echo "Migrating database"
php artisan migrate --force
echo "Installing npm deps and building assets"
npm ci --silent
npm run build --silent
echo "Clearing and caching"
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
echo "Restarting queue workers"
php artisan queue:restart || true
# Supervisor or systemd restart examples — uncomment what applies
# sudo supervisorctl restart laravel-worker:*
# sudo systemctl restart horizon
echo "Deployment finished"
SSH
