#!/bin/bash
set -e

echo "🚀 Début du déploiement EMSI..."

# Passer en mode maintenance avec écran élégant
php artisan down --render="errors::500" --secret="emsi-secret-bypass" || true

# Récupérer la dernière version du code
git pull origin main

# Mettre à jour les dépendances PHP & JS
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build

# Exécuter les nouvelles migrations
php artisan migrate --force

# Recréer les caches de performance
php artisan optimize:clear
php artisan optimize

# Redémarrer les workers de queue si configurés
php artisan queue:restart || true

# Désactiver le mode maintenance
php artisan up

echo "✅ Déploiement terminé avec succès !"
