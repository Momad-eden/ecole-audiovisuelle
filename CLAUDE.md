# CLAUDE.md — Projet EMSI Dakar (école-audiovisuelle)

> Mémoire projet pour l'agent Claude (Antigravity IDE). À lire intégralement avant toute action.

## 1. Contexte

- **Client** : EMSI — École des Métiers du Son et de l'Image, Dakar.
- **Projet porté** : programme intégré EMSI × Grand Théâtre National Doudou Ndiaye Coumba Rose
  - **Volet 1** — Perfectionnement intensif (40 jeunes, 3 mois, sept.–nov. 2026)
  - **Volet 2** — Cycle BTS par la VAE (60 jeunes, 9 mois, démarrage 2027)
  - 5 filières : Son, Technicien Lumière, Régie Générale Spectacle, Infographie & Création Numérique, Cadrage Sportif & Régie Vidéo
  - Document source : `Copie de PROJET_EMSI_DAKAR_GRAND_THEATRE_FESTIVALS_EVENEMENTS.pdf` (racine du repo) = **référence de contenu**.
- **Écosystème** (depuis le 27/09/2026) : fondé par Boubacar Tall, ingénieur du son sénégalais basé à Saint-Louis : l'EMSI (campus de Dakar au Grand Théâtre et de Saint-Louis, mêmes formations), **Impact Live Studio** (studio d'enregistrement), **Impact Live Events** (location de sono, lumières, podiums et prestations) et l'**Espace Habib Faye** (centre culturel privé). Spec : `docs/superpowers/specs/2026-09-27-impact-live-design.md`.
- **Développeur / décideur** : Momar Diop (Halal Techno).

## 2. Objectif de la refonte

1. Corriger les incohérences (contenu, données, logique métier, sécurité).
2. Refaire un **espace admin** professionnel et cohérent.
3. Migrer le **frontend vers Next.js** (App Router, TypeScript). Laravel devient une **API**.
4. Mieux présenter l'école sur le site public.

## 2 bis. Principes directeurs (décisions de Momar — non négociables)

### A. La plateforme = l'école EMSI, pas le projet
- L'élément principal du site est **l'EMSI** : ses formations, ses ateliers, les réalisations de ses apprenants, son univers artistique.
- Le projet EMSI × Grand Théâtre (Volet 1 + BTS par la VAE) s'adresse à des **professionnels** (titulaires CPS/CS, techniciens en activité). Il est regroupé dans **une seule rubrique centralisée**, par exemple « **Espace Professionnels** » (`/professionnels`) : présentation du programme, les 2 volets, la VAE, les filières concernées, le calendrier, les partenaires, la candidature professionnelle.
- Le projet ne doit **pas** dominer l'accueil, le menu principal ou l'identité du site : un seul point d'entrée clair (entrée de menu + un bloc sur l'accueil), le reste vit dans sa rubrique.
- Les anciennes pages dispersées (`/projet`, `/vae`, parties de l'accueil et de `/ecole`) sont fusionnées dans cette rubrique.

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
- Navigation simple : Univers · Formations · L'École · Réalisations · Espace Pro, CTA « Candidater » toujours visible.

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
./vendor/bin/pint

# Tout lancer en local (Laravel :8000, file d'attente, site :3000)
composer dev

# Frontend seul
cd frontend && npm install && npm run dev
npm run lint && npm run build
```
