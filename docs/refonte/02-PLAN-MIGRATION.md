# 02 — Plan de refonte & migration Next.js

> Chaque phase se termine par un livrable et un **STOP pour validation de Momar**.

## Architecture cible

```
ecole-audiovisuelle/
├── app/ … (Laravel = API)          → /api/public/*   (lecture, sans auth, cache)
│                                   → /api/admin/*    (Sanctum, Policies)
├── database/ (PostgreSQL)
├── frontend/  (Next.js App Router, TS)
│   ├── app/(public)/…              → site vitrine SSR/ISR
│   ├── app/admin/…                 → espace admin (client, protégé)
│   ├── components/ui (shadcn)  components/public  components/admin
│   ├── lib/api (client typé)  lib/schemas (zod)
│   └── types/ (générés depuis les API Resources)
└── docs/refonte/
```

Auth : Sanctum SPA (cookie `XSRF-TOKEN`, même domaine parent — ex. `emsi.sn` + `api.emsi.sn`), middleware Next.js qui redirige `/admin/*` vers `/admin/login` si non authentifié.

## Phase 0 — Audit complet (lecture seule)
- Compléter `01-AUDIT.md` (surtout section 5 : lecture de toutes les vues publiques et admin).
- Lancer l'app en local, parcourir chaque page, noter bugs et incohérences.
- Produire `03-CONCEPTION.md` : arborescence du site public, contenus par page, parcours candidat, écrans admin par rôle, modèle de données cible (MCD + dictionnaire), contrat d'API (liste des endpoints).
- **Livrable** : audit complété + conception. **STOP.**

## Phase 1 — Correctifs critiques (sur la base Blade actuelle)
- Désactiver `/register` ; commande `php artisan emsi:create-admin` pour les comptes (existe déjà : `CreateAdminCommand`) ; supprimer le mot de passe par défaut du seeder.
- `restrictOnDelete` sur `students.course_id` et `payments.student_id` ; SoftDeletes sur `courses`, `students`, `payments`.
- Générateur de séquences transactionnel (`sequences` table + `lockForUpdate`) pour reçus (`REC`, `DEP` séparés) et matricules.
- Rate-limit + honeypot sur le formulaire de candidature.
- *(ajouts Phase 0)* Recherche de la caisse en erreur 500 sous MySQL (A7), tableau de bord cloisonné par rôle (S6), slug d'actualité unique (D13), catégorie de caisse cohérente avec le type et montants bornés (D11), locale FR (`lang/fr`, dates, messages de validation), retrait des coordonnées fictives et des affirmations non sourcées (01-AUDIT §5.2 et §5.4), renommage de `make:admin` en `emsi:create-admin` sans option `--password`, tests exécutés aussi sur MySQL.
- Tests Feature couvrant chaque correctif.
- **STOP.**

## Phase 2 — Modèle de données & PostgreSQL
- Passage à PostgreSQL (docker-compose ou instance locale). **Aucune donnée de production** (décision Q-2) : schéma neuf et seeders de référence (03-CONCEPTION §9.5), sans script de reprise.
- Nouvelles entités :
  - **Formation** : `programs` (formations classiques de l'école **et** programmes professionnels Volet 1 / BTS-VAE, avec un champ `audience` : grand public / professionnels), `cohorts` (le nom `sessions` est déjà utilisé par Laravel) (année, dates, capacité, candidatures ouvertes/fermées), `offerings` (filière × programme × session, capacité, frais, mode de financement), `admission_documents` (uploads), `admission_steps` (dossier → entretien → décision).
  - **Musée numérique** : `rooms` (salles : Son, Lumière, Image, Visuel… créables depuis l'admin, avec couleur d'accent et ambiance), `exhibitions` (expositions temporaires), `artworks` (œuvres : médias, crédits, filière, promotion, matériel, récit), `artwork_credits`, pivot `artwork_exhibition`.
  - **Contenu administrable** : `media` (médiathèque : image/vidéo/audio/PDF, alt, crédits, variantes générées), `pages` + `page_blocks` (blocs ordonnés, type + données JSON validées par schéma), `menus` + `menu_items`, `content_revisions` (brouillon/publication/historique), `testimonials`, `faqs`, `events` (Biennale, ECOFES, JOJ…).
- Caisse : `cash_transactions` inaltérables (annulation par contre-écriture), `cash_closings`, table `sequences`.
- Normalisation des enums (voir `CLAUDE.md` §6), montants en entiers, `capacity` au lieu de `students_count`.
- Casts Enum dans tous les modèles ; Policies par modèle.
- **STOP.**

## Phase 3 — API Laravel
- Routes `/api/public/*` : settings, pages, formations (liste/détail), actualités, galerie, partenaires, événements, FAQ, dépôt de candidature (multipart).
- Routes `/api/admin/*` : CRUD complet + actions (`approve`, `reject`, `enroll`), caisse, comptabilité (agrégats SQL), export CSV/Excel, reçu PDF, dashboard (KPI calculés en SQL).
- API Resources, pagination, filtres, tri ; FormRequests réutilisés ; erreurs JSON homogènes.
- Tests Feature pour chaque endpoint + tests d'autorisation par rôle.
- Documentation OpenAPI (ex. Scribe) → génération des types TS.
- **STOP.**

## Phase 4 — Site public Next.js (« musée numérique »)
Commencer par une **maquette de direction artistique** dans la piste retenue, **A « Salle obscure »** (accueil + une salle + une fiche œuvre), à valider avant de développer le reste, puis remettre le **tableau de collecte des contenus** à l'école.

Arborescence (détaillée dans 03-CONCEPTION §2) :
- `/` Accueil = **entrée du musée** : immersion visuelle et sonore (sans lecture auto), les salles, œuvres à la une, formations de l'école, actualités, **un seul bloc** vers l'Espace Professionnels, CTA « Candidater ». Composée de blocs gérés dans l'admin.
- `/musee` (plan du musée), `/musee/[salle]` (Son, Lumière, Image, Visuel…), `/musee/oeuvres/[slug]`, `/expositions/[slug]`
- `/formations` et `/formations/[slug]` (formations de l'école : compétences, débouchés, équipements, œuvres des apprenants liées, conditions, calendrier, FAQ)
- `/professionnels` = **Espace Professionnels centralisé** : `/professionnels` (le programme EMSI × Grand Théâtre), `/professionnels/perfectionnement` (Volet 1), `/professionnels/bts-vae` (Volet 2, dispositif VAE, Livret, jury), `/professionnels/candidater`
- `/ecole`, `/actualites`, `/actualites/[slug]`, `/contact`, pages libres `/[slug]` créées depuis l'admin
- `/candidater` : formulaire multi-étapes adapté au public (école) ; variante professionnelle (parcours CPS/CS, expérience, pièces jointes VAE) dans l'Espace Professionnels. Brouillon local, confirmation + e-mail.
- Lecteur audio global persistant (la musique continue en changeant de page), visualisation de forme d'onde, galeries plein écran, vidéos en lecture différée.
- SEO : `generateMetadata`, sitemap, robots, OpenGraph, JSON-LD (`EducationalOrganization`, `CreativeWork` pour les œuvres). ISR avec revalidation déclenchée à chaque publication dans l'admin.
- Redirections 301 : `/projet` et `/vae` → `/professionnels…`, `/galerie` → `/musee`, `/admission` → `/candidater`.
- **STOP.**

## Phase 5 — Espace admin Next.js (utilisable par un non-informaticien)
- Principe : **tout ce qui apparaît sur le site se modifie ici**, en français simple, sans jargon, avec aperçu avant publication.
- Layout : sidebar par rôle, recherche globale, fil d'Ariane, notifications (nouvelles candidatures), bouton « Voir sur le site » partout.
- **Constructeur de pages par blocs** (glisser-déposer, bibliothèque de blocs prédéfinis, aperçu en direct, brouillon / publication / historique).
- **Médiathèque** (upload glisser-déposer, recadrage, alt, crédits, compression et formats générés automatiquement, extraction de forme d'onde pour l'audio).
- **Musée** : gestion des salles, expositions, œuvres (assistant pas à pas pour ajouter une œuvre).
- **Menus & pied de page**, paramètres du site (coordonnées, réseaux, logo, couleurs d'accent), SEO par page avec aperçu Google/réseaux sociaux.
- **Espace Professionnels** géré comme une rubrique : programmes, sessions, ouverture/fermeture des candidatures.
- Guide intégré (aide contextuelle sur chaque écran) + `docs/GUIDE-ADMIN.md` illustré pour l'équipe de l'école.
- Dashboard : KPI (candidatures par statut, taux de remplissage par filière/volet, encaissé vs attendu, solde de caisse), graphiques.
- Candidatures : tableau filtrable (filière, volet, statut), fiche candidat avec documents, pipeline (dossier → entretien → décision → inscription), actions en masse.
- Étudiants : fiche, historique de paiements, statut, export.
- Caisse & comptabilité : saisie encaissement/décaissement, reçu PDF, journal avec solde courant, clôture de période, export.
- Contenu : formations, pages éditoriales (sections), actualités (éditeur riche), galerie, partenaires, événements, FAQ.
- Paramètres & utilisateurs (directeur uniquement), journal d'activité (audit log).
- **STOP.**

## Phase 6 — Bascule & nettoyage
- Tests e2e Playwright (candidature, login, encaissement, publication d'actualité).
- Suppression des vues Blade, d'Alpine et de Vite côté Laravel ; mise à jour de `DEPLOYMENT.md` (fusion avec `GUIDE_DEPLOIEMENT.md`).
- Déploiement sur **un seul VPS** : Nginx + PHP-FPM (Laravel) + Node (Next.js, service systemd), **même domaine** (`/api` et `/sanctum` vers Laravel), PostgreSQL, stockage disque (public et privé), sauvegardes.
- **Recette « non-informaticien »** : une personne de l'école réalise seule les tâches listées dans `CLAUDE.md` §2 bis-B ; chaque blocage devient un correctif.
- **Livrable final** : recette avec Momar.
