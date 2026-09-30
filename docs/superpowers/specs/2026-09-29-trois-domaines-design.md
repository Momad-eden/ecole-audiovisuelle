# R1 — Un site, trois domaines (Maison Habib Faye, EMSI, Impact Live Studio) — conception

- **Date** : 29/09/2026
- **Décideurs** : Boubacar Tall (demande), Momar Diop (validation)
- **Périmètre** : premier des deux sous-projets de la restructuration. **R2 (bilingue FR/EN)** suit, avant la mise en ligne. Dépend de la branche des héros Projection, Studio et Cinéma (`feat/heros-projection-studio-cinema`).
- **Maquettes validées** : accueil « Triptyque » (A) et arborescence (`.superpowers/brainstorm/…/content/accueil-directions.html`, `arborescence.html`).

## 1. Intention

Après présentation du site, Boubacar Tall demande :
- **un seul site** où l'on voit **la séparation des domaines** : la Maison de la culture Habib Faye, l'EMSI, Impact Live Studio (logé dans la Maison) ;
- **une navigation simple** : Accueil · Maison Habib Faye · EMSI (Campus de Dakar, Campus de Saint-Louis) ;
- **un site de culture et d'art de niveau international**, capable d'attirer des financements culturels ;
- **le Grand Théâtre National** n'est qu'un **partenaire** qui accueille le campus de Dakar ;
- dans **chaque campus** : ses formations, la candidature, etc.

Décisions (29/09/2026) :
- Impact Live Events (location de sono et lumière, prestations) est **retiré du site** ; ses données restent en base.
- Les **univers** (Son, Image, Infographie, Scène, Cinéma) restent l'organisation interne de l'EMSI.
- Les formations sont communes aux deux campus, mais **une formation peut n'exister que dans un campus**. La **VAE** est le projet de l'EMSI, pour les deux campus.
- Le site est **bilingue dès le lancement** (R2). R1 garde le français à la racine et laisse la place à `/en`.
- Nom de domaine : **`emsi.sn`** pour le moment. Le nom et le logo de l'en-tête viennent des Paramètres du site (EMSI aujourd'hui), pour pouvoir en changer sans code.

Cette décision remplace le principe A de CLAUDE.md (« la plateforme = l'école EMSI ») : CLAUDE.md sera mis à jour dans ce sous-projet.

## 2. Arborescence

| Adresse | Page | Domaine |
|---|---|---|
| `/` | Accueil | général |
| `/maison-habib-faye` | La Maison (Habib Faye, Boubacar Tall, mission) | maison |
| `/maison-habib-faye/agenda` | Programmation | maison |
| `/maison-habib-faye/studio` | Impact Live Studio | studio |
| `/maison-habib-faye/espaces` | Les espaces | maison |
| `/emsi` | L'école | emsi |
| `/emsi/dakar`, `/emsi/saint-louis` | Campus | emsi |
| `/emsi/formations`, `/emsi/formations/{slug}` | Catalogue et fiches | emsi |
| `/emsi/professionnels` (+ sous-pages actuelles) | VAE et professionnels | emsi |
| `/emsi/realisations` (+ fiches) | Réalisations | emsi |
| `/emsi/univers/{slug}` | Univers | emsi |
| `/candidater` | Candidature (campus puis formation) | emsi |
| `/mission`, `/partenaires`, `/soutenir`, `/presse` | Pages pour les financeurs | général |
| `/actualites`, `/contact`, mentions légales, confidentialité | Inchangées | général |

**Redirections permanentes (301)**, dans `frontend/next.config.ts` (`redirects()`, qui gère déjà les anciennes adresses du musée) car elles couvrent des sous-adresses : `/univers` → `/emsi#univers`, `/univers/:slug` → `/emsi/univers/:slug`, `/formations/:path*` → `/emsi/formations/:path*`, `/realisations/:path*` → `/emsi/realisations/:path*`, `/professionnels/:path*` → `/emsi/professionnels/:path*`, `/expositions/:path*` → `/emsi/realisations`, `/studio` → `/maison-habib-faye/studio`, `/ecole` → `/emsi`, `/espace-habib-faye` → `/maison-habib-faye`, `/agenda` → `/maison-habib-faye/agenda`, `/events` → `/maison-habib-faye`. Les redirections existantes vers ces anciennes adresses sont réécrites pour pointer directement vers les nouvelles (pas de chaîne de redirections). La table `redirects` de l'admin reste pour les cas ponctuels.

Le **menu** (admin, emplacement `main`) : Accueil · Maison Habib Faye ▾ (La Maison, Programmation, Impact Live Studio, Les espaces) · EMSI ▾ (L'école, Campus de Dakar, Campus de Saint-Louis, Formations, VAE et professionnels, Réalisations) · bouton **Candidater**. Le **pied de page** : Mission et impact, Partenaires et soutiens, Nous soutenir, Actualités, Presse, Contact, mentions légales.

## 3. Données (migrations réversibles, avec tests)

| Changement | Détail |
|---|---|
| `pages.domain` | `general` (défaut), `maison`, `emsi`, `studio` — enum `SiteDomain` avec libellés FR et couleur |
| `menu_items.parent_id` | Sous-menus, un seul niveau ; suppression du parent = enfants supprimés |
| `place_program` (pivot) | Campus où une formation est **disponible** ; seuls les lieux de type `campus` |
| `cohorts.place_id` (nullable) | Campus d'une session ; vide = les deux campus (VAE) |
| `contact_messages.organization` (nullable) | Pour « Nous soutenir » |

Couleurs de domaine : maison `#e0a84a` (ocre), emsi `var(--color-brand)` (orange du logo), studio `var(--color-rec)`, général : couleur de la marque. En thème clair, elles passent par les mêmes règles de contraste que les accents existants.

**Règle de disponibilité** : une formation est proposée dans un campus si ce campus est coché sur la formation **et** si elle a une offre ouverte dont la session est rattachée à ce campus ou à aucun campus.

## 4. Site public

### 4.1 En-tête, menus, repères
- Nom et logo : `settings.school_name` et `settings.logo` (existants).
- Menus déroulants : ouverture au survol (souris), au clic et au clavier (Entrée, Espace, Échap, flèches) ; `aria-expanded` ; en accordéon dans le menu mobile.
- Une page de domaine reçoit : la couleur du domaine (`--accent` sur la page), un **fil d'Ariane** (Accueil › Domaine › Page, avec JSON-LD `BreadcrumbList`) et une **barre de sous-navigation** construite depuis les enfants du menu du domaine (page courante marquée `aria-current`).
- Le domaine vient de `pages.domain` pour les pages à blocs, et est fixé en dur pour les routes dédiées (formations, univers, réalisations, professionnels, candidater : `emsi`).
- Sélecteur FR/EN : absent en R1.

### 4.2 Nouveaux blocs (admin + site)
| Bloc | Champs | Rendu |
|---|---|---|
| **Nos trois maisons** (`domains`) | 3 panneaux : photo + description, surtitre, titre, texte, lien, domaine (pour la couleur) ; phrase d'intention | Triptyque plein écran ; au survol souris un panneau s'élargit ; empilé sous 1024 px ; sans animation en mouvement réduit |
| **Formations de ce campus** (`campus_programs`) | campus (liste des campus) ; titre | Cartes des formations disponibles dans ce campus, prochaine rentrée du campus, bouton « Candidater » vers `/candidater?campus={slug}&formation={slug}` |
| **Documents à télécharger** (`downloads`) | titre ; fichiers (PDF, 20 Mo) avec titre et description | Liste avec type et poids du fichier |
| **Nous soutenir** (`support_form`) | titre, texte | Formulaire : nom, organisation, e-mail, téléphone, type de soutien (partenariat, mécénat, don, autre), message, consentement ; envoyé à `POST /api/v1/public/support` → `ContactMessage` sujet « Soutien / partenariat : {type} », limité en fréquence comme le contact |

Blocs existants réutilisés : héros (Projection, Studio, Cinéma…), texte, chiffres clés, agenda, actualités, partenaires, lieux, services, productions, formulaire de demande (limité à `studio_session` et `space_rental`), galerie, Espace Pro.

### 4.3 Candidature
- `/candidater` : étape 1 **campus** (Dakar, Saint-Louis), puis seules les formations disponibles dans ce campus. Paramètres `campus` et `formation` préremplis depuis les pages de campus.
- Laravel refuse une candidature dont l'offre n'est pas disponible dans le campus choisi (message clair en français).
- Le parcours professionnel (VAE) garde son formulaire actuel, sous `/emsi/professionnels/candidater`, avec le choix du campus.

### 4.4 Référencement
- Plan du site régénéré avec les nouvelles adresses.
- JSON-LD : `Organization` pour la Maison et pour le studio, `EducationalOrganization` pour l'EMSI et pour chaque campus (avec `address`).

## 5. Administration
- **Pages** : champ « Domaine » (liste FR) ; les nouveaux blocs dans la bibliothèque.
- **Menus** : champ « Sous-menu de » (menu principal seulement).
- **Formations** : cases « Disponible à… » (campus publiés).
- **Sessions** : champ « Campus » (« Les deux campus » par défaut).
- **Messages** : filtre « Soutien / partenariat », colonne Organisation.
- **Impact Live Events retiré** : ressources Catégories de matériel, Matériel, Packs masquées du menu (données gardées) ; types de demande `equipment_rental` et `event_service` retirés des formulaires publics ; le rôle `commercial` garde les séances studio et la location d'espaces. Liens de menu et blocs qui pointaient vers `/events` remplacés.

## 6. Mise à niveau du contenu : `php artisan emsi:site-v4`

Relançable, sans perte (même principe que `emsi:site-v3`) :
- crée les pages manquantes (Maison, Programmation, Espaces, EMSI, deux campus, Mission, Partenaires, Soutenir, Presse) avec des blocs de départ et des textes marqués « À compléter » ;
- **déplace** les pages existantes vers leur nouvelle adresse (`studio` → `maison-habib-faye/studio`, `ecole` → `emsi`, `espace-habib-faye` → `maison-habib-faye`, `professionnels` → contenu de `/emsi/professionnels`) en gardant leurs blocs et leur historique, et **ne réécrit jamais** une page déjà présente à la nouvelle adresse ;
- remplace l'accueil par le triptyque **uniquement** avec l'option `--home` (l'accueil actuel est gardé en révision) ;
- construit le menu principal et le pied de page, coche les deux campus sur les formations existantes et laisse les sessions existantes sur « Les deux campus » ;
- retire du menu et des blocs les liens vers Impact Live Events et les mentions du Grand Théâtre comme identité (surtitres « Dakar · Grand Théâtre National ») — le bloc « Le lieu (Grand Théâtre) » est déplacé sur la page du campus de Dakar.

## 7. Tests
- **Laravel** : migrations aller-retour ; disponibilité par campus (formation cochée ou non, session d'un campus, session des deux) ; candidature refusée hors campus ; `POST /support` (validation, limite de fréquence, message créé) ; API des blocs `domains`, `campus_programs`, `downloads` ; menus avec enfants dans `/site` ; `emsi:site-v4` relancé deux fois = même résultat, page modifiée par l'équipe non écrasée, accueil inchangé sans `--home`.
- **Playwright** : menu déroulant à la souris, au clavier et sur mobile ; triptyque (liens, empilement mobile) ; page de campus (formations du campus seulement, bouton Candidater prérempli) ; candidature filtrée par campus ; redirections des anciennes adresses ; fil d'Ariane et sous-navigation ; aucun défilement horizontal à 360 px.
- Suites complètes : `php artisan test`, `composer test:mysql`, `npm run lint`, `npm test`, `npm run build`, `npm run e2e`.

## 8. Hors périmètre
- Traduction anglaise, sélecteur de langue (R2).
- Contenu réel de la Maison (textes, photos), chiffres d'impact, rapports : fournis par l'école, saisis dans l'admin.
- Suppression des données d'Impact Live Events.
- Style des titres dans l'admin, paiement mensuel des étudiants (sous-projets suivants).
