# EMSI — École des Métiers du Son et de l'Image (Dakar)

Plateforme de l'école : **site public « musée numérique »** et **administration** pour l'équipe.

| Partie | Technologie | Dossier |
|---|---|---|
| Administration (candidatures, étudiants, caisse, formations, musée, pages) | Laravel 13 + Filament 5 | racine — `/admin` |
| API publique (lecture seule + formulaires) | Laravel 13 | `routes/api.php` — `/api/v1/public` |
| Site public (musée, formations, Espace Professionnels, candidature) | Next.js 16 + TypeScript + Tailwind 4 | `frontend/` |
| Base de données | MySQL 8.4 | — |

## Démarrer en local

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed          # contenu de référence
php artisan storage:link
php artisan emsi:create-admin       # premier compte
php artisan serve                   # http://127.0.0.1:8000/admin

cd frontend && cp .env.example .env.local && npm install && npm run dev   # http://localhost:3000
```

## Tests

```bash
php artisan test            # SQLite en mémoire
composer test:mysql         # même suite sur MySQL 8.4 (conteneur Docker jetable)
cd frontend && npm run lint && npx tsc --noEmit
```

## Documentation

- [`DEPLOYMENT.md`](DEPLOYMENT.md) — mise en production (un serveur, un domaine)
- [`docs/GUIDE-ADMIN.md`](docs/GUIDE-ADMIN.md) — guide de l'administration pour l'équipe de l'école
- [`frontend/README.md`](frontend/README.md) — site public
- [`docs/refonte/`](docs/refonte/) — audit et conception initiaux (historique)
