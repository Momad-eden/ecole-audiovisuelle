# R1 — Un site, trois domaines — plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** réorganiser le site en trois domaines (Maison Habib Faye avec Impact Live Studio, EMSI avec deux campus, pages générales pour les financeurs), avec formations disponibles par campus, menus déroulants, repères de domaine, nouveaux blocs et retrait d'Impact Live Events.

**Architecture:** tout reste en pages à blocs (adresses à plusieurs niveaux déjà servies par `pages/{slug}` et `app/[...slug]`). Un champ `pages.domain` pilote couleur, fil d'Ariane et sous-navigation ; les menus gagnent un parent ; la disponibilité par campus passe par un pivot `place_program` et `cohorts.place_id`. Les routes Next dédiées (formations, univers, réalisations, professionnels) passent sous `app/emsi/`, les anciennes adresses redirigent dans `next.config.ts`. Une commande `emsi:site-v4` met le contenu à niveau sans perte.

**Tech Stack:** Laravel 13, Filament 5, MySQL 8.4 / SQLite (tests), Next.js 16 App Router, Tailwind 4, react-hook-form + zod, Playwright.

**Spec:** `docs/superpowers/specs/2026-09-29-trois-domaines-design.md`

## Global Constraints

- Interface et contenus en français ; code en anglais ; valeurs d'enum en snake_case ASCII.
- Migrations réversibles (`down()` correct) et testées ; ne jamais lancer `migrate:fresh` ni `db:seed` sur la base locale ; **demander à Momar avant de lancer `emsi:site-v4` sur la base locale** (après sauvegarde `mysqldump`).
- Tout contenu visible est modifiable dans l'admin ; libellés admin sans jargon.
- Couleurs de domaine : maison `#e0a84a`, emsi `#ff7a1a` (orange du logo), studio `#ff3b30`, general = couleur de marque. Passer par `accentVars` (contraste dans les deux thèmes).
- Accessibilité : menus au clavier (`aria-expanded`, Échap), `prefers-reduced-motion`, aucun défilement horizontal à 360 px.
- Les redirections de structure sont des 301 dans `frontend/next.config.ts` ; pas de chaîne de redirections.
- `settings.school_name` / `settings.logo` restent la source du nom et du logo de l'en-tête.
- Serveur local de l'EMSI : `E2E_BASE_URL=http://localhost:3001` (le port 3000 est occupé par un autre projet).
- Données e2e : les parcours des blocs et de la candidature (Tasks 6, 7) s'appuient sur `emsi:domains-showcase` (créée en Task 6, même principe que `emsi:hero-showcase` : pages `essai-domaines-*` et une formation d'essai « Essai — Dakar seulement », créées par `showcase.setup.ts`, supprimées par `showcase.teardown.ts` avec `--remove`, étape ajoutée dans `DEPLOYMENT.md`). Les parcours qui dépendent du vrai menu (menus déroulants, fil d'Ariane, sous-navigation : Tasks 4 et 5) sont écrits dans leur tâche mais exécutés après `emsi:site-v4` (Task 10) ; d'ici là, leur logique est couverte par les tests unitaires de `domainSection` et une vérification du rendu sur une page d'essai.

## Review Focus

- Formation cochée dans un seul campus mais dont la session est « les deux campus » → proposée seulement dans le campus coché (Task 1).
- Menu dont le parent est masqué ou pointe vers une page en brouillon → ses enfants ne s'affichent pas orphelins dans l'en-tête (Task 3).
- `emsi:site-v4` relancé après que l'équipe a modifié `maison-habib-faye` ou déplacé une page à la main → rien n'est écrasé ni dupliqué (Task 9).
- Ancien lien profond (`/formations/technicien-lumiere`, `/univers/son`, `/professionnels/candidater`) → arrive sur la bonne page en une seule redirection (Task 4).
- Candidat qui arrive sur `/candidater?campus=saint-louis&formation=x` alors que `x` n'existe qu'à Dakar → formulaire ouvert sur Saint-Louis sans formation présélectionnée, message clair (Task 7).

---

### Task 1: Données — domaines, sous-menus, disponibilité par campus

**Files:**
- Create: `database/migrations/2026_09_29_100000_add_domains_and_campus_availability.php`, `app/Enums/SiteDomain.php`
- Modify: `app/Models/Page.php`, `app/Models/MenuItem.php`, `app/Models/Program.php`, `app/Models/Cohort.php`, `app/Models/Offering.php`, `app/Models/ContactMessage.php`, `app/Models/Place.php`
- Test: `tests/Feature/Domain/CampusAvailabilityTest.php`, `tests/Feature/Domain/DomainsMigrationTest.php`

**Interfaces:**
- Produces:
  - `SiteDomain` enum : `GENERAL='general'`, `MAISON='maison'`, `EMSI='emsi'`, `STUDIO='studio'` ; `label(): string` (« Général », « Maison Habib Faye », « EMSI », « Impact Live Studio ») ; `color(): string` (hex de Global Constraints ; general `#ff7a1a`).
  - `Page::$casts['domain'] = SiteDomain::class` (défaut `general`).
  - `MenuItem::parent(): BelongsTo`, `MenuItem::children(): HasMany` (triés `position`), colonne `parent_id` (cascade à la suppression).
  - `Program::campuses(): BelongsToMany` (pivot `place_program`, `place_id` + `program_id`, unique).
  - `Cohort::place(): BelongsTo` (`cohorts.place_id` nullable, `nullOnDelete`).
  - `Offering::scopeAvailableAt(Builder $q, Place $campus): Builder` — offres ouvertes (`openForApplications`) dont la formation a ce campus coché **et** dont la session a `place_id` = ce campus ou null.
  - `Offering::isAvailableAt(?Place $campus): bool` (campus null → true s'il n'existe qu'un campus publié).
  - `contact_messages.organization` (nullable string 150) ; `ContactMessage::SUBJECTS` gagne `'support' => 'Soutien / partenariat'`.

- [ ] **Step 1: Write the failing tests**

```php
// CampusAvailabilityTest
test_offering_is_available_where_its_program_is_offered_and_session_matches()
// programme coché Dakar seulement, session sans campus → availableAt(dakar) contient l'offre, availableAt(saintLouis) non
test_a_session_tied_to_one_campus_is_only_offered_there()
// programme coché Dakar + Saint-Louis, session place_id = Saint-Louis → seulement Saint-Louis
test_closed_offerings_are_never_available()
// DomainsMigrationTest
test_migration_rolls_back_cleanly()   // artisan migrate:rollback --step=1 puis migrate ; colonnes/table absentes puis présentes
test_deleting_a_parent_menu_removes_its_children()
test_page_domain_defaults_to_general()
```

- [ ] **Step 2: Run** `php artisan test --filter="CampusAvailabilityTest|DomainsMigrationTest"` — Expected: FAIL (colonnes/classes absentes).
- [ ] **Step 3: Implement** migration (`pages.domain` string 20 défaut `general` ; `menu_items.parent_id` ; table `place_program` ; `cohorts.place_id` ; `contact_messages.organization`), enum, relations, scope.
- [ ] **Step 4: Run** `php artisan test` — Expected: tout PASS.
- [ ] **Step 5: Commit** — `feat(domaine): domaines de page, sous-menus et formations disponibles par campus`

---

### Task 2: Admin — domaine, sous-menus, campus, retrait d'Impact Live Events

**Files:**
- Modify: ressources Filament `Pages`, `MenuItems`, `Programs`, `Cohorts`, `ContactMessages`, `EquipmentCategories`, `EquipmentItems`, `RentalPacks` (dans `app/Filament/Resources/…`), `app/Filament/Support/PageBlocks.php` (bloc `booking_form` : types `studio_session`, `space_rental` seulement ; bloc `equipment_list` et `packs` retirés de la bibliothèque)
- Modify: `app/Http/Requests/Api/StoreBookingRequest.php` (types acceptés : `studio_session`, `space_rental`)
- Test: `tests/Feature/Admin/DomainsAdminTest.php`, `tests/Feature/Api/…` (réservation)

**Interfaces:**
- Consumes: Task 1 (colonnes, enum, relations).

- [ ] **Step 1: Write the failing tests**

```php
test_page_form_offers_the_domain_field()               // éditeur de page : « Domaine » avec les 4 libellés
test_menu_item_form_offers_a_parent_for_main_menu()    // « Sous-menu de » ; options = éléments main sans parent
test_program_form_offers_campus_availability()         // « Disponible à » avec les campus publiés
test_cohort_form_offers_a_campus()                     // « Campus » avec option « Les deux campus »
test_events_resources_are_hidden_from_navigation()     // EquipmentItemResource::shouldRegisterNavigation() === false (idem catégories, packs)
test_booking_api_rejects_equipment_rental_and_event_service() // POST booking-requests type equipment_rental → 422
```

- [ ] **Step 2: Run** — Expected: FAIL.
- [ ] **Step 3: Implement.** Libellés : « Domaine » (aide : « Donne sa couleur et son menu à la page »), « Sous-menu de », « Disponible à » (`CheckboxList`, relation `campuses`, options = `Place::campuses()`), « Campus » (`Select`, placeholder « Les deux campus »). Messages : colonne « Organisation », filtre par objet.
- [ ] **Step 4: Run** `php artisan test` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(admin): domaine des pages, sous-menus, campus des formations ; Impact Live Events retiré`

---

### Task 3: API — menus, domaine des pages, offres par campus, candidature, « Nous soutenir »

**Files:**
- Modify: `app/Http/Controllers/Api/Public/ContentController.php` (`site`, `pageResponse`, `offerings`), `app/Http/Resources/Public/OfferingResource.php` (ou équivalent), `app/Http/Requests/Api/StoreApplicationRequest.php`, `app/Http/Controllers/Api/Public/FormController.php`, `routes/api.php`
- Test: `tests/Feature/Api/DomainsApiTest.php`, `tests/Feature/Api/ApplicationCampusTest.php`, `tests/Feature/Api/SupportFormTest.php`

**Interfaces:**
- Produces (JSON) :
  - `site.menus.main[]` : `{label, url, isButton, children: [{label, url}]}` — enfants visibles seulement ; un parent masqué ou vers une page non publiée disparaît avec ses enfants ; enfants vers page non publiée retirés.
  - `site.domains` : `{general|maison|emsi|studio: {label, color}}`.
  - page : `domain: "general"|"maison"|"emsi"|"studio"`.
  - `GET offerings?campus={slug}` : seulement `availableAt` ; chaque offre expose `campusIds: number[]`.
  - `POST applications` : refus 422 sur `offeringId` avec « Cette formation n'est pas proposée dans ce campus. » si `! isAvailableAt(place)`.
  - `POST /api/v1/public/support` (throttle `contact`) : champs `name` (req, 150), `organization` (150), `email` (req sans téléphone), `phone`, `supportType` ∈ `partnership|sponsorship|donation|other`, `message` (req, 3000), `consent` accepted, `website` (pot de miel) → `ContactMessage` `subject='support'`, message préfixé « Type de soutien : {libellé FR} ».

- [ ] **Step 1: Write the failing tests** (un test par point ci-dessus, dont `test_children_of_a_hidden_parent_are_not_served` et `test_application_for_an_offering_absent_from_the_campus_is_rejected`).
- [ ] **Step 2: Run** — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** `php artisan test` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(api): menus déroulants, domaine des pages, offres par campus, formulaire « Nous soutenir »`

---

### Task 4: Front — adresses sous `/emsi` et redirections

**Files:**
- Move: `frontend/src/app/formations` → `frontend/src/app/emsi/formations`, `univers/[slug]` → `emsi/univers/[slug]`, `realisations` → `emsi/realisations`, `professionnels` → `emsi/professionnels` ; supprimer `app/univers/page.tsx`, `app/expositions`, `app/events`, `app/agenda` (remplacés par redirections et pages à blocs)
- Create: `frontend/src/app/emsi/[...slug]/page.tsx` (pages à blocs `emsi/*` : réutilise `CmsPageContent`), `frontend/src/app/emsi/page.tsx` (page `emsi`)
- Modify: `frontend/next.config.ts` (`redirects()` : la liste exacte de la spec §2, `permanent: true`, et les redirections existantes du musée pointées directement vers les nouvelles cibles), tous les liens internes codés (`grep -rn '"/formations\|"/univers\|"/realisations\|"/professionnels\|"/studio\|"/events\|"/agenda\|"/ecole' src`), `app/sitemap.ts` si des chemins y sont codés
- Test: `frontend/e2e/domains.spec.ts` (nouveau)

**Interfaces:**
- Produces: adresses `/emsi/formations/[slug]`, `/emsi/univers/[slug]`, `/emsi/realisations/[slug]`, `/emsi/professionnels/[slug]`, `/emsi/professionnels/candidater` ; pages à blocs `/emsi/<slug>` et `/maison-habib-faye/<slug>` via les catch-all.

- [ ] **Step 1: Write the failing test**

```ts
test("les anciennes adresses arrivent sur la nouvelle en une seule redirection", async ({ request }) => {
  for (const [from, to] of [["/formations", "/emsi/formations"], ["/univers/son", "/emsi/univers/son"], ["/professionnels/candidater", "/emsi/professionnels/candidater"], ["/studio", "/maison-habib-faye/studio"], ["/events", "/maison-habib-faye"], ["/ecole", "/emsi"]]) {
    const response = await request.get(from, { maxRedirects: 0 });
    expect(response.status(), from).toBe(308); // permanent de Next
    expect(new URL(response.headers()["location"], "http://x").pathname, from).toBe(to);
  }
});
```

- [ ] **Step 2: Run** `E2E_BASE_URL=http://localhost:3001 npx playwright test --project=bureau e2e/domains.spec.ts -g redirection` — Expected: FAIL.
- [ ] **Step 3: Implement** les déplacements, catch-all, redirections et liens.
- [ ] **Step 4: Run** le test, puis `npm run lint && npm run build` — Expected: PASS / succès.
- [ ] **Step 5: Commit** — `feat(site): formations, univers, réalisations et professionnels sous /emsi, anciennes adresses redirigées`

---

### Task 5: Front — menus déroulants et repères de domaine

**Files:**
- Modify: `frontend/src/lib/types.ts` (`MenuLink.children?: {label, url}[]`, `Site.domains`, `Page.domain`), `components/layout/SiteHeader.tsx` (menu depuis `site.menus.main` et ses enfants ; le déroulant automatique « Univers » est retiré), `components/layout/MobileMenu.tsx` (accordéon)
- Create: `components/layout/NavDropdown.tsx`, `components/layout/DomainChrome.tsx` (fil d'Ariane + sous-navigation + JSON-LD `BreadcrumbList`), `lib/domains.ts`
- Modify: `app/[...slug]/page.tsx`, `app/emsi/[...slug]/page.tsx`, pages des routes dédiées sous `/emsi` et `/candidater` (domaine `emsi`) pour envelopper le contenu de `DomainChrome` et `accentVars(domain.color)`
- Test: `frontend/src/lib/domains.test.ts`, `frontend/e2e/domains.spec.ts`

**Interfaces:**
- Produces:
  - `domainSection(menus: Site["menus"]["main"], path: string): { parent: MenuLink; current?: {label, url} } | null` — l'entrée de menu dont un enfant (ou elle-même) correspond au chemin (correspondance exacte, sinon plus long préfixe).
  - `NavDropdown({ link }: { link: MenuLink })` : bouton `aria-expanded` + liste ; ouvre au survol souris, au clic, Entrée/Espace ; Échap referme et rend le focus ; flèches haut/bas entre les liens.
  - `DomainChrome({ domain, site, path, title, children })`.

- [ ] **Step 1: Write the failing tests**

```ts
// domains.test.ts
test("retrouve la section d'une sous-page", () => expect(domainSection(menus, "/emsi/dakar")?.parent.label).toBe("EMSI"));
test("le plus long préfixe l'emporte", () => expect(domainSection(menus, "/emsi/formations/technicien-lumiere")?.current?.url).toBe("/emsi/formations"));
test("null hors des sections", () => expect(domainSection(menus, "/contact")).toBeNull());
// domains.spec.ts
test("le menu EMSI s'ouvre au clavier et se referme avec Échap")
test("sur téléphone, le menu EMSI s'ouvre en accordéon")
test("une page de campus affiche le fil d'Ariane et la sous-navigation EMSI, page courante marquée")
```

- [ ] **Step 2: Run** `npm test` et le spec e2e ciblé — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** `npm test`, e2e ciblé, `npm run lint && npm run build` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(site): menus déroulants, fil d'Ariane et couleur de chaque domaine`

---

### Task 6: Nouveaux blocs — triptyque, formations du campus, documents, « Nous soutenir »

**Files:**
- Modify: `app/Filament/Support/PageBlocks.php` (blocs `domains`, `campus_programs`, `downloads`, `support_form`), `app/Services/BlockResolver.php`
- Create: `frontend/src/components/blocks/DomainsBlock.tsx`, `CampusProgramsBlock.tsx`, `DownloadsBlock.tsx`, `frontend/src/components/forms/SupportForm.tsx` ; types dans `blocks/types.ts` ; cas dans `BlockRenderer.tsx`
- Test: `tests/Feature/Api/NewBlocksTest.php`, `tests/Feature/Admin/PageBlocksHeroTest.php` (ou nouveau `NewBlocksAdminTest.php`), `frontend/e2e/domains.spec.ts`

**Interfaces:**
- Produces (données résolues, camelCase) :
  - `domains` : `{intro?: string, panels: [{domain, eyebrow, title, text, image: Image|null, url, label}]}` — exactement 3 panneaux dans l'admin (`minItems(3)->maxItems(3)`), `color` ajouté par le resolver depuis `SiteDomain`.
  - `campus_programs` : `{title, campus: {id, name, slug, city}, items: [{title, slug, summary, cover: Image|null, nextStart: "YYYY-MM-DD"|null, applyUrl}]}` — `applyUrl = /candidater?campus={campus.slug}&formation={program.slug}` ; formations via `Offering::availableAt`.
  - `downloads` : `{title, files: [{title, description, url, size (octets), extension}]}` (PDF, 20 Mo, disque public `pages/documents`).
  - `support_form` : `{title, text}` ; le composant poste vers `/api/v1/public/support` (proxy Next existant des formulaires).
- Rendu `DomainsBlock` : 3 panneaux `grid lg:flex`, au survol souris `flex-grow` 1 → 1.6 (transition coupée en mouvement réduit), empilés sous `lg` ; chaque panneau est un lien entier avec nom accessible = titre ; couleur via `accentVars(panel.color)`.

- [ ] **Step 1: Write the failing tests** : résolution API des 4 blocs (dont `campus_programs` qui n'affiche pas une formation absente du campus, et `downloads` qui calcule `size`) ; admin : 3 panneaux exactement ; e2e : sur une page de test, triptyque cliquable et empilé à 390 px ; « Nous soutenir » envoyé → message de confirmation.
- [ ] **Step 2: Run** — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** `php artisan test`, `npm run lint && npm run build`, e2e ciblé — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(blocs): triptyque des trois maisons, formations du campus, documents à télécharger, « Nous soutenir »`

---

### Task 7: Candidature — le campus d'abord

**Files:**
- Modify: `frontend/src/app/candidater/page.tsx`, `frontend/src/app/emsi/professionnels/candidater/page.tsx`, `frontend/src/components/forms/ApplicationForm.tsx`, `frontend/src/lib/api.ts` (`offerings` avec `campusIds`)
- Test: `frontend/e2e/domains.spec.ts`

**Interfaces:**
- Consumes: `campusIds` (Task 3), paramètres `campus` (slug) et `formation` (slug de programme).
- Produces: étape 1 du formulaire = choix du campus (si plus d'un campus) ; la liste des formations est filtrée sur `campusIds.includes(placeId)` ; si la formation passée en paramètre n'existe pas dans le campus, elle n'est pas présélectionnée et le message « Cette formation n'est pas proposée à {campus}. Choisissez-en une autre ou changez de campus. » s'affiche.

- [ ] **Step 1: Write the failing tests** : « depuis la page du campus, la formation et le campus sont présélectionnés » ; « à Saint-Louis, une formation réservée à Dakar n'est pas proposée » ; « formation absente du campus demandé : message et aucune présélection ».
- [ ] **Step 2: Run** — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** e2e ciblé, `npm run lint && npm run build` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(candidature): choisir son campus, puis une formation proposée dans ce campus`

---

### Task 8: Référencement

**Files:**
- Modify: `app/Http/Controllers/Api/Public/ContentController.php` (`sitemap` : nouvelles adresses des routes dédiées, plus `/events`, `/univers`, `/expositions`, `/agenda`), `frontend/src/lib/metadata.ts` ou le composant JSON-LD existant (`grep -rn "application/ld+json" frontend/src`)
- Test: `tests/Feature/Api/PublicContentApiTest.php` (sitemap), `frontend/e2e/domains.spec.ts`

- [ ] **Step 1: Write the failing tests** : le plan du site contient `/emsi/formations/{slug}` et `/emsi/univers/{slug}`, plus aucune adresse `/univers/…`, `/formations/…`, `/events` ; la page d'un campus contient un JSON-LD `EducationalOrganization` avec `address`, la page Maison un `Organization`.
- [ ] **Step 2: Run** — Expected: FAIL.
- [ ] **Step 3: Implement.** JSON-LD par domaine : `maison`/`studio` → `Organization` ; `emsi` → `EducationalOrganization` ; page de campus (bloc `campus_programs` présent) → `EducationalOrganization` avec l'adresse du campus.
- [ ] **Step 4: Run** — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(seo): plan du site et données structurées des trois domaines`

---

### Task 9: Mise à niveau du contenu — `emsi:site-v4`

**Files:**
- Create: `app/Console/Commands/SiteV4Command.php`
- Modify: `database/seeders/ContentSeeder.php` (méthode `refreshSiteV4(bool $home): void`, sur le modèle de `refreshSiteV3`)
- Test: `tests/Feature/SiteV4CommandTest.php`

**Interfaces:**
- Produces: `php artisan emsi:site-v4 {--home}`.
  - Déplacements (si la cible n'existe pas) : `studio` → `maison-habib-faye/studio` (domain studio), `ecole` → `emsi` (emsi), `espace-habib-faye` → `maison-habib-faye` (maison), page `professionnels` → `emsi/professionnels` (emsi). Blocs, révisions et statut conservés (mise à jour du slug, pas de recréation).
  - Créations (si absentes, publiées, textes « À compléter ») : `maison-habib-faye/agenda`, `emsi/dakar`, `emsi/saint-louis` (bloc `campus_programs` du campus ; pour Dakar, bloc `venue` déplacé de l'accueil et bloc `partners` avec le Grand Théâtre), `maison-habib-faye/espaces` avec bloc `places` et `booking_form` (location d'espace), `mission`, `partenaires`, `soutenir` (bloc `support_form`), `presse` (bloc `downloads`).
  - Menus : `main` reconstruit selon la spec §2 (parents + enfants) ; `footer` : Mission et impact, Partenaires et soutiens, Nous soutenir, Actualités, Presse, Contact. Les anciens éléments sont masqués (`is_visible=false`), pas supprimés.
  - Formations existantes : les deux campus cochés si aucun n'est coché. Sessions : inchangées (`place_id` null).
  - Liens internes des blocs de toutes les pages réécrits vers les nouvelles adresses (`/events…` → `/maison-habib-faye`) ; surtitres de héros « Dakar · Grand Théâtre National » remplacés par « Dakar · Saint-Louis ».
  - `--home` : accueil republié avec héros Cinéma (une diapositive par domaine) + bloc `domains` + blocs actuels utiles (chiffres, agenda, actualités, partenaires) ; l'ancienne version reste en révision. Sans `--home`, l'accueil n'est touché que pour les liens et surtitres.

- [ ] **Step 1: Write the failing tests**

```php
test_pages_are_moved_with_their_blocks_and_history()
test_running_twice_gives_the_same_result()                 // mêmes nombres de pages, menus visibles, révisions
test_a_page_already_at_the_target_address_is_not_overwritten()
test_home_is_only_rebuilt_with_the_home_option()
test_main_menu_has_maison_and_emsi_with_their_children()
test_links_to_events_are_rewritten()
```

- [ ] **Step 2: Run** `php artisan test --filter=SiteV4CommandTest` — Expected: FAIL.
- [ ] **Step 3: Implement** (transaction, comme `SiteV3Command`).
- [ ] **Step 4: Run** `php artisan test` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(contenu): commande emsi:site-v4, du site EMSI au site des trois domaines`
- [ ] **Step 6: STOP — demander à Momar** : « Je sauvegarde ta base locale (`mysqldump` dans `storage/app/backups/`) puis je lance `emsi:site-v4 --home` ? » Ne continuer qu'après son accord.

---

### Task 10: Parcours complets, documentation, vérification finale

**Files:**
- Modify: `frontend/e2e/site.spec.ts` (adresses et titres qui ont changé), `frontend/e2e/domains.spec.ts`, `CLAUDE.md` (§1 écosystème, §2 bis A réécrit : « la plateforme = trois domaines », Impact Live Events retiré, §8), `docs/GUIDE-ADMIN.md` (domaines, sous-menus, campus des formations et des sessions, nouveaux blocs, « Nous soutenir »), `DEPLOYMENT.md` (`emsi:site-v4 --home` dans la mise à niveau)

- [ ] **Step 1:** Après `emsi:site-v4 --home` sur la base locale (Task 9, accord de Momar) : parcours e2e « l'accueil présente les trois domaines » (triptyque, trois liens), « la page du campus de Dakar présente le Grand Théâtre comme partenaire et ses formations » ; mettre à jour les parcours existants cassés par les nouvelles adresses.
- [ ] **Step 2: Run** `./vendor/bin/pint --test && php artisan test && composer test:mysql` — Expected: vert.
- [ ] **Step 3: Run** `cd frontend && npm run lint && npm test && npm run build && E2E_BASE_URL=http://localhost:3001 npm run e2e` — Expected: vert (relancer seul tout parcours en dépassement de délai et le noter).
- [ ] **Step 4: Documenter** (fichiers ci-dessus).
- [ ] **Step 5: Commit** — `docs: site des trois domaines — principes, guide de l'équipe, déploiement`
