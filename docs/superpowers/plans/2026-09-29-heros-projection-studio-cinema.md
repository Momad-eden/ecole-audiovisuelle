# Héros Projection, Studio et Cinéma — plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** ajouter les mises en page de héros « Projection » et « Cinéma », et remplacer le rendu « Studio » par une visite guidée du matériel avec écoute, le tout réglable dans l'admin.

**Architecture:** les données passent comme aujourd'hui : bloc `hero` de Filament (`PageBlocks::hero()`) → JSON de la page → `BlockResolver` (URL des médias, clés en camelCase) → `HeroBlock.tsx` qui aiguille vers un composant par mise en page. La logique canvas/son de `ArtHero.tsx` est extraite en hooks partagés sans changer son rendu.

**Tech Stack:** Laravel 13, Filament 5, PHPUnit ; Next.js 16 (App Router, TS strict, Tailwind 4), Playwright.

**Spec:** `docs/superpowers/specs/2026-09-29-heros-projection-studio-cinema-design.md`

## Global Constraints

- `masterpiece` (`ArtHero.tsx`) : rendu strictement inchangé.
- Aucun son en lecture automatique ; `prefers-reduced-motion` respecté ; animations suspendues hors écran (`IntersectionObserver`).
- Textes de l'interface en français, code en anglais ; libellés admin sans jargon.
- Hauteur de l'en-tête du site : `h-18` (4.5rem) — le cadre de Projection commence en dessous.
- Limites : `highlight` 40 car. ; `hotspots` ≤ 6, libellé 40 car., x/y en % 0–100 une décimale ; `tracks` ≤ 3 (MP3/M4A/WAV/OGG, 20 Mo ; titre 60 requis, crédit 80) ; `slides` 2–5 (surtitre 60, titre 80 requis, texte du lien 30) ; `facts` ≤ 4 (valeur 12, libellé 40) ; diapositive Cinéma : 6 s.
- Clés JSON côté front en camelCase (le `BlockResolver` convertit).
- Ne jamais lancer `migrate:fresh` ni `db:seed` sur la base locale (contenu réel). Aucune migration n'est nécessaire (les blocs sont du JSON).
- Contrôles avant chaque commit touché : `./vendor/bin/pint` (PHP), `npm run lint` (TS).

## Review Focus

- Titre contenant le mot en couleur avec une casse ou des espaces insécables différents (après `frenchSpacing`) → le mot doit quand même être coloré, ou le titre rester uni, jamais dupliqué ni tronqué (Task 4).
- Page Studio existante sans photo, sans points ni morceaux → pas d'erreur, fond nuit + `ConsoleArt`, aucun lecteur vide (Task 7).
- Cinéma avec une diapositive dont la photo a été supprimée de la médiathèque (`image: null`) → la diapositive est écartée par l'API, pas d'image cassée (Task 1).
- Point placé puis photo changée ou retirée dans l'admin → les points restent enregistrés mais ne s'affichent que s'il y a une photo (Task 7).
- Écran de 360 px de large → aucun défilement horizontal pour les trois héros (Task 9).

---

### Task 1: API — résolution des nouveaux champs du héros

**Files:**
- Modify: `app/Services/BlockResolver.php:60-61` (branche `'hero'`)
- Test: `tests/Feature/Api/PublicContentApiTest.php`

**Interfaces:**
- Produces (JSON `data.blocks.N.data` d'un héros) : `tracks: [{title, credits, url}]`, `slides: [{eyebrow, title, image: {url, alt}, link: {label, url} | null}]`, `hotspots: [{x, y, label}]`, `facts: [{value, label}]`, `highlight: string|null`.

- [ ] **Step 1: Write the failing tests** (même style que `test_mosaic_hero_images_are_resolved`, page publiée avec un bloc `hero`)

```php
public function test_studio_hero_tracks_and_hotspots_are_resolved(): void
// data: layout studio, tracks [['file' => 'pages/audio/a.mp3', 'title' => 'Démo', 'credits' => 'Mixé ici']],
// hotspots [['x' => 48.5, 'y' => 50, 'label' => 'Console'], ['x' => 140, 'y' => 20, 'label' => 'Hors cadre'], ['x' => 10, 'y' => 10, 'label' => '']]
// assert data.blocks.0.data.tracks.0.url === url('/storage/pages/audio/a.mp3'), tracks.0.credits === 'Mixé ici'
// assert data.blocks.0.data.hotspots === [['x' => 48.5, 'y' => 50, 'label' => 'Console']]

public function test_cinema_hero_slides_are_resolved_and_slides_without_photo_dropped(): void
// slides [['image' => 'pages/a.jpg', 'image_alt' => 'Salle', 'eyebrow' => 'Scène', 'title' => 'Un', 'link_label' => 'Voir', 'link_url' => '/univers/scene'],
//         ['image' => null, 'title' => 'Sans photo'], ['image' => 'pages/b.jpg', 'image_alt' => 'Régie', 'title' => 'Deux']]
// facts [['value' => '5', 'label' => 'filières']]
// assert slides count 2 ; slides.0.image.url === url('/storage/pages/a.jpg') ; slides.0.link === ['label' => 'Voir', 'url' => '/univers/scene'] ; slides.1.link === null
// assert facts.0.value === '5'
```

- [ ] **Step 2: Run** `php artisan test --filter=PublicContentApiTest` — Expected: les 2 nouveaux tests FAIL, les autres PASS.

- [ ] **Step 3: Implement** dans la branche `'hero'` : `tracks` au même format que le bloc `audio` (clé source `file`) ; `slides` via `withImages` puis filtre `image !== null`, `link` = `{label, url}` si `link_url` rempli sinon `null`, clés `link_label`/`link_url` retirées ; `hotspots` filtrés (x et y numériques dans [0, 100], libellé non vide après `trim`), x/y arrondis à 1 décimale ; `facts` et `highlight` passent tels quels. Extraire en méthodes privées `heroTracks(array)`, `heroSlides(array)`, `heroHotspots(array)` pour garder la `match` lisible.

- [ ] **Step 4: Run** `php artisan test --filter=PublicContentApiTest` — Expected: PASS (dont les tests héros existants).

- [ ] **Step 5: Commit** — `feat(api): morceaux, points, diapositives et chiffres du héros`

---

### Task 2: Admin — champ « Points sur la photo » (`HotspotPicker`)

**Files:**
- Create: `app/Filament/Forms/Components/HotspotPicker.php`
- Create: `resources/views/filament/forms/components/hotspot-picker.blade.php`
- Test: `tests/Feature/Admin/HotspotPickerTest.php`

**Interfaces:**
- Produces: `HotspotPicker::make(string $name): static`, `->imageField(string $field): static` (nom du champ frère qui contient la photo) ; état = `array<int, array{x: float, y: float, label: string}>`.

- [ ] **Step 1: Write the failing tests**

```php
public function test_state_is_normalised(): void
// dehydrate de [['x' => '48.56', 'y' => 50, 'label' => ' Console '], ['x' => 10, 'y' => 10, 'label' => '']]
// → [['x' => 48.6, 'y' => 50.0, 'label' => 'Console']]

public function test_more_than_six_points_is_rejected(): void
// 7 points → erreur de validation « 6 points au maximum. »

public function test_label_longer_than_40_characters_is_rejected(): void
```

- [ ] **Step 2: Run** `php artisan test --filter=HotspotPickerTest` — Expected: FAIL (classe absente).

- [ ] **Step 3: Implement** : `Field` Filament avec `dehydrateStateUsing` (normalisation ci-dessus, points sans libellé retirés) et règles de validation (≤ 6 ; libellé ≤ 40). Vue Blade + Alpine (`x-data` lié par `$wire.$entangle`) :
  - aperçu de la photo lue depuis le champ frère (`$get(imageField)`, URL publique via `Storage::disk('public')->url`) ; sans photo : message « Choisissez d'abord la photo du studio, puis enregistrez. » et champ inactif ;
  - clic sur l'aperçu → nouveau point à la position cliquée (en %, une décimale) et focus sur son libellé ;
  - liste sous l'aperçu : numéro, libellé (`input` 40 car.), bouton « Supprimer » ; glisser un point le déplace ;
  - plus de clic possible à 6 points, avec le message « 6 points au maximum ».

- [ ] **Step 4: Run** `php artisan test --filter=HotspotPickerTest` — Expected: PASS. Vérifier à la main dans `/admin` (page de test locale) : poser, déplacer, supprimer un point, enregistrer, recharger.

- [ ] **Step 5: Commit** — `feat(admin): placer les points du studio en cliquant sur la photo`

---

### Task 3: Admin — mises en page et champs du bloc héros

**Files:**
- Modify: `app/Filament/Support/PageBlocks.php:161-201` (`hero()`)
- Test: `tests/Feature/Admin/PageBlocksHeroTest.php` (nouveau)

**Interfaces:**
- Consumes: `HotspotPicker` (Task 2) — `HotspotPicker::make('hotspots')->imageField('image')`.
- Produces : clés stockées `highlight`, `hotspots`, `tracks[].{file,title,credits}`, `slides[].{image,image_alt,eyebrow,title,link_label,link_url}`, `facts[].{value,label}` ; valeurs de `layout` `projection`, `cinema`.

- [ ] **Step 1: Write the failing test** — instancier le bloc et vérifier le schéma :

```php
public function test_hero_block_offers_projection_and_cinema_layouts(): void
// options du Radio 'layout' contiennent les clés 'projection', 'cinema', 'studio', 'masterpiece'
// libellés : 'Projection (photo de fond, rubans de lumière, cartel)', 'Cinéma (diaporama plein écran et chiffres clés)', 'Studio (photo, matériel commenté et écoute)'

public function test_hero_block_declares_new_fields(): void
// le schéma contient les champs nommés highlight, hotspots, tracks, slides, facts
// tracks->getMaxItems() === 3 ; slides min 2 max 5 ; facts max 4
```

Pour lire le schéma : parcourir `Block::getChildComponents()` récursivement et indexer par `getName()`. Si Filament 5 exige un conteneur pour évaluer, utiliser `Livewire::test(EditPage::class, …)` sur une page existante et `assertFormFieldExists('blocks.….data.highlight')` — choisir la méthode qui marche sans monter de navigateur.

- [ ] **Step 2: Run** `php artisan test --filter=PageBlocksHeroTest` — Expected: FAIL.

- [ ] **Step 3: Implement** :
  - options `projection` (après `masterpiece`) et `cinema` (après `projection`) ; nouveau libellé de `studio` ;
  - `words` visible pour `stage` et `events` seulement ;
  - `sound` et `caption` visibles aussi pour `projection` (texte d'aide de `caption` : ajouter « Projection : le titre du cartel. ») ;
  - `highlight` : `TextInput`, libellé « Mot(s) du titre à mettre en couleur », `maxLength(40)`, aide « Doit figurer tel quel dans le titre. », visible `projection` et `studio` ;
  - `hotspots` : `HotspotPicker`, libellé « Points sur la photo », visible `studio` ;
  - `tracks` : `Repeater`, libellé « Morceaux à écouter », `maxItems(3)`, `defaultItems(0)`, champs `file` (même `FileUpload` que `sound` : disque `public`, dossier `pages/audio`, mêmes types, 20 Mo, `required`), `title` (60, requis), `credits` (80) ; visible `studio` ;
  - `slides` : `Repeater`, libellé « Diapositives », `minItems(2)->maxItems(5)`, `...self::image('image', 'Photo', required: true)`, `eyebrow` (60), `title` (80, requis), `link_label` (30), `link_url` via `LinkTargets::field('link_url', 'Lien')` (facultatif, requis si `link_label` rempli) ; visible `cinema` ; `addActionLabel('Ajouter une diapositive')` ;
  - `facts` : `Repeater`, libellé « Chiffres clés », `maxItems(4)`, `columns(2)`, `value` (12, requis), `label` (40, requis) ; visible `cinema`.

- [ ] **Step 4: Run** `php artisan test --filter=PageBlocksHeroTest && php artisan test` — Expected: PASS.

- [ ] **Step 5: Commit** — `feat(admin): mises en page Projection et Cinéma, champs du héros Studio`

---

### Task 4: Front — types et mot du titre en couleur

**Files:**
- Modify: `frontend/src/components/blocks/types.ts` (`HeroData`)
- Create: `frontend/src/lib/highlight.ts`
- Test: `frontend/src/lib/highlight.test.ts`

**Interfaces:**
- Produces:
  - `HeroData.layout` accepte en plus `"projection" | "cinema"` ; champs `highlight?: string | null`, `hotspots?: Hotspot[]`, `tracks?: HeroTrack[]`, `slides?: CinemaSlide[]`, `facts?: { value: string; label: string }[]` ;
  - `type Hotspot = { x: number; y: number; label: string }` ; `type HeroTrack = { title: string; credits?: string | null; url: string | null }` ; `type CinemaSlide = { eyebrow?: string | null; title: string; image: Image; link?: { label: string; url: string } | null }` (exportés de `types.ts`) ;
  - `splitHighlight(title: string, highlight?: string | null): [before: string, match: string, after: string] | null`.

- [ ] **Step 1: Write the failing tests** (projet Playwright « unitaire », comme `contrast.test.ts`)

```ts
test("coupe le titre autour du mot", () => expect(splitHighlight("Faites de votre passion un métier", "un métier")).toEqual(["Faites de votre passion ", "un métier", ""]));
test("ignore la casse et garde le texte d'origine", () => expect(splitHighlight("Le studio qui fait sonner Dakar", "SONNER")).toEqual(["Le studio qui fait ", "sonner", " Dakar"]));
test("tolère les espaces insécables de frenchSpacing", () => expect(splitHighlight("Écoutez !", "Écoutez !")).toEqual(["", "Écoutez !", ""]));
test("renvoie null si absent ou vide", () => { expect(splitHighlight("Titre", "autre")).toBeNull(); expect(splitHighlight("Titre", "  ")).toBeNull(); expect(splitHighlight("Titre", null)).toBeNull(); });
```

- [ ] **Step 2: Run** `cd frontend && npm test` — Expected: FAIL (module absent).
- [ ] **Step 3: Implement** : comparaison insensible à la casse, où espaces normaux et insécables (` `, ` `) sont équivalents ; la sortie reprend les caractères du titre d'origine ; première occurrence seulement.
- [ ] **Step 4: Run** `npm test && npm run lint` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(front): types des nouveaux héros et mot du titre en couleur`

---

### Task 5: Front — extraire rubans et son de l'Œuvre d'art

**Files:**
- Create: `frontend/src/components/blocks/art/useLightRibbons.ts`, `frontend/src/components/blocks/art/useArtSound.ts`
- Modify: `frontend/src/components/blocks/ArtHero.tsx`

**Interfaces:**
- Produces:
  - `useLightRibbons(sectionRef: RefObject<HTMLElement | null>, canvasRef: RefObject<HTMLCanvasElement | null>): { pointer: MutableRefObject<{ x: number; y: number; energy: number }>; follow: (event: React.PointerEvent<HTMLElement>) => { x: number; y: number } }` — contient `RIBBONS`, la boucle canvas, le suivi hors écran et le mouvement réduit ;
  - `useArtSound(src?: string | null): { listening: boolean; loading: boolean; toggle: () => Promise<void>; steer: (x: number, y: number, energy: number) => void }` — contient `SoundGraph`, le cache `decoded`, la fermeture au démontage.

- [ ] **Step 1: Déplacer** le code existant dans les deux hooks sans le modifier, et faire consommer les hooks par `ArtHero` (le cartel `readoutRef` reste dans `ArtHero`, qui l'alimente depuis la valeur renvoyée par `follow`).
- [ ] **Step 2: Run** `npm run lint && npm run build` — Expected: succès.
- [ ] **Step 3: Vérifier le rendu inchangé** : `npm run dev`, ouvrir une page en « Œuvre d'art » ; rubans qui suivent la souris, cartel qui affiche la fréquence, bouton « Écouter l'œuvre » fonctionnel ; `npm run e2e` — Expected: mêmes résultats qu'avant la tâche.
- [ ] **Step 4: Commit** — `refactor(hero): rubans et son de l'Œuvre d'art en hooks partagés`

---

### Task 6: Front — héros Projection

**Files:**
- Create: `frontend/src/components/blocks/ProjectionHero.tsx`
- Modify: `frontend/src/components/blocks/HeroBlock.tsx` (case `"projection"`), `frontend/src/app/globals.css` (poursuite, si une règle CSS est plus simple qu'un style en ligne)

**Interfaces:**
- Consumes: `useLightRibbons`, `useArtSound` (Task 5), `splitHighlight` (Task 4), `accentVars`, `MediaImage`, `ButtonLink`.
- Produces: `ProjectionHero({ data, first }: { data: HeroData; first: boolean })`, racine `<section class="projection-hero" data-testid="projection-hero">`, cadre `data-testid="projection-frame"`.

- [ ] **Step 1: Implement** selon la spec §2.1 :
  - calques : photo `MediaImage` (`priority={first}`, `brightness ≈ 0.42`) ; seconde photo identique, pleine luminosité et saturation, masquée par un `radial-gradient` centré sur les variables CSS `--px`/`--py`, mises à jour par `follow` sans rendu React ; dégradé nuit ; canvas des rubans (`mix-blend-mode: screen`) ;
  - cadre `absolute inset-x-4 bottom-4 top-[5.25rem] sm:inset-x-6 sm:bottom-6 sm:top-[5.75rem]`, avec les mêmes repères d'angle que l'Œuvre d'art ;
  - titre `display` plein, `<span class="text-[var(--accent-ink)]">` autour du morceau renvoyé par `splitHighlight` ;
  - cartel et bouton son identiques à l'Œuvre d'art ;
  - sans image, pas de calques photo ni de poursuite.
- [ ] **Step 2: Run** `npm run lint && npm run build` — Expected: succès.
- [ ] **Step 3: Commit** — `feat(hero): mise en page Projection`

---

### Task 7: Front — héros Studio

**Files:**
- Create: `frontend/src/components/blocks/StudioHero.tsx`, `frontend/src/components/blocks/studio/Hotspots.tsx`, `frontend/src/components/blocks/studio/StudioPlayer.tsx`
- Modify: `HeroBlock.tsx` (`"studio"` → `StudioHero` ; `StageHero` garde `stage` et `events`), `StageHero.tsx` (retirer `studio` de `Variant`, `WORD_COLORS`, `BEAMS`, `ACCENT` ; extraire le timecode REC en `useRecTimecode(ref)` exporté depuis `StageHero.tsx` ou `studio/useRecTimecode.ts`)

**Interfaces:**
- Consumes: `splitHighlight`, `Hotspot`, `HeroTrack` (Task 4) ; `useAudio`, `Track`, `Waveform`, `usePeaks` (existants) ; `ConsoleArt` (`HeroArt.tsx`).
- Produces: `StudioHero({ data, first })`, `Hotspots({ points }: { points: Hotspot[] })`, `StudioPlayer({ tracks, accent }: { tracks: HeroTrack[]; accent?: string | null })`.

- [ ] **Step 1: Implement `Hotspots`** : chaque point est un `button` positionné en `left:x% top:y%`, `aria-label` = libellé, `aria-expanded`, étiquette visible au survol, au focus ou au toucher (un seul point ouvert à la fois, `Échap` le referme) ; numéro visible sous 640 px ; composant frère `HotspotList` (liste `ol`, visible seulement sous 640 px). Rendu seulement si `data.image` existe.
- [ ] **Step 2: Implement `StudioPlayer`** : ne rend rien si aucun morceau n'a d'`url` ; morceau choisi par défaut = premier ; bouton lecture/pause (`useAudio().play({ src: url, title, subtitle: credits, accent })`, sinon `toggle`), titre et crédit, `Waveform` (`usePeaks(url, null, true)`, `progress` = `currentTime / duration` quand le morceau affiché est celui du lecteur global, sinon 0 ; `onSeek` actif seulement dans ce cas) ; boutons de morceau (`role="tablist"` inutile — de simples `button` avec `aria-pressed`) quand il y en a plus d'un.
- [ ] **Step 3: Implement `StudioHero`** selon la spec §2.2 : photo + dégradés gauche et bas, sans photo fond nuit + `ConsoleArt` ; voyant REC + timecode (`useRecTimecode`) ; titre avec `splitHighlight` ; sous-titre ; boutons ; `Hotspots` ; `StudioPlayer` en bas (au-dessus du bord, `backdrop-blur`) ; `data-testid="studio-hero"`.
- [ ] **Step 4: Run** `npm run lint && npm run build` — Expected: succès. Vérifier la page Studio d'Impact Live existante (héros en mise en page `studio`) : la page s'affiche sans erreur avec les anciennes données.
- [ ] **Step 5: Commit** — `feat(hero): Studio — matériel commenté et écoute des productions`

---

### Task 8: Front — héros Cinéma

**Files:**
- Create: `frontend/src/components/blocks/CinemaHero.tsx`
- Modify: `HeroBlock.tsx` (case `"cinema"`), `globals.css` (keyframes `ken-burns` si absents)

**Interfaces:**
- Consumes: `CinemaSlide` (Task 4), `useReducedMotion`, `MediaImage`, `ButtonLink`, `accentVars`.
- Produces: `CinemaHero({ data, first })`, `data-testid="cinema-hero"`, bouton pause `aria-label` « Mettre le diaporama en pause » / « Reprendre le diaporama », barres = `button` `aria-label` « Diapositive N sur M », diapositive affichée `aria-current="true"`.

- [ ] **Step 1: Implement** selon la spec §2.3 :
  - `DURATION = 6000` ms ; index courant en état ; minuterie active seulement si visible (`IntersectionObserver`), pas en pause, sans survol ni focus à l'intérieur, et hors mouvement réduit ;
  - la barre de la diapositive courante se remplit en CSS (durée 6 s, `animation-play-state` lié à la pause) ;
  - `ArrowLeft`/`ArrowRight` sur la section (`tabIndex={0}`, `aria-roledescription="diaporama"`, `aria-label` = titre du bloc) ;
  - titre : diapositive 0 → `Heading` (`h1` si `first`), les autres `h2` ; titre de secours = `data.title` si la première diapositive n'en a pas ;
  - images : `priority` pour la première seulement ;
  - bandeau `facts` + `buttons` (grille 2 × 2 au-dessus des boutons sous 640 px) ;
  - moins de 2 diapositives : afficher la première sans barres ni minuterie ; aucune : fond nuit avec `data.title`.
- [ ] **Step 2: Run** `npm run lint && npm run build` — Expected: succès.
- [ ] **Step 3: Commit** — `feat(hero): mise en page Cinéma`

---

### Task 9: Pages d'essai et parcours Playwright

**Files:**
- Create: `app/Console/Commands/HeroShowcaseCommand.php`, `frontend/e2e/global-setup.ts`, `frontend/e2e/global-teardown.ts`, `frontend/e2e/heroes.spec.ts`
- Modify: `frontend/playwright.config.ts` (`globalSetup`, `globalTeardown`)
- Test: `tests/Feature/HeroShowcaseCommandTest.php`

**Interfaces:**
- Produces: `php artisan emsi:hero-showcase` (crée ou met à jour 3 pages publiées, type `free`, slugs `essai-heros-projection`, `essai-heros-studio`, `essai-heros-cinema` ; copie `public/images/lieux/{grande_salle,studio_son,regie_broadcast}.jpg` vers `storage/app/public/pages/essai-heros/`, et un court son de démonstration généré en WAV) ; `--remove` supprime les 3 pages (avec leurs révisions) et le dossier `pages/essai-heros`.

- [ ] **Step 1: Write the failing test** : `emsi:hero-showcase` crée 3 pages publiées dont les blocs ont les mises en page `projection`, `studio` (2 morceaux, 3 points), `cinema` (3 diapositives, 4 chiffres) ; relancée, toujours 3 pages ; `--remove` → 0 page de ces slugs, aucun autre contenu touché (une page existante reste). `Storage::fake('public')`.
- [ ] **Step 2: Run** `php artisan test --filter=HeroShowcaseCommandTest` — Expected: FAIL.
- [ ] **Step 3: Implement** la commande (pages via `Page::firstOrNew` + `fill` + `publish()`, comme `ContentSeeder::page`) ; `global-setup.ts` / `global-teardown.ts` lancent `php artisan emsi:hero-showcase` / `--remove` depuis la racine du dépôt (`execFileSync("php", ["artisan", …], { cwd: "…/.." })`), sauf si `E2E_BASE_URL` pointe vers un autre hôte que `localhost`.
- [ ] **Step 4: Run** `php artisan test --filter=HeroShowcaseCommandTest` — Expected: PASS.
- [ ] **Step 5: Write `heroes.spec.ts`** (projets bureau et mobile) :

```ts
test("Projection : le cadre ne chevauche pas l'en-tête") // boundingBox(frame).y >= boundingBox(.site-header).y + height
test("Studio : les points ont un nom et s'ouvrent au clavier") // getByRole("button", { name: "Console 48 pistes" }) focus + Enter → étiquette visible
test("Studio : rien ne joue avant le clic, le premier morceau se lance au clic") // la barre de lecture globale absente, clic « Écouter … » → visible et en lecture
test("Cinéma : flèche droite passe à la diapositive 2, bouton pause arrête le défilement") // aria-current
test("Cinéma : en mouvement réduit, la diapositive ne change pas seule") // page.emulateMedia({ reducedMotion: "reduce" }), attendre 7 s → toujours la 1
test("aucun défilement horizontal à 360 px") // pour les 3 pages : document.documentElement.scrollWidth <= 360
```

- [ ] **Step 6: Run** `cd frontend && npm run e2e` (Laravel `composer dev` lancé) — Expected: nouveaux parcours PASS ; anciens parcours inchangés ; après exécution, `essai-heros-*` absents de la base.
- [ ] **Step 7: Commit** — `test(e2e): pages d'essai et parcours des nouveaux héros`

---

### Task 10: Vérification finale et documentation

**Files:**
- Modify: `CLAUDE.md` (§ 8 État du projet), `docs/GUIDE-ADMIN.md` (section héros : Projection, Studio avec points et morceaux, Cinéma)

- [ ] **Step 1: Run** `./vendor/bin/pint --test && php artisan test && composer test:mysql` — Expected: tout vert.
- [ ] **Step 2: Run** `cd frontend && npm run lint && npm test && npm run build && npm run e2e` — Expected: tout vert.
- [ ] **Step 3: Documenter** : guide admin (comment choisir la mise en page, placer un point, ajouter un morceau, composer des diapositives) ; CLAUDE.md §8 (héros ajoutés, nombre de tests à jour).
- [ ] **Step 4: Commit** — `docs: guide des nouveaux héros, état du projet`
