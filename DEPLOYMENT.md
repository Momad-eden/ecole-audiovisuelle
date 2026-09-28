# Déploiement — EMSI (Laravel + Next.js, un seul serveur)

Architecture de production : **un VPS**, **un domaine** (ex. `emsi.sn`).

```
                         ┌──────────── Nginx (HTTPS) ────────────┐
 visiteurs ──► emsi.sn ──┤ /admin, /api/v1, /livewire, /storage, ├──► PHP-FPM ─► Laravel 13 (Filament, API)
                         │ /filament, /css/filament, /js/filament│                  │
                         │ tout le reste                          ├──► Node :3000 ─► Next.js (site public)
                         └────────────────────────────────────────┘                  │
                                                                      MySQL 8.4 ◄───┘
```

Même domaine pour tout : les cookies de l'admin restent simples et il n'y a pas de CORS.
`/api/revalidate` et `/api/preview` appartiennent à Next.js (seul `/api/v1` va à Laravel).

Serveur conseillé : 4 vCPU, 8 Go de RAM, 75 Go SSD minimum (ex. OVHcloud VPS-2), Ubuntu 24.04 LTS.
Le guide pas à pas pour l'équipe (domaine .sn, serveur, e-mails) complète ce document.

## 1. Préparer le serveur

```bash
# Utilisateur de déploiement (connexion par clé SSH uniquement)
adduser emsi && usermod -aG sudo,www-data emsi
# /etc/ssh/sshd_config : PasswordAuthentication no, PermitRootLogin no ; puis systemctl restart ssh

# Pare-feu
ufw allow OpenSSH && ufw allow 'Nginx Full' && ufw enable
apt install -y fail2ban unattended-upgrades

# Mémoire d'appoint pour la compilation du site Next.js
fallocate -l 2G /swapfile && chmod 600 /swapfile && mkswap /swapfile && swapon /swapfile
echo '/swapfile none swap sw 0 0' >> /etc/fstab
```

## 2. Logiciels

- **PHP 8.4 minimum** (Symfony 8 l'exige ; Ubuntu 24.04 fournit la 8.3, d'où le dépôt ondrej/php) :
  ```bash
  add-apt-repository ppa:ondrej/php && apt update
  apt install -y php8.4-fpm php8.4-cli php8.4-mbstring php8.4-intl php8.4-gd php8.4-zip php8.4-xml php8.4-curl php8.4-mysql php8.4-bcmath
  ```
- Nginx, Certbot (`apt install -y nginx certbot python3-certbot-nginx`)
- MySQL 8.4 (dépôt officiel MySQL) ; créer la base `emsi` et l'utilisateur `emsi` (droits sur cette base seulement)
- Composer 2, Node.js 22 LTS (dépôt NodeSource)
- `ffmpeg` (formes d'onde des sons MP3/M4A/OGG ; sans lui, seuls les WAV en ont une)

`/etc/php/8.4/fpm/php.ini` : `upload_max_filesize = 60M`, `post_max_size = 64M`, puis `systemctl restart php8.4-fpm`.

## 3. Code et dépendances

```bash
sudo mkdir -p /var/www/emsi && sudo chown emsi:www-data /var/www/emsi
git clone <dépôt> /var/www/emsi && cd /var/www/emsi

composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate

# Laravel (PHP-FPM, file d'attente) écrit dans storage/ et bootstrap/cache
sudo chgrp -R www-data storage bootstrap/cache && sudo chmod -R g+ws storage bootstrap/cache
# Les fichiers créés par « emsi » (artisan, deploy.sh) restent modifiables par www-data (journaux, caches)
echo 'umask 002' >> /home/emsi/.bashrc
```

## 4. Configuration `.env` (Laravel)

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
LOG_LEVEL=warning

MAIL_MAILER=smtp            # accusés de réception, alertes candidatures et demandes Impact Live
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=…
MAIL_PASSWORD=…
MAIL_FROM_ADDRESS=contact@emsi.sn
MAIL_FROM_NAME="EMSI"

# Adresse PUBLIQUE du site : sert au bouton « Aperçu » de l'admin et à la régénération des pages.
# (Une adresse interne comme http://127.0.0.1:3000 casserait l'aperçu pour l'équipe.)
FRONTEND_URL=https://emsi.sn
FRONTEND_REVALIDATE_SECRET=<chaîne aléatoire longue : openssl rand -hex 32>
```

## 5. Nginx et HTTPS (avant la compilation du site)

Next.js interroge l'API pendant sa compilation : Laravel doit déjà répondre sur `https://emsi.sn`.

`/etc/nginx/sites-available/emsi` (puis lien dans `sites-enabled`, `nginx -t`, `systemctl reload nginx`) :

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

`sudo certbot --nginx -d emsi.sn -d www.emsi.sn` (renouvellement automatique inclus).

## 6. Base de données, fichiers, premier compte

**Première installation, base vide :**

```bash
php artisan migrate --force
php artisan db:seed --force              # contenu de référence (univers, filières, programmes, pages…)
php artisan storage:link
php artisan emsi:create-admin            # premier directeur (mot de passe saisi de façon masquée)
php artisan filament:assets
php artisan optimize && php artisan filament:optimize
```

**Ou reprise du contenu déjà saisi en local** (logo, pages, photos…) à la place de `db:seed` :

```bash
# Sur le poste local
mysqldump --single-transaction --no-tablespaces ecole_audiovisuelle | gzip > emsi-contenu.sql.gz
tar czf emsi-medias.tgz -C storage/app public private
scp emsi-contenu.sql.gz emsi-medias.tgz emsi@<ip-du-serveur>:/tmp/

# Sur le serveur
gunzip < /tmp/emsi-contenu.sql.gz | mysql emsi
tar xzf /tmp/emsi-medias.tgz -C /var/www/emsi/storage/app
php artisan migrate --force && php artisan storage:link
```

Supprimer ensuite les comptes de test et vérifier ceux de l'équipe (Administration › Comptes).
Aucun compte n'est créé par défaut. Les documents des candidats sont stockés dans `storage/app/private` (jamais publics).

## 7. Site Next.js

`frontend/.env.local` :

```dotenv
API_URL=https://emsi.sn            # l'API Laravel, via Nginx
MEDIA_URL=https://emsi.sn
NEXT_PUBLIC_SITE_URL=https://emsi.sn
REVALIDATE_SECRET=<même valeur que FRONTEND_REVALIDATE_SECRET>
```

```bash
cd frontend && npm ci && npm run build
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
User=emsi
Environment=NODE_ENV=production

[Install]
WantedBy=multi-user.target
```

## 8. File d'attente et tâches planifiées

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

## 9. E-mails : enregistrements DNS

Chez le bureau d'enregistrement du domaine, en plus de `A emsi.sn → IP du serveur` et `A www → IP` :

- **MX** : ceux du fournisseur de boîtes mail (Google Workspace, Zoho…).
- **SPF** (TXT sur `emsi.sn`) : une seule ligne qui autorise les deux services, par ex. `v=spf1 include:_spf.google.com include:spf.brevo.com ~all`.
- **DKIM** : les enregistrements fournis par Brevo et par le fournisseur de boîtes mail.
- **DMARC** (TXT sur `_dmarc.emsi.sn`) : `v=DMARC1; p=none; rua=mailto:contact@emsi.sn`, à durcir en `p=quarantine` après quelques semaines.

## 10. Sauvegardes (quotidiennes)

```bash
# /etc/cron.daily/emsi-backup
mysqldump --single-transaction emsi | gzip > /var/backups/emsi/db-$(date +%F).sql.gz
tar czf /var/backups/emsi/files-$(date +%F).tgz -C /var/www/emsi storage/app
find /var/backups/emsi -mtime +30 -delete
```

Copier ensuite `/var/backups/emsi` hors du serveur (ex. `rclone sync` vers un stockage objet). Les sauvegardes automatiques de l'hébergeur ne suffisent pas : elles sont dans le même centre de données. Tester une restauration au moins une fois par trimestre.

## 11. Mise à jour

`./deploy.sh` depuis `/var/www/emsi` (maintenance, `git pull`, dépendances, migrations, caches, file d'attente, compilation et redémarrage du site).

Pour une base installée avant ces versions, une seule fois chacune : `php artisan emsi:site-v2`, `emsi:impact-live`, `emsi:site-v3`.

## 12. Vérifications après déploiement

- `https://emsi.sn/up` répond 200 ; `https://emsi.sn/admin` affiche la connexion.
- Publier une actualité dans l'admin : elle apparaît immédiatement sur le site.
- Bouton **Aperçu** d'une page dans l'admin : l'aperçu s'ouvre sur `https://emsi.sn/apercu`.
- Déposer une candidature de test avec une pièce jointe, puis la retrouver dans Scolarité › Candidatures ; l'e-mail d'alerte arrive.
- Envoyer une demande de devis Impact Live de test.
- `APP_DEBUG=false` : une erreur n'affiche jamais de trace technique.
