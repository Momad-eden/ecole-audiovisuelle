# R2 — Site bilingue français / anglais — plan d'implémentation

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** rendre le site public bilingue (français à la racine, anglais sous `/en`), avec une traduction automatique gratuite (DeepL API Free) relue dans l'admin, et un repli sur le français partout.

**Architecture:** Laravel garde le français dans ses colonnes ; une table `translations` (morph) porte l'anglais champ par champ avec empreinte de la source et statut de relecture. Un service `Translator` (DeepL, factice, aucun) est appelé par une tâche en file à chaque modification publiée. L'API applique la langue demandée (`?locale=en`). Next.js passe toutes ses routes sous `app/[locale]`, un aiguillage sert le français sans préfixe, et des dictionnaires typés portent les textes fixes.

**Tech Stack:** Laravel 13, Filament 5, MySQL 8.4 / SQLite (tests), client HTTP Laravel (DeepL), Next.js 16 App Router, TypeScript strict, Playwright.

**Spec:** `docs/superpowers/specs/2026-09-30-site-bilingue-design.md`

## Global Constraints

- Aucun service de traduction payant ; DeepL API Free uniquement (`https://api-free.deepl.com/v2/…`, clé `DEEPL_API_KEY`), `source_lang=FR`, `target_lang=EN-GB`, `tag_handling=html` pour les champs HTML, 50 textes au plus par appel, quota : ne jamais dépasser 95 % de la limite renvoyée par `/v2/usage`.
- Sans clé : aucune tâche, saisie manuelle possible, site en repli français. Aucune erreur visible pour le visiteur.
- Les colonnes françaises existantes ne sont jamais modifiées par R2 ; migrations réversibles et testées ; ne jamais lancer `migrate:fresh` ni `db:seed` sur la base locale ; jamais d'appel réel à DeepL dans les tests (`Http::fake`).
- Statuts de traduction : `auto`, `reviewed`, `failed` ; libellés admin « Traduction automatique », « Relue », « Échec », « Pas encore traduit ».
- Un champ `reviewed` n'est jamais écrasé tant que l'empreinte du français n'a pas changé ; sinon l'ancienne valeur relue va dans `previous_value`.
- `Page` : seuls les `blocks` publiés sont traduits, jamais `draft_blocks`.
- Adresses françaises et redirections de R1 inchangées ; l'anglais vit sous `/en/…` avec les mêmes chemins ; jamais de redirection automatique selon la langue du navigateur.
- Admin en français ; droits de relecture/modification des traductions : rôles `directeur` et `communication`.
- Montants en FCFA dans les deux langues ; dates et nombres via `Intl` selon la langue.
- Serveur local de l'EMSI : `E2E_BASE_URL=http://localhost:3001` ; API sur :8000.

## Review Focus

- Bloc de page modifié en français (bloc ajouté ou déplacé) après traduction → l'anglais réappliqué par chemin, les nouvelles feuilles en français, jamais de texte anglais sous le mauvais bloc (Task 2).
- Texte HTML riche (listes, liens, gras) → balises, attributs `href` et `data-*` intacts après traduction (Task 3).
- Quota DeepL épuisé en cours de lancement (`--all`) → tâches reportées, aucune perdue, aucune marquée « Échec » à tort (Tasks 3, 7).
- Visiteur sur `/en/…` d'une page sans aucune traduction → interface en anglais, contenu en français balisé `lang="fr"` (la réponse de page porte `contentLocale: "fr"|"en"` selon que ses champs principaux sont traduits ; le site pose `lang` sur le conteneur du contenu) (Tasks 5, 9).
- Lien partagé ou favori vers une ancienne adresse sous `/en` (`/en/formations/x`) → une seule redirection vers `/en/emsi/formations/x` (Task 8).

---

### Task 1: Données — table des traductions, trait, langue des demandes

**Files:**
- Create: `database/migrations/2026_09_30_120000_create_translations_table.php`, `database/migrations/2026_09_30_120100_add_locale_to_public_requests.php`, `app/Models/Translation.php`, `app/Models/Concerns/HasTranslations.php`, `app/Enums/TranslationStatus.php`
- Modify: les modèles du tableau §2.2 de la spec (déclarer `protected array $translatable`), `Application`, `ContactMessage`, `BookingRequest` (fillable `locale`)
- Test: `tests/Feature/Translation/TranslationStorageTest.php`

**Interfaces:**
- Produces:
  - `TranslationStatus` : `AUTO='auto'`, `REVIEWED='reviewed'`, `FAILED='failed'` ; `label()` FR.
  - `Translation` : colonnes de la spec §2.1 ; casts `status`, `value` brut (texte) ; `morphTo translatable`.
  - Trait `HasTranslations` : `translations(): MorphMany` ; `translatableFields(): array` ; `translation(string $field, string $locale = 'en'): ?Translation` ; `translated(string $field, string $locale): mixed` (valeur anglaise décodée — JSON pour blocs/tableaux — sinon valeur française) ; `sourceHash(string $field): string` (sha256 de la valeur française normalisée JSON) ; `outdatedFields(string $locale = 'en'): array` (champs sans traduction ou dont `source_hash` diffère) ; suppression des traductions à la suppression de la fiche.
  - Colonne `locale` (string 5, défaut `fr`) sur `applications`, `contact_messages`, `booking_requests`.

- [ ] **Step 1: Write the failing tests** : aller-retour des deux migrations ; `translated()` renvoie l'anglais si présent, le français sinon, décode le JSON pour `blocks` et les champs tableau ; `outdatedFields()` liste un champ dont le français a changé ; suppression d'une fiche → ses traductions supprimées ; un modèle non traduit (Partner) n'a pas le trait.
- [ ] **Step 2: Run** `php artisan test --filter=TranslationStorageTest` — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** `php artisan test` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(traduction): table des traductions et champs traduits des contenus`

---

### Task 2: Textes des blocs — extraction et réapplication

**Files:**
- Create: `app/Support/Translation/BlockTexts.php`
- Test: `tests/Unit/BlockTextsTest.php`

**Interfaces:**
- Produces:
  - `BlockTexts::extract(array $blocks): array<string, string>` — clés = chemins pointés (`0.data.title`, `2.data.slides.1.title`, `3.data.buttons.0.label`) ; seulement les feuilles texte non vides dont la clé figure dans la liste fermée de la spec §2.3 ; `words` (tableau de chaînes) → une entrée par élément ; jamais `url`, `link_url`, images, fichiers, couleurs, `layout`, `type`, `domain`, identifiants ; `isHtml(string $path): bool` pour `body` et les champs riches.
  - `BlockTexts::apply(array $blocks, array $texts): array` — renvoie `$blocks` où chaque chemin présent dans `$texts` et existant dans la structure reçoit la valeur ; chemins absents ignorés.
  - `BlockTexts::keyed(array $blocks): array` — même extraction mais indexée par une clé stable `{index de bloc}:{type}:{chemin relatif}` pour réappliquer après ajout/suppression de blocs ; la valeur anglaise d'un champ `blocks` est stockée sous cette forme (`{clé stable: texte}`), et `applyKeyed(array $blocks, array $keyedTexts)` la réapplique en tolérant les déplacements (même type et même rang parmi les blocs de ce type).

- [ ] **Step 1: Write the failing tests** (données réelles : héros Cinéma avec slides et facts, héros Studio avec hotspots et tracks, bloc `domains`, `text` HTML, `faq`, boutons) : extraction exacte ; liens/images/couleurs absents ; `apply` idempotent ; réapplication après insertion d'un bloc en tête (les anglais restent sur leur bloc) ; bloc supprimé → ses textes ignorés ; bloc nouveau → pas de texte anglais (reste français).
- [ ] **Step 2: Run** `php artisan test --filter=BlockTextsTest` — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(traduction): textes des blocs extraits et réappliqués sans toucher liens ni images`

---

### Task 3: Service de traduction — DeepL gratuit, factice, aucun ; quota ; glossaire

**Files:**
- Create: `app/Services/Translation/Translator.php` (interface), `DeepLTranslator.php`, `NullTranslator.php`, `FakeTranslator.php` (tests), `TranslationQuota.php`, `DeepLGlossary.php`
- Modify: `config/services.php` (`deepl.key`, `deepl.url` défaut `https://api-free.deepl.com`), `app/Providers/AppServiceProvider.php` (liaison : DeepL si clé, sinon Null), `.env.example` (`DEEPL_API_KEY=` commenté)
- Test: `tests/Feature/Translation/DeepLTranslatorTest.php`

**Interfaces:**
- Produces:
  - `interface Translator { public function isAvailable(): bool; public function translate(array $texts, array $htmlKeys = []): array; }` — `$texts` = `[clé => texte]`, renvoie les mêmes clés ; lève `TranslationFailed` (réponse invalide, erreur) ou `QuotaExceeded`.
  - `DeepLTranslator` : envoie par lots de 50 (`text[]`), `source_lang=FR`, `target_lang=EN-GB`, `preserve_formatting=1`, `tag_handling=html` pour les lots HTML (lots séparés HTML / texte), `glossary_id` si présent ; en-tête `Authorization: DeepL-Auth-Key {clé}` ; vérifie le nombre de textes renvoyés et l'absence de vide ; codes 429/5xx → exception réessayable, 456 → `QuotaExceeded`.
  - `TranslationQuota::canSend(int $characters): bool` (lit `/v2/usage`, mis en cache 10 min, `false` si `character_count + $characters > 0.95 * character_limit`) ; `usage(): array{used:int, limit:int}`.
  - `DeepLGlossary::sync(array $pairs): ?string` — supprime l'ancien glossaire et en crée un (`/v2/glossaries`, `source_lang=fr`, `target_lang=en`, entrées TSV) ; renvoie l'id, stocké dans `settings.deepl_glossary_id` (colonne ajoutée ici, migration réversible).

- [ ] **Step 1: Write the failing tests** (`Http::fake`) : requête exacte (URL, en-tête, paramètres, glossaire) ; lots de 50 ; HTML envoyé avec `tag_handling=html` et renvoyé intact (lien `href` conservé) ; nombre de textes incohérent → `TranslationFailed` ; 456 → `QuotaExceeded` ; `canSend` à 94 % oui, à 96 % non ; sans clé → `NullTranslator::isAvailable() === false` ; `sync` recrée le glossaire.
- [ ] **Step 2: Run** `php artisan test --filter=DeepLTranslatorTest` — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** `php artisan test` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(traduction): service DeepL gratuit avec quota et glossaire`

---

### Task 4: Chaîne de traduction — déclenchement, tâche, relecture, réglages

**Files:**
- Create: `app/Jobs/TranslateRecord.php`, `app/Observers/TranslatableObserver.php` (ou dans le trait `bootHasTranslations`), migration `settings` : `auto_translate` (bool, défaut vrai), `translation_glossary` (JSON, prérempli par la migration avec les noms de la spec §3.2)
- Modify: `app/Models/Setting.php`, `app/Filament/Pages/SiteSettings.php` (+ vue) : interrupteur, lexique (répéteur « Terme français » / « Traduction anglaise »), état du quota ; à l'enregistrement du lexique → `DeepLGlossary::sync`
- Test: `tests/Feature/Translation/TranslationPipelineTest.php`

**Interfaces:**
- Consumes: Tasks 1–3.
- Produces:
  - `TranslateRecord(Model $record)` (`ShouldBeUnique` par `type:id`, 3 tentatives, attente croissante) : calcule `outdatedFields('en')` ; ignore les champs `reviewed` dont l'empreinte est à jour ; pour `Page`, ne traite que `blocks` publiés, `title`, `seo` ; construit les textes (champs simples, tableaux élément par élément, `BlockTexts::keyed` pour `blocks`, `seo.title`/`seo.description`) ; vérifie `TranslationQuota::canSend` sinon `release()` au 1er du mois suivant ; appelle `Translator::translate` ; enregistre chaque champ (`value`, `source_hash`, `status=auto`, `translated_at`, et `previous_value` si le champ était `reviewed`) ; en échec définitif, `status=failed` sur les champs concernés.
  - Déclenchement : `saved` d'un modèle traduit → si `Setting::current()->auto_translate` et `Translator::isAvailable()` et `outdatedFields()` non vide → `TranslateRecord::dispatch` ; `Page` : seulement si `blocks` (publiés) ou `title`/`seo` ont changé.

- [ ] **Step 1: Write the failing tests** (`FakeTranslator` qui préfixe « EN: ») : publier une page → tâche et traductions `auto` ; modifier `draft_blocks` seul → aucune tâche ; champ relu + français inchangé → non retraduit ; champ relu + français changé → retraduit, `previous_value` = ancien anglais relu ; quota insuffisant → tâche relâchée, aucune traduction, aucun `failed` ; `TranslationFailed` après 3 essais → `failed` ; interrupteur coupé → aucune tâche ; clé absente → aucune tâche ; enregistrer le lexique → glossaire resynchronisé (`Http::fake`).
- [ ] **Step 2: Run** `php artisan test --filter=TranslationPipelineTest` — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** `php artisan test` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(traduction): traduction automatique à chaque publication, relecture respectée`

---

### Task 5: API publique en anglais

**Files:**
- Create: `app/Http/Middleware/SetPublicLocale.php` (lit `?locale=` ∈ {fr, en}, défaut fr, `App::setLocale`), `lang/en/validation.php` (+ fichiers de messages nécessaires), `lang/en.json` si des chaînes `__()` publiques existent
- Modify: `routes/api.php` (middleware sur le groupe public), toutes les ressources `app/Http/Resources/Public/*`, `ContentController` (site : menus, réglages, domaines ; page : `title`, `seo`, `blocks` traduits via `BlockTexts::applyKeyed`, + `locale`, `alternates`), `BlockResolver` (données incluses : formations, univers, réalisations, actualités, agenda, services, lieux, FAQ), `FormController` + requêtes de formulaires (`locale` enregistré)
- Test: `tests/Feature/Api/LocalizedApiTest.php`

**Interfaces:**
- Consumes: `HasTranslations::translated()`, `BlockTexts::applyKeyed()`.
- Produces: toute ressource publique lit ses champs traduits via `$this->translated('champ', app()->getLocale())` ; page : `locale: "fr"|"en"`, `contentLocale: "fr"|"en"` (`en` si le titre de la page est traduit), `alternates: {fr: "/…", en: "/en/…"}` (accueil : `/` et `/en`) ; formulaires : `locale` enregistré depuis la requête (`fr` par défaut) ; messages de validation en anglais quand `?locale=en`.

- [ ] **Step 1: Write the failing tests** : `GET pages/emsi?locale=en` → titre et textes de blocs anglais, liens identiques ; bloc ajouté après traduction → en français, les autres en anglais ; sans traduction → français ; `site?locale=en` → libellés de menus anglais ; formations, actualités, lieux, FAQ traduits via leurs points d'accès et via les blocs dynamiques ; `POST applications?locale=en` invalide → messages anglais ; demande enregistrée avec `locale=en` ; `?locale=de` → français.
- [ ] **Step 2: Run** `php artisan test --filter=LocalizedApiTest` — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** `php artisan test` — Expected: PASS (les tests d'API existants inchangés).
- [ ] **Step 5: Commit** — `feat(api): contenus et messages en anglais avec repli français`

---

### Task 6: Admin — onglet « Anglais », traductions à relire, droits

**Files:**
- Create: `app/Filament/Support/TranslationTab.php` (fabrique d'onglet réutilisable : un bloc par champ, français en lecture seule à côté de l'anglais modifiable, statut, actions), `app/Filament/Resources/Translations/TranslationResource.php` (+ pages : liste « Traductions à relire »), `app/Filament/Widgets/TranslationsOverview.php`, `app/Policies/TranslationPolicy.php`
- Modify: les ressources Filament des modèles traduits (ajout de l'onglet ; les formulaires existants passent dans des onglets « Français » / « Anglais » si ce n'est pas déjà le cas)
- Test: `tests/Feature/Admin/TranslationAdminTest.php`

**Interfaces:**
- Consumes: Tasks 1, 2, 4.
- Produces:
  - Onglet « Anglais » : pour `blocks`, une ligne par feuille texte (libellé = « Bloc N · type · champ », texte français, texte anglais) ; enregistrer une valeur modifiée → `status=reviewed`, `reviewed_at`, `reviewed_by` ; action « Marquer comme relue » (sans modification) ; action « Retraduire » (met `TranslateRecord` en file pour ce champ, désactivée si la traduction automatique n'est pas disponible, avec explication) ; affichage de `previous_value` quand il existe (« Ancienne version relue »).
  - « Traductions à relire » (menu « Contenu ») : colonnes Type, Fiche (lien vers l'onglet Anglais), Champ, Statut, Date ; filtres type/statut ; par défaut `auto` et `failed`.
  - Widget tableau de bord : nombre à relire, nombre en échec, quota du mois, ou « Traduction automatique non configurée ».
  - Droits : `directeur`, `communication` modifient ; autres rôles lisent ; `commercial` ne voit pas la liste.

- [ ] **Step 1: Write the failing tests** : l'éditeur d'une page affiche l'onglet « Anglais » avec les feuilles texte des blocs ; enregistrer un texte anglais → `reviewed` et valeur stockée sous la clé stable ; « Marquer comme relue » ; « Retraduire » met une tâche en file (`Queue::fake`) ; la liste affiche `auto` et `failed` ; un `secretaire` ne peut pas modifier ; widget sans clé → message « non configurée ».
- [ ] **Step 2: Run** — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** `php artisan test` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(admin): onglet Anglais, traductions à relire et suivi du quota`

---

### Task 7: Commande de lancement `emsi:translate`

**Files:**
- Create: `app/Console/Commands/TranslateCommand.php`
- Test: `tests/Feature/Translation/TranslateCommandTest.php`

**Interfaces:**
- Produces: `php artisan emsi:translate {--all} {--model=} {--dry-run} {--force}` ; ordre : pages, formations, filières, univers, menus, lieux, services, réalisations, agenda, FAQ, actualités (les plus récentes d'abord) ; ne prend que le contenu publié dont `outdatedFields('en')` n'est pas vide ; affiche nombre de fiches, caractères, quota restant, confirmation ; met les `TranslateRecord` en file ; `--dry-run` ne met rien en file et n'appelle pas `/v2/usage` si la clé est absente.

- [ ] **Step 1: Write the failing tests** : `--dry-run` compte les caractères sans rien mettre en file ; `--all --force` met une tâche par fiche à traduire, dans l'ordre, ignore les brouillons et les fiches à jour ; `--model=News` limite ; clé absente → message clair et code de sortie succès sans tâche.
- [ ] **Step 2: Run** — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** `php artisan test` — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(traduction): commande de traduction de lancement dans la limite gratuite`

---

### Task 8: Site — routes sous `[locale]`, aiguillage, API avec langue

**Files:**
- Move: tout `frontend/src/app/*` public (pages, layouts, `[...slug]`, `emsi`, `maison-habib-faye`, `candidater`, `actualites`, `apercu`, `not-found`, `error`, `opengraph-image`) → `frontend/src/app/[locale]/…` ; `api/`, `robots.ts`, `sitemap.ts` restent à la racine
- Create: `frontend/src/proxy.ts` (Next 16 ; sinon `middleware.ts`) : adresses sans préfixe → réécriture interne vers `/fr/…` ; `/en/…` passe ; `/fr/…` explicite → redirection 308 vers l'adresse sans préfixe ; ignore `/api`, `/_next`, `/storage`, fichiers ; `frontend/src/lib/i18n/locales.ts` (`LOCALES = ["fr","en"] as const`, `Locale`, `isLocale`, `localizedPath(path, locale)`)
- Modify: `frontend/src/lib/api.ts` (chaque appel prend `locale` et ajoute `?locale=`), toutes les pages (passer `params.locale`), `generateStaticParams`/`dynamicParams` si utilisés, `next.config.ts` (chaque redirection R1 dupliquée sous `/en/…` vers `/en/…`), tous les liens internes construits (`Link href`) via `localizedPath`
- Test: `frontend/e2e/i18n.spec.ts`

**Interfaces:**
- Produces: `localizedPath(path: string, locale: Locale): string` (`fr` → inchangé, `en` → `/en` + path, `/` → `/en`) ; `api.*(…, locale)` ; `params: Promise<{ locale: Locale; … }>` dans toutes les pages.

- [ ] **Step 1: Write the failing tests** : `/emsi/dakar` 200 ; `/en/emsi/dakar` 200 ; `/fr/emsi` → 308 `/emsi` ; `/en/formations/x` → une redirection vers `/en/emsi/formations/x` ; `/en/xyz-inconnu` → 404 ; les liens internes d'une page `/en/…` pointent vers `/en/…`.
- [ ] **Step 2: Run** `E2E_BASE_URL=http://localhost:3001 npx playwright test --project=bureau e2e/i18n.spec.ts` — Expected: FAIL.
- [ ] **Step 3: Implement** (`git mv` pour les déplacements).
- [ ] **Step 4: Run** le test, `npm run lint && npx tsc --noEmit -p . && npm run build`, puis la suite e2e existante — Expected: PASS (adresses françaises inchangées).
- [ ] **Step 5: Commit** — `feat(site): routes par langue, français à la racine, anglais sous /en`

---

### Task 9: Site — dictionnaires, sélecteur de langue, formats

**Files:**
- Create: `frontend/src/lib/i18n/dictionaries/fr.ts`, `en.ts` (`en` typé `Dictionary = typeof fr` : clé manquante = erreur de compilation), `frontend/src/lib/i18n/index.ts` (`getDictionary(locale)`, contexte client `useT()`), `frontend/src/lib/i18n/format.ts` (`formatDate`, `formatNumber`, `formatMoney` FCFA), `frontend/src/components/layout/LanguageSwitcher.tsx`
- Modify: tous les composants et pages contenant des textes fixes (en-tête, pied de page, menu mobile, fil d'Ariane, héros — « Écouter l'œuvre », diaporama —, lecteur audio, blocs, formulaires et schémas zod, messages, 404, erreurs, confirmations, métadonnées fixes) ; `formatDate` existant remplacé
- Test: `frontend/src/lib/i18n/dictionaries.test.ts`, `frontend/src/lib/i18n/format.test.ts`, `frontend/e2e/i18n.spec.ts`

**Interfaces:**
- Produces: `getDictionary(locale): Dictionary` (serveur) ; `useT(): Dictionary` (client, via fournisseur dans le layout `[locale]`) ; `LanguageSwitcher` : liens « FR » / « EN » vers `alternates` de la page (ou `localizedPath` du chemin courant pour les routes dédiées), `aria-current` sur la langue active, `hreflang`/`lang` sur chaque lien, cookie `emsi-locale` mémorisé (aucune redirection automatique).

- [ ] **Step 1: Write the failing tests** : unitaire — mêmes clés dans `fr` et `en`, aucune valeur vide ; `formatDate("2026-10-05","en")` = « 5 October 2026 » (en-GB), `fr` = « 5 octobre 2026 » ; `formatMoney(1250000,"en")` contient « FCFA » ; e2e — sur `/emsi/dakar`, cliquer « EN » → `/en/emsi/dakar`, menus et boutons en anglais, cliquer « FR » → retour ; formulaire de contact vide sur `/en/contact` → erreurs en anglais ; `/en/…` à 360 px sans défilement horizontal.
- [ ] **Step 2: Run** — Expected: FAIL.
- [ ] **Step 3: Implement** (grep des chaînes françaises restantes dans `frontend/src/components` et `frontend/src/app` à la fin ; toute chaîne visible restante doit être justifiée dans le rapport).
- [ ] **Step 4: Run** `npm test && npm run lint && npx tsc --noEmit -p . && npm run build` et e2e — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(site): interface en anglais, sélecteur de langue, dates et montants selon la langue`

---

### Task 10: Référencement bilingue

**Files:**
- Modify: layout `[locale]` (`<html lang>`), `frontend/src/lib/metadata.ts` (`alternates.languages` fr/en/x-default, `openGraph.locale`), `frontend/src/app/sitemap.ts` (les deux langues, `alternates`), `frontend/src/lib/structured-data.ts` (`inLanguage`), `opengraph-image` (texte selon la langue), `robots.ts` si besoin
- Test: `frontend/src/lib/structured-data.test.ts`, `frontend/e2e/i18n.spec.ts`

- [ ] **Step 1: Write the failing tests** : `/en/emsi` → `<html lang="en">`, liens `hreflang="fr"`, `"en"`, `"x-default"` corrects ; plan du site contient `/en/emsi/dakar` ; JSON-LD `inLanguage: "en"` sur une page anglaise.
- [ ] **Step 2: Run** — Expected: FAIL.
- [ ] **Step 3: Implement.**
- [ ] **Step 4: Run** — Expected: PASS.
- [ ] **Step 5: Commit** — `feat(seo): référencement dans les deux langues`

---

### Task 11: Documentation et vérification finale

**Files:**
- Modify: `CLAUDE.md` (§2 bis : site bilingue, traduction DeepL gratuite relue ; §3 architecture ; §7 commandes `emsi:translate` ; §8 état), `docs/GUIDE-ADMIN.md` (onglet Anglais, traductions à relire, lexique, quota, que faire sans compte DeepL, comment créer le compte DeepL API Free et où mettre la clé), `DEPLOYMENT.md` (`DEEPL_API_KEY`, `php artisan queue:work` requis en production, lancement `emsi:translate --all`)

- [ ] **Step 1: Run** `./vendor/bin/pint --test && php artisan test && composer test:mysql` — Expected: vert.
- [ ] **Step 2: Run** `cd frontend && npm run lint && npm test && npx tsc --noEmit -p . && npm run build && E2E_BASE_URL=http://localhost:3001 npm run e2e` — Expected: vert (relancer seul tout parcours en dépassement de délai et le noter).
- [ ] **Step 3: Documenter.**
- [ ] **Step 4: Commit** — `docs: site bilingue — guide de l'équipe, déploiement, état du projet`
