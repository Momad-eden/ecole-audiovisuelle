# CLAUDE.md — Projet EMSI Dakar (école-audiovisuelle)

> Mémoire projet pour l'agent Claude (Antigravity IDE). À lire intégralement avant toute action.

## 1. Contexte

- **Client** : EMSI — École des Métiers du Son et de l'Image, Dakar.
- **Projet porté** : programme intégré EMSI × Grand Théâtre National Doudou Ndiaye Coumba Rose
  - **Volet 1** — Perfectionnement intensif (40 jeunes, 3 mois, sept.–nov. 2026)
  - **Volet 2** — Cycle BTS par la VAE (60 jeunes, 9 mois, démarrage 2027)
  - 5 filières : Son, Technicien Lumière, Régie Générale Spectacle, Infographie & Création Numérique, Cadrage Sportif & Régie Vidéo
  - Document source : `Copie de PROJET_EMSI_DAKAR_GRAND_THEATRE_FESTIVALS_EVENEMENTS.pdf` (racine du repo) = **référence de contenu**.
- **Écosystème** (depuis le 27/09/2026) : fondé par Boubacar Tall, ingénieur du son sénégalais basé à Saint-Louis : l'EMSI (campus de Dakar au Grand Théâtre et de Saint-Louis, mêmes formations), **Impact Live Studio** (studio d'enregistrement) et l'**Espace Habib Faye** (centre culturel privé), réunis dans la Maison de la culture Habib Faye. **Impact Live Events** (location de sono, lumières, podiums) est **retiré du site** (décision du 29/09/2026) ; ses données restent en base. Specs : `docs/superpowers/specs/2026-09-27-impact-live-design.md` puis `2026-09-29-trois-domaines-design.md`.
- **Développeur / décideur** : Momar Diop (Halal Techno).

## 2. Objectif de la refonte

1. Corriger les incohérences (contenu, données, logique métier, sécurité).
2. Refaire un **espace admin** professionnel et cohérent.
3. Migrer le **frontend vers Next.js** (App Router, TypeScript). Laravel devient une **API**.
4. Mieux présenter l'école sur le site public.

## 2 bis. Principes directeurs (décisions de Momar — non négociables)

### A. La plateforme = trois domaines (décision du 29/09/2026, remplace « la plateforme = l'école »)
- Le site présente **trois domaines** : la **Maison de la culture Habib Faye** (avec Impact Live Studio : agenda, espaces à louer, studio), l'**EMSI** (campus de Dakar et de Saint-Louis, mêmes formations, disponibilité par campus) et des **pages générales pour les financeurs** (mission, partenaires, soutenir, presse).
- Le **Grand Théâtre National Doudou Ndiaye Coumba Rose est un partenaire** qui accueille le campus de Dakar ; ce n'est pas un domaine.
- Le projet EMSI × Grand Théâtre (Volet 1 + BTS par la VAE) s'adresse à des **professionnels** ; il vit dans sa rubrique `/emsi/professionnels` et ne domine ni l'accueil ni le menu.
- Navigation principale : **Accueil · Maison Habib Faye ▾ · EMSI ▾ · Candidater** (sous-menus au clic et au clavier, accordéon sur téléphone, fil d'Ariane et sous-navigation de domaine). L'accueil commence par le triptyque « Nos trois maisons ».
- **À venir (R2)** : site **bilingue français / anglais**, à faire avant le style des titres et le paiement mensuel.
- Les anciennes adresses (`/formations`, `/studio`, `/ecole`, `/agenda`, `/events`, `/musee`, `/demande`…) sont redirigées en une fois vers les nouvelles.

### B. Tout est administrable par un non-informaticien
- **Aucun contenu visible ne doit nécessiter de modifier du code** : textes, images, vidéos, sons, menus, pied de page, blocs de l'accueil, chiffres clés, FAQ, SEO, coordonnées, réseaux sociaux, couleurs d'accent des « univers ».
- Pages construites par **blocs (sections) réorganisables** depuis l'admin (glisser-déposer), à partir d'une bibliothèque de blocs prédéfinis et testés (héros, texte + image, galerie, lecteur audio, vidéo, chiffres clés, citation, appel à l'action, liste de formations, etc.). Pas d'éditeur HTML libre.
- **Médiathèque** centrale (images, vidéos, fichiers audio, PDF) avec recadrage, texte alternatif, crédits.
- **Brouillon / aperçu / publication** pour chaque contenu, historique des versions, restauration.
- Mise en forme « comme un traitement de texte » mais **guidée** (décision de Momar, 27/09/2026) : polices, tailles et couleurs choisies dans des listes fermées (`app/Filament/Support/RichText`), jamais de style libre.
- Interface admin en français simple, sans jargon technique (« Publier », « Masquer du site », pas « is_active »), aides contextuelles, messages de confirmation clairs, impossibilité de casser la mise en page (champs limités, formats imposés, compression automatique des médias).
- Critère de recette : **une personne de l'administration de l'école, sans formation technique, doit pouvoir réaliser seule** : publier une actualité, ajouter une réalisation d'étudiant, modifier la page d'accueil, ouvrir une session de candidature, traiter une candidature, encaisser un paiement.

### C. Le site public est une œuvre visuelle au service du choix de l'école
- **Objectif premier** : faire découvrir l'EMSI et ses formations, et donner envie de la **choisir** (précision de Momar du 27/09/2026 : le « musée » était une image pour dire « beau comme un musée », pas une arborescence).
- Le site doit **faire ressentir l'art du son, de la lumière et de l'image** et être à la hauteur d'une école installée au **Grand Théâtre National Doudou Ndiaye Coumba Rose**, équipée de matériel de dernière génération : une œuvre qui attire l'œil, pas une plaquette institutionnelle.
- Organisation en **univers** (disciplines) : Son · Image (vidéo & photo) · Infographie & design · Scène (régie & lumière) · Cinéma (bientôt). Chaque univers présente ses filières, ce qu'on y apprend, les métiers, les formations et les **réalisations** des étudiants (fiche : médias, **audio avec forme d'onde**, crédits, filière, promotion, matériel, récit de création).
- Direction artistique **« Plein feux »** (spec : `docs/superpowers/specs/2026-09-27-site-public-v2-design.md`) : scène dans le noir qui s'allume au défilement, couleurs du logo (orange projecteur, violet), une lumière par univers, titres Archivo étendus, signatures animées en SVG. Toujours **lisible et performant** : `prefers-reduced-motion` respecté, animations suspendues hors écran, aucun son en lecture automatique.
- Navigation simple : Accueil · Maison Habib Faye ▾ · EMSI ▾ · Candidater ; le CTA « Candidater » est toujours visible.

## 3. Architecture en place (depuis le 27/09/2026)

| Couche | Choix |
|---|---|
| Administration | **Filament 5** dans Laravel (`/admin`), rôles appliqués par des **Policies** (`app/Policies`) |
| Backend / API | Laravel 13 ; API publique en lecture seule `/api/v1/public` (+ dépôt de candidature, contact) |
| Base de données | **MySQL 8.4** (PostgreSQL écarté : aucun bénéfice décisif pour ce projet) |
| Site public | **Next.js 16** (App Router, TypeScript strict, Tailwind 4) dans `frontend/`, direction artistique « Plein feux » |
| Données du site | `fetch` serveur étiqueté `content`, régénéré par Laravel à chaque publication (`FrontendRevalidator`) |
| Formulaires publics | react-hook-form + zod côté Next ; FormRequest Laravel font foi |

Comptabilité : **une caisse par campus** (`place_id`, code DKR/STL dans la numérotation, soldes, clôtures et exports séparés) ; personnel rattachable à un campus (`users.place_id`, requêtes et Policies filtrées via `BelongsToCampus`).

Impact Live : lieux (`places`), services à prix « à partir de », matériel à louer et packs, agenda et références, demandes de devis/réservation (`BookingWorkflow` : nouvelle → devis envoyé → confirmée → réalisée / annulée), rôle `commercial`.

Trois domaines : chaque page porte un domaine (`domain` : `general`, `maison`, `emsi`, `studio` ; déduit de l'adresse dans l'admin, modifiable) et un parent ; les menus ont des sous-menus (`menu_items.parent_id`) ; formations et sessions ont une **disponibilité par campus** (table `place_program`, « Disponible à » / « Campus »). Blocs ajoutés : `domains` (Nos trois maisons), `campus_programs` (formations de ce campus), documents à télécharger, `support_form` (Nous soutenir, messages de type soutien/partenariat).

Domaine : filières → programmes (école / professionnels) → sessions → offres ; candidatures (`ApplicationWorkflow`) → étudiants → inscriptions ; caisse inaltérable (`CashRegister` : contre-écritures, clôtures). Univers (table `rooms`) → filières ; réalisations (`artworks`) et expositions. Contenu : pages à blocs (brouillon → publication → révisions), actualités, FAQ, menus, redirections.

## 4. Méthode de travail

- Les phases de `docs/refonte/` ne sont plus suivies (décision de Momar) : ces documents restent comme historique de l'audit et de la conception.
- Branche de travail dédiée, commits petits et explicites, TDD pour toute logique métier.
- Ne jamais toucher à `.env`, ni supprimer de données, ni lancer `migrate:fresh` sur une base contenant des données réelles sans accord.
- Toute migration de schéma est **réversible** (`down()` correct) et accompagnée d'un test.
- Tests sur SQLite **et** MySQL (`composer test:mysql`) avant tout jalon.

## 5. Skills

Des skills sont installées dans l'environnement de l'agent.
1. **Au début de chaque session : lister les skills disponibles** et indiquer lesquelles seront utilisées pour la tâche en cours et pourquoi.
2. Les utiliser systématiquement quand elles couvrent la tâche (ex. : design frontend / UI, Next.js, Laravel, tests, revue de code, sécurité, génération de documents, diagrammes).
3. Si une skill contredit ce fichier, **ce fichier prime** ; signaler le conflit.

## 6. Conventions

- Langue de l'interface et du contenu : **français**. Code (noms de variables, classes) : anglais.
- Monnaie : **FCFA (XOF)**, format `1 250 000 FCFA`, sans décimales à l'affichage.
- Téléphones : format sénégalais `+221 7X XXX XX XX`.
- Valeurs d'enum stockées en base : **snake_case ASCII** (`enrolled`, `graduated`…) ; libellés FR uniquement côté présentation.
- Un seul référentiel de genre : `male` / `female` (plus de mélange `M/F` vs `Homme/Femme`).
- Montants : colonnes `bigInteger` en FCFA entiers (pas de `decimal`).
- Rôles : `directeur`, `gestionnaire`, `secretaire`, `communication` — autorisations via **Policies/Gates**, pas seulement du middleware de routes.
- Qualité : `pint` (PHP), ESLint + Prettier (TS), tests Feature Laravel + tests e2e Playwright sur les parcours clés (candidature, connexion, encaissement).
- Accessibilité (WCAG AA), performances (Lighthouse ≥ 90 sur le public), SEO (metadata, sitemap, OpenGraph, JSON-LD `EducationalOrganization`).

## 7. Commandes utiles

```bash
# Backend
composer install && php artisan migrate && php artisan db:seed
php artisan test                 # SQLite en mémoire (rapide)
composer test:mysql              # même suite sur MySQL 8.4 (conteneur Docker jetable, port 33306)
php artisan emsi:create-admin    # créer un compte (aucun compte par défaut)
php artisan emsi:site-v2         # mettre à niveau une base existante vers le site « Plein feux » (relançable)
php artisan emsi:impact-live     # ajouter Impact Live et le campus de Saint-Louis à une base existante (relançable)
php artisan emsi:site-v3         # page L'École à deux campus, chiffres clés et agenda sur l'accueil (relançable)
php artisan emsi:site-v4 [--home] [--force]  # site des trois domaines : adresses, menus, pied de page, pages à compléter ; --home remplace l'accueil. Premier passage noté (settings.site_version = 4) : relancée, elle ne refait que les liens et la page EMSI ; --force reprend tout
php artisan emsi:domains-showcase  # pages d'essai des nouveaux blocs pour Playwright (--remove pour les retirer ; créées et supprimées par les tests)
./vendor/bin/pint

# Tout lancer en local (Laravel :8000, file d'attente, site :3000)
composer dev

# Frontend seul
cd frontend && npm install && npm run dev
npm run lint && npm run build
```

## 8. État du projet (au 30/09/2026)

- **Code terminé** : PR #1 à #9 fusionnées dans `main` (site « Plein feux » avec thème clair, univers, Impact Live, deux campus et deux caisses, héros « Œuvre d'art » avec son, choix des liens dans l'admin, titres lisibles quelle que soit la couleur, guide de déploiement corrigé, PHP 8.4 minimum). Suites vertes : 104 tests Laravel (SQLite et MySQL), 6 tests unitaires (`npm test`), 26 parcours Playwright.
- **Héros (branche `feat/heros-projection-studio-cinema`, 29/09/2026)** : nouvelles mises en page *Projection* (photo de fond, rubans, poursuite) et *Cinéma* (diaporama et chiffres clés) ; *Studio* refait (points posés sur le matériel dans l'admin via `HotspotPicker`, 1 à 3 morceaux à écouter). *Œuvre d'art* inchangée. Les héros sur photo gardent une palette de nuit en thème clair (`.scene-dark`). Pages d'essai e2e : `php artisan emsi:hero-showcase` (créées puis supprimées par Playwright, jamais par `db:seed`). Suites : 116 tests Laravel (SQLite et MySQL), 12 tests unitaires, 44 parcours Playwright (le serveur local de l'EMSI tourne sur le port 3001 : `E2E_BASE_URL=http://localhost:3001 npm run e2e`). Suite de la demande de Momar : 2. style des titres et textes dans l'admin, 4. passe visuelle, 3. paiement mensuel des étudiants.
- **Trois domaines (R1, branche `feat/trois-domaines`, 30/09/2026)** : terminé et vérifié en local (`emsi:site-v4 --home` appliqué à la base locale). Suites : 231 tests Laravel (SQLite et MySQL), 40 tests unitaires (`npm test`), 88 parcours Playwright (bureau et téléphone ; 8 ignorés selon l'écran ou les données). Suite : **R2 site bilingue FR/EN**, puis 2. style des titres, 3. paiement mensuel. Restes connus : **photos de la Maison à fournir** (puis un héros Cinéma sur l'accueil), textes « À compléter » à remplacer dans l'admin (agenda, espaces, campus, mission, partenaires, soutenir, presse), relancer `php artisan migrate` puis `emsi:site-v4` sur la base locale pour poser le repère de version et compléter la page EMSI (univers, réalisations ; `/univers` → `/emsi#univers`).
- **Prochaine étape — mise en ligne** : l'EMSI réserve `emsi.sn` (bureau d'enregistrement accrédité NIC Sénégal) et commande un OVHcloud VPS-2 (Ubuntu 24.04). Ensuite installation selon `DEPLOYMENT.md`, en **transférant la base et `storage/app` locales** (le contenu y est déjà saisi, déjà mis à niveau), pas en repartant d'un `db:seed` ; sur un `db:seed`, lancer `migrate` puis `emsi:site-v4 --home`.
- **Attendu de l'école** : photos, tarifs du studio, matériel à louer, dates de rentrée, coordonnées des quatre lieux, mentions légales, comptes de l'équipe, boîtes mail.
- **Documents** : présentation du site à Boubacar Tall et guide de mise en ligne (domaine, serveur, e-mails, budget), publiés comme pages privées claude.ai ; guide de l'équipe `docs/GUIDE-ADMIN.md`.
- **Restes techniques** : fichiers front hérités à la racine (`package.json`, `vite.config.js`, `tailwind.config.js`, `resources/css`) sans usage par Filament ; le parcours e2e de l'accueil dépend du contenu de la base locale.
- **Circuit de livraison** : branche dédiée → suites vertes → accord de Momar → push, PR, CI verte, fusion. Momar modifie souvent le contenu dans l'admin local : ne pas réécrire une page en base qu'il vient de modifier.
