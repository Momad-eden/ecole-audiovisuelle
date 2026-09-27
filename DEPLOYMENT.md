# Déploiement — EMSI (Laravel + Next.js, un seul serveur)

Architecture de production : **un VPS**, **un domaine** (ex. `emsi.sn`).

```
                         ┌──────────── Nginx (HTTPS) ────────────┐
 visiteurs ──► emsi.sn ──┤ /admin, /api, /livewire, /storage,    ├──► PHP-FPM ─► Laravel 13 (Filament, API)
                         │ /filament, /css/filament, /js/filament│                  │
                         │ tout le reste                          ├──► Node :3000 ─► Next.js (site public)
                         └────────────────────────────────────────┘                  │
                                                                      MySQL 8.4 ◄───┘
```

Même domaine pour tout : les cookies de l'admin restent simples et il n'y a pas de CORS.

## 1. Prérequis

- Ubuntu 24.04 LTS, Nginx, Certbot
- PHP 8.4 (fpm, cli) avec `mbstring intl gd zip xml curl mysql bcmath`
- Composer 2, Node.js 22 LTS, MySQL 8.4
- `ffmpeg` (formes d'onde des sons MP3/M4A/OGG ; sans lui, seuls les WAV en ont une)

## 2. Code et dépendances

```bash
sudo mkdir -p /var/www/emsi && sudo chown $USER:www-data /var/www/emsi
git clone <dépôt> /var/www/emsi && cd /var/www/emsi

composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate
```

## 3. Configuration `.env` (Laravel)

```dotenv
APP_NAME=EMSI
APP_ENV=production
APP_DEBUG=false
APP_URL=https://emsi.sn

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=emsi
DB_USERNAME=emsi
DB_PASSWORD=********

QUEUE_CONNECTION=database
SESSION_SECURE_COOKIE=true

MAIL_MAILER=smtp            # accusés de réception des candidatures
MAIL_HOST=…
MAIL_FROM_ADDRESS=contact@emsi.sn
MAIL_FROM_NAME="EMSI"

FRONTEND_URL=http://127.0.0.1:3000        # appel interne de régénération du site
FRONTEND_REVALIDATE_SECRET=<chaîne aléatoire longue>
```

## 4. Base de données, fichiers, premier compte

```bash
php artisan migrate --force
php artisan db:seed --force              # contenu de référence (salles, filières, programmes, pages…)
php artisan storage:link
php artisan emsi:create-admin            # premier directeur (mot de passe saisi de façon masquée)
php artisan filament:assets
php artisan optimize && php artisan filament:optimize

sudo chown -R www-data:www-data storage bootstrap/cache
```

Aucun compte n'est créé par défaut. Les documents des candidats sont stockés dans `storage/app/private` (jamais publics).

## 5. Site Next.js

`frontend/.env.local` :

```dotenv
API_URL=https://emsi.sn            # l'API Laravel, via Nginx
MEDIA_URL=https://emsi.sn
NEXT_PUBLIC_SITE_URL=https://emsi.sn
REVALIDATE_SECRET=<même valeur que FRONTEND_REVALIDATE_SECRET>
```

```bash
cd frontend && npm ci && npm run build   # l'API Laravel doit répondre pendant le build
```

Service systemd `/etc/systemd/system/emsi-web.service` :

```ini
[Unit]
Description=EMSI site public (Next.js)
After=network.target

[Service]
WorkingDirectory=/var/www/emsi/frontend
ExecStart=/usr/bin/npm run start -- --port 3000 --hostname 127.0.0.1
Restart=always
User=www-data
Environment=NODE_ENV=production

[Install]
WantedBy=multi-user.target
```

## 6. File d'attente et tâches planifiées

`/etc/systemd/system/emsi-queue.service` (formes d'onde, e-mails) :

```ini
[Unit]
Description=EMSI file d'attente Laravel
After=network.target mysql.service

[Service]
WorkingDirectory=/var/www/emsi
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
User=www-data

[Install]
WantedBy=multi-user.target
```

Crontab de `www-data` : `* * * * * cd /var/www/emsi && php artisan schedule:run >> /dev/null 2>&1`

```bash
sudo systemctl daemon-reload && sudo systemctl enable --now emsi-web emsi-queue
```

## 7. Nginx

```nginx
server {
    server_name emsi.sn www.emsi.sn;
    root /var/www/emsi/public;
    client_max_body_size 60M;          # sons (50 Mo) et pièces jointes

    # Laravel : administration, API, fichiers publics, ressources Filament/Livewire
    location ~ ^/(admin|api/v1|livewire|filament|css/filament|js/filament|fonts/filament|up)(/|$) {
        try_files $uri /index.php?$query_string;
    }
    location ^~ /storage/ {
        expires 30d;
        try_files $uri =404;
    }
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
    }

    # Tout le reste : site public Next.js
    location / {
        proxy_pass http://127.0.0.1:3000;
        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

`sudo certbot --nginx -d emsi.sn -d www.emsi.sn`. Le réglage `upload_max_filesize = 60M` et `post_max_size = 64M` est aussi à appliquer dans `php.ini` (FPM).

## 8. Sauvegardes (quotidiennes)

```bash
# /etc/cron.daily/emsi-backup
mysqldump --single-transaction emsi | gzip > /var/backups/emsi/db-$(date +%F).sql.gz
tar czf /var/backups/emsi/files-$(date +%F).tgz -C /var/www/emsi storage/app
find /var/backups/emsi -mtime +30 -delete
```

Copier ensuite `/var/backups/emsi` hors du serveur (stockage distant). Tester une restauration au moins une fois par trimestre.

## 9. Mise à jour

```bash
cd /var/www/emsi && git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize && php artisan filament:optimize && php artisan filament:assets
sudo systemctl restart emsi-queue
cd frontend && npm ci && npm run build && sudo systemctl restart emsi-web
```

## 10. Vérifications après déploiement

- `https://emsi.sn/up` répond 200 ; `https://emsi.sn/admin` affiche la connexion.
- Publier une actualité dans l'admin : elle apparaît immédiatement sur le site.
- Déposer une candidature de test avec une pièce jointe, puis la retrouver dans Scolarité › Candidatures.
- `APP_DEBUG=false` : une erreur n'affiche jamais de trace technique.
