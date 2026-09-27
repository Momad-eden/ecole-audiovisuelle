# CLAUDE.md — Projet EMSI Dakar (école-audiovisuelle)

> Mémoire projet pour l'agent Claude (Antigravity IDE). À lire intégralement avant toute action.

## 1. Contexte

- **Client** : EMSI — École des Métiers du Son et de l'Image, Dakar.
- **Projet porté** : programme intégré EMSI × Grand Théâtre National Doudou Ndiaye Coumba Rose
  - **Volet 1** — Perfectionnement intensif (40 jeunes, 3 mois, sept.–nov. 2026)
  - **Volet 2** — Cycle BTS par la VAE (60 jeunes, 9 mois, démarrage 2027)
  - 5 filières : Son, Technicien Lumière, Régie Générale Spectacle, Infographie & Création Numérique, Cadrage Sportif & Régie Vidéo
  - Document source : `Copie de PROJET_EMSI_DAKAR_GRAND_THEATRE_FESTIVALS_EVENEMENTS.pdf` (racine du repo) = **référence de contenu**.
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
- **Aucun contenu visible ne doit nécessiter de modifier du code** : textes, images, vidéos, sons, menus, pied de page, blocs de l'accueil, chiffres clés, FAQ, SEO, coordonnées, réseaux sociaux, couleurs d'accent des « salles ».
- Pages construites par **blocs (sections) réorganisables** depuis l'admin (glisser-déposer), à partir d'une bibliothèque de blocs prédéfinis et testés (héros, texte + image, galerie, lecteur audio, vidéo, chiffres clés, citation, appel à l'action, liste de formations, etc.). Pas d'éditeur HTML libre.
- **Médiathèque** centrale (images, vidéos, fichiers audio, PDF) avec recadrage, texte alternatif, crédits.
- **Brouillon / aperçu / publication** pour chaque contenu, historique des versions, restauration.
- Interface admin en français simple, sans jargon technique (« Publier », « Masquer du site », pas « is_active »), aides contextuelles, messages de confirmation clairs, impossibilité de casser la mise en page (champs limités, formats imposés, compression automatique des médias).
- Critère de recette : **une personne de l'administration de l'école, sans formation technique, doit pouvoir réaliser seule** : publier une actualité, ajouter une œuvre au musée, modifier la page d'accueil, ouvrir une session de candidature, traiter une candidature, encaisser un paiement.

### C. Le site public est un « musée numérique »
- Le site doit **faire ressentir l'art du son, de la lumière et de l'image** — comme la visite d'un musée numérique, pas une plaquette institutionnelle.
- Organisation en **salles / expositions** : Salle du Son, Salle de la Lumière, Salle de l'Image (cadrage, vidéo), Salle du Visuel (infographie, motion design), + expositions temporaires (ex. un festival, une promotion).
- Chaque **œuvre** (réalisation d'apprenant ou de l'école) a une fiche : médias (vidéo, photo, **audio avec lecteur et forme d'onde**), titre, auteurs/crédits, filière, promotion, matériel utilisé, récit de création.
- Direction artistique : fond sombre de salle d'exposition, la lumière comme élément de design (halos, faisceaux, dégradés), typographie éditoriale, grands médias plein écran, transitions douces, réactivité au son (visualisation audio). Toujours **sobre, lisible et performant** : respecter `prefers-reduced-motion`, chargement progressif des médias, aucun son en lecture automatique.
- Les formations, l'école, les actualités et l'Espace Professionnels s'intègrent dans cette expérience (même langage visuel), mais la navigation reste simple et classique en surface (menu clair, CTA « Candidater » visible).

## 3. Stack cible

| Couche | Choix |
|---|---|
| Backend | Laravel 12 (API only) + Sanctum (auth SPA par cookie) + API Resources + Policies |
| Base de données | **PostgreSQL** (remplace SQLite) |
| Frontend | Next.js (App Router) + TypeScript strict, dans `frontend/` |
| UI | Tailwind CSS + shadcn/ui, icônes lucide-react |
| Données client | TanStack Query (admin), fetch serveur + ISR/revalidate (public) |
| Formulaires | react-hook-form + zod (schémas alignés sur les FormRequest Laravel) |
| Tableaux admin | TanStack Table (tri, filtre, pagination serveur) |

Organisation : repo unique — Laravel reste à la racine, Next.js dans `frontend/`. Les vues Blade restent en place **jusqu'à parité validée**, puis sont supprimées.

## 4. Méthode de travail (OBLIGATOIRE)

Momar travaille en **phases à validation** : conception/audit séparés de l'implémentation.

- Travailler sur une branche git dédiée : `refonte/nextjs`. Commits petits et explicites.
- **Ne jamais passer à la phase suivante sans validation explicite de Momar.** À la fin de chaque phase : livrable + résumé + questions ouvertes, puis STOP.
- Phases : voir `docs/refonte/02-PLAN-MIGRATION.md`.
- Audit initial déjà réalisé : `docs/refonte/01-AUDIT.md` — le vérifier, le compléter, ne pas le recopier aveuglément.
- Ne jamais toucher à `.env`, ni supprimer de données, ni lancer `migrate:fresh` sur une base contenant des données réelles sans accord.
- Toute migration de schéma doit être **réversible** (`down()` correct) et accompagnée d'un test.

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
./vendor/bin/pint

# Frontend
cd frontend && npm install && npm run dev
npm run lint && npm run build
```
