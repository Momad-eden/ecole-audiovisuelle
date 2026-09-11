# 🚀 Guide & Checklist Officielle de Déploiement en Production — EMSI

> **École des Métiers du Son et de l'Image (EMSI)**  
> *Ce guide détaille l'intégralité des opérations nécessaires pour déployer le projet en environnement de production (VPS Linux Ubuntu/Debian, Nginx, PHP 8.2+, MySQL).*

---

## 📋 Table des Matières
1. [Prérequis Serveur](#1-prérequis-serveur)
2. [Étape 1 : Récupération du Code & Dépendances](#2-étape-1--récupération-du-code--dépendances)
3. [Étape 2 : Configuration de l'Environnement (.env)](#3-étape-2--configuration-de-lenvironnement-env)
4. [Étape 3 : Base de Données, Médias & Premier Administrateur](#4-étape-3--base-de-données-médias--premier-administrateur)
5. [Étape 4 : Permissions des Dossiers](#5-étape-4--permissions-des-dossiers)
6. [Étape 5 : Mise en Cache des Performances](#6-étape-5--mise-en-cache-des-performances)
7. [Étape 6 : Configuration du Serveur Web Nginx & Certificat SSL](#7-étape-6--configuration-du-serveur-web-nginx--certificat-ssl)
8. [Étape 7 : Tâches Automatisées (Cron & Queue Worker)](#8-étape-7--tâches-automatisées-cron--queue-worker)
9. [Étape 8 : Script de Mise à Jour Continue (CI/CD / Déploiement rapide)](#9-étape-8--script-de-mise-à-jour-continue-cicd--déploiement-rapide)

---

## 1. Prérequis Serveur

Assurez-vous que votre serveur VPS ou Cloud dispose des paquets suivants :

- **Système d'exploitation** : Ubuntu 22.04 LTS ou 24.04 LTS (ou Debian 12)
- **Serveur Web** : Nginx
- **PHP** : PHP 8.2 ou PHP 8.3 avec les extensions requises :
  ```bash
  sudo apt update
  sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath \
                      php8.2-curl php8.2-zip php8.2-intl php8.2-gd php8.2-sqlite3
  ```
- **Base de données** : MySQL 8.0+ ou MariaDB 10.11+
- **Gestionnaires de paquets** : Composer 2.x et Node.js 20+ (LTS) / NPM

---

## 2. Étape 1 : Récupération du Code & Dépendances

Sur votre serveur :

```bash
# 1. Cloner le projet dans le répertoire web
cd /var/www
sudo git clone https://github.com/votre-compte/ecole-audiovisuelle.git
cd /var/www/ecole-audiovisuelle

# 2. Installer les dépendances PHP optimisées pour la production
composer install --no-dev --optimize-autoloader

# 3. Installer et compiler les assets frontend (Vite & Tailwind)
npm ci
npm run build
```

---

## 3. Étape 2 : Configuration de l'Environnement (.env)

Créez le fichier de configuration de production :

```bash
cp .env.example .env
nano .env
```

Renseignez les variables clés de production :

```ini
APP_NAME="EMSI - École des Métiers du Son et de l'Image"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com
APP_TIMEZONE=Africa/Dakar
APP_LOCALE=fr

# Connexion MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=emsi_prod_db
DB_USERNAME=emsi_prod_user
DB_PASSWORD=VOTRE_MOT_DE_PASSE_SECURISE

# Sessions & Cache
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database

# Configuration des Emails (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io # ou votre serveur SMTP (Sendgrid, Google Workspace...)
MAIL_PORT=587
MAIL_USERNAME=votre_utilisateur
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="contact@emsi.sn"
MAIL_FROM_NAME="EMSI Administration"
```

---

## 4. Étape 3 : Base de Données, Médias & Premier Administrateur

```bash
# 1. Générer la clé de chiffrement Laravel
php artisan key:generate --force

# 2. Exécuter les migrations de base de données
php artisan migrate --force

# 3. Créer le lien symbolique vers le dossier public pour les médias (logos, photos, reçus)
php artisan storage:link

# 4. Créer le premier compte administrateur (Directeur)
php artisan make:admin
# Répondez aux questions : Nom, Email, Rôle (directeur), Mot de passe
```

---

## 5. Étape 4 : Permissions des Dossiers

Il est capital de donner les droits d'écriture au serveur web (`www-data`) sur les répertoires de stockage et de cache :

```bash
# Définir l'utilisateur propriétaire
sudo chown -R www-data:www-data /var/www/ecole-audiovisuelle

# Ajuster les droits sur les dossiers sensibles
sudo chmod -R 775 /var/www/ecole-audiovisuelle/storage
sudo chmod -R 775 /var/www/ecole-audiovisuelle/bootstrap/cache
sudo chmod -R 775 /var/www/ecole-audiovisuelle/public/storage
```

---

## 6. Étape 5 : Mise en Cache des Performances

Pour obtenir des temps de réponse ultra-rapides (< 50ms) :

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

> 💡 *Si vous modifiez le fichier `.env` ou les routes plus tard, pensez à vider puis recréer les caches (`php artisan optimize:clear && php artisan optimize`).*

---

## 7. Étape 6 : Configuration du Serveur Web Nginx & Certificat SSL

Créez le bloc serveur Nginx :

```bash
sudo nano /etc/nginx/sites-available/ecole-audiovisuelle
```

Collez la configuration suivante :

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name votre-domaine.com www.votre-domaine.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name votre-domaine.com www.votre-domaine.com;

    root /var/www/ecole-audiovisuelle/public;
    index index.php index.html;

    # En-têtes de sécurité renforcés
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    charset utf-8;

    # Limite de téléversement (permet les uploads de vidéos et photos jusqu'à 50 Mo)
    client_max_body_size 50M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    # Traitement PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # ou php8.3-fpm
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Bloquer l'accès aux fichiers cachés (.env, .git...)
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Mise en cache des assets statiques (images, css, js)
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|webp|svg|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}
```

Activez le site et rechargez Nginx :

```bash
sudo ln -s /etc/nginx/sites-available/ecole-audiovisuelle /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Installation du Certificat SSL Gratuit (Let's Encrypt)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d votre-domaine.com -d www.votre-domaine.com
```

---

## 8. Étape 7 : Tâches Automatisées (Cron & Queue Worker)

### A. Planificateur de tâches Laravel (Cron)
Ouvrez le crontab du serveur web :
```bash
sudo crontab -e -u www-data
```
Ajoutez cette unique ligne à la fin :
```bash
* * * * * cd /var/www/ecole-audiovisuelle && php artisan schedule:run >> /dev/null 2>&1
```

### B. Gestionnaire de file d'attente (Supervisor)
Si vous envoyez des emails ou traitez des fichiers en arrière-plan :
```bash
sudo apt install -y supervisor
sudo nano /etc/supervisor/conf.d/emsi-worker.conf
```

Collez :
```ini
[program:emsi-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ecole-audiovisuelle/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ecole-audiovisuelle/storage/logs/worker.log
stopwaitsecs=3600
```

Activez le worker :
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start emsi-worker:*
```

---

## 9. Étape 8 : Script de Mise à Jour Continue (Déploiement Rapide)

Pour les futures mises à jour du site en 1 seule commande, créez un fichier `deploy.sh` à la racine :

```bash
nano /var/www/ecole-audiovisuelle/deploy.sh
```

Collez :
```bash
#!/bin/bash
set -e

echo "🚀 Début du déploiement..."

# Passer en mode maintenance avec écran élégant
php artisan down --render="errors::500" --secret="emsi-secret-bypass" || true

# Récupérer la dernière version du code
git pull origin main

# Mettre à jour les dépendances
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build

# Exécuter les nouvelles migrations éventuelles
php artisan migrate --force

# Recréer les caches de performance
php artisan optimize:clear
php artisan optimize

# Redémarrer les workers de queue
php artisan queue:restart || true

# Désactiver le mode maintenance
php artisan up

echo "✅ Déploiement terminé avec succès !"
```

Rendez le script exécutable :
```bash
chmod +x /var/www/ecole-audiovisuelle/deploy.sh
```

Désormais, pour mettre à jour le site en production, il vous suffira de lancer :
```bash
./deploy.sh
```

---

*Document généré pour l'École des Métiers du Son et de l'Image (EMSI).*
