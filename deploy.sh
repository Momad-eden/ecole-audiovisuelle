#!/bin/bash
# Mise à jour de production (voir DEPLOYMENT.md). À lancer depuis /var/www/emsi.
set -euo pipefail

echo "Mise à jour EMSI…"

php artisan down --retry=30 || true
trap 'php artisan up' ERR

git pull --ff-only origin main

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
php artisan filament:assets
php artisan filament:optimize
php artisan queue:restart

# Laravel remis en ligne avant le build : Next.js interroge l'API pendant la génération.
php artisan up

(cd frontend && npm ci && npm run build)
sudo systemctl restart emsi-web

echo "Déploiement terminé."
