# R2 — Site bilingue français / anglais — conception

- **Date** : 30/09/2026
- **Décideur** : Momar Diop
- **Périmètre** : second sous-projet de la restructuration demandée par Boubacar Tall (R1 « un site, trois domaines » est fusionné, PR #12). Le site doit être bilingue **dès le lancement**, pour un public et des financeurs internationaux.
- **Décisions** (29–30/09/2026) :
  - l'anglais est **proposé par traduction automatique gratuite (DeepL API Free) et relu par l'équipe** ; aucun service payant (décision du 30/09/2026, après abandon de Claude pour raison de coût) ;
  - **sans compte DeepL, tout fonctionne** : l'équipe traduit à la main dans l'admin, le site affiche le français tant qu'un texte n'est pas traduit ;
  - une traduction automatique est **en ligne tout de suite**, l'admin liste les traductions « à relire » ;
  - stockage dans une **table de traductions à part** (les colonnes françaises ne bougent pas) ;
  - mêmes adresses sous `/en/…` ; l'admin reste en français.

## 1. Intention

- Un visiteur anglophone trouve un site **complet** en anglais : textes des pages, formations, actualités, agenda, menus, formulaires, messages, référencement.
- Le contenu anglais suit le français **sans travail manuel obligatoire** ; l'équipe garde le dernier mot par la relecture.
- Aucun risque pour le contenu français existant ; sans clé d'API, quota atteint ou panne, le site anglais affiche le français.
- **Jamais de dépense** : la traduction automatique reste dans la formule gratuite de DeepL (500 000 caractères par mois).

## 2. Données (Laravel)

### 2.1 Table `translations` (migration réversible)

| Colonne | Détail |
|---|---|
| `translatable_type`, `translatable_id` | fiche traduite (morph) |
| `field` | nom du champ (`title`, `blocks`, `seo`, …) |
| `locale` | `en` (une seule langue cible aujourd'hui, colonne prête pour d'autres) |
| `value` | texte anglais (`longText`) ; JSON pour `blocks`, `seo` et les champs tableau |
| `source_hash` | empreinte (sha256) de la valeur française traduite |
| `status` | `auto` (traduction automatique, à relire) ou `reviewed` (relue) |
| `previous_value` | dernière version relue quand une retraduction la remplace (consultable) |
| `translated_at`, `reviewed_at`, `reviewed_by` | suivi |

Unique sur (`translatable_type`, `translatable_id`, `field`, `locale`). Suppression en cascade avec la fiche (via le trait).

### 2.2 Modèles traduits (trait `HasTranslations` + `protected array $translatable`)

| Modèle | Champs |
|---|---|
| `Page` | `title`, `blocks`, `seo` |
| `Program` | `title`, `summary`, `description`, `level_label`, `duration_label`, `skills`, `outcomes`, `prerequisites`, `equipment`, `seo` |
| `Track` | `name`, `short_name`, `summary`, `description`, `skills`, `outcomes` |
| `Room` | `name`, `tagline`, `intro`, `cover_alt` |
| `Artwork` | `title`, `summary`, `creation_story`, `equipment`, `transcript`, `cover_alt` |
| `News` | `title`, `excerpt`, `content` |
| `Faq` | `question`, `answer` |
| `AgendaEvent` | `title`, `summary`, `content`, `venue`, `image_alt` |
| `Service` | `name`, `summary`, `description`, `image_alt` (l'unité de prix est une énumération : libellé traduit par le site) |
| `Place` | `tagline`, `description`, `highlights`, `opening_hours`, `image_alt` |
| `MenuItem` | `label` |
| `Setting` | `description`, `opening_hours`, `seo_title`, `seo_description` |

Les noms propres (`Place.name`, `Partner.name`, noms de personnes) ne sont pas traduits. Les slugs ne sont pas traduits.

### 2.3 Blocs de page

- Un extracteur parcourt les blocs et ne retient que les **feuilles texte** : clés `title`, `subtitle`, `eyebrow`, `text`, `body` (HTML), `caption`, `label`, `link_label`, `intro`, `question`, `answer`, `description`, `credits`, `value` (chiffres clés), `*_alt`, `words`, `highlight`, `quote`, `author_role`/`role`, `buttons[].label`, et leurs équivalents dans les répéteurs (slides, panels, items, tracks, hotspots, facts, steps…). Liste fermée dans une seule classe, testée.
- **Jamais traduits** : `url`, `link_url`, images et fichiers, couleurs, `layout`, `type`, identifiants, `domain`, nombres de réglage.
- La valeur anglaise d'un champ `blocks` est la **structure française complète** où seules les feuilles texte sont remplacées. Au rendu, si la structure française a changé depuis (bloc ajouté, retiré, déplacé), les feuilles anglaises sont réappliquées par chemin ; les feuilles sans traduction gardent le français.

## 3. Traduction automatique

### 3.1 Déclenchement

- Événement `saved` des modèles traduits : pour chaque champ traduit dont l'empreinte française diffère de `source_hash` (ou sans traduction), une tâche `TranslateRecord` est mise en file (`database`, déjà utilisée), regroupée par fiche (une tâche par fiche et par enregistrement, les tâches en double sont écartées).
- `Page` : seulement à la **publication** (`blocks` publiés), jamais sur `draft_blocks`.
- Interrupteur « Traduction automatique activée » dans les Paramètres du site (désactivé = aucune tâche).

### 3.2 Appel à DeepL (formule gratuite)

- Service : **DeepL API Free** (`https://api-free.deepl.com/v2/translate`), clé `DEEPL_API_KEY` dans l'environnement ; client HTTP de Laravel (pas de paquet supplémentaire), derrière une interface `Translator` (une implémentation DeepL, une implémentation factice pour les tests, une implémentation « aucune » quand la clé est absente).
- Paramètres : `source_lang=FR`, `target_lang=EN-GB` (anglais britannique, usage courant des institutions culturelles et bailleurs européens), `tag_handling=html` pour les champs HTML (la mise en forme est conservée), `preserve_formatting=1`. Tous les textes d'une fiche partent dans un seul appel (liste de textes, 50 au plus par appel ; au-delà, plusieurs appels).
- **Noms propres et lexique** : un glossaire DeepL (français → anglais) est créé et tenu à jour à partir du **lexique** réglable dans les Paramètres du site (ex. « VAE » → « Recognition of Prior Learning (VAE) », « filière » → « programme track ») ; les noms à ne jamais traduire (EMSI, Impact Live Studio, Maison de la culture Habib Faye, Grand Théâtre National Doudou Ndiaye Coumba Rose, noms de personnes et de lieux) y figurent avec eux-mêmes comme traduction. Le lexique est prérempli avec ces noms. À chaque modification du lexique, le glossaire est recréé.
- **Quota gratuit** : avant chaque tâche, lecture du compteur DeepL (`/v2/usage`) ; si l'envoi ferait dépasser **95 %** de la limite mensuelle, la tâche est remise à plus tard (reprise automatique le mois suivant) et l'admin affiche « Quota gratuit de traduction atteint ce mois-ci ». Le compte gratuit ne peut de toute façon pas être facturé ; ce garde-fou évite des échecs en série.
- Vérification de la réponse : autant de textes renvoyés qu'envoyés, aucun texte vide si la source ne l'est pas ; sinon échec.
- Erreur (réseau, 429, 456 quota, 5xx) : 3 tentatives espacées ; ensuite l'échec est journalisé et visible dans « Traductions à relire » (statut « Échec »). Aucun impact sur le site (repli français).
- Clé absente : aucune tâche, l'admin indique « Traduction automatique non configurée : traduisez à la main dans l'onglet Anglais ».

### 3.3 Relecture

- Corriger un texte anglais dans l'admin → `status = reviewed`.
- Si le français d'un champ **relu** change ensuite : nouvelle traduction automatique, l'ancienne valeur relue passe dans `previous_value`, `status = auto`.
- Un champ relu dont le français n'a pas changé n'est **jamais** écrasé.

### 3.4 Lancement : `php artisan emsi:translate {--all} {--model=} {--dry-run}`

- `--all` : met en file la traduction de tout le contenu publié sans traduction à jour, en commençant par les pages, puis formations, univers, menus, lieux, services, actualités récentes. Les tâches respectent le quota (§3.2) : si le contenu dépasse la limite du mois, le reste se traduit automatiquement le mois suivant.
- Affiche avant de lancer le nombre de fiches, le **nombre de caractères** à envoyer et le quota restant du mois, puis demande confirmation (`--force` pour l'automatiser).
- `--dry-run` : n'appelle pas le service de traduction, affiche seulement le décompte.

## 4. API publique

- Tous les points d'accès de lecture acceptent `?locale=en` (défaut `fr`) ; les ressources et le `BlockResolver` appliquent la traduction champ par champ, repli sur le français.
- Réponse de page : `locale`, et `alternates: {fr: "/…", en: "/en/…"}`.
- Formulaires publics (candidature, contact, soutien, réservation) : champ `locale` enregistré sur la demande (colonne ajoutée, réversible) ; les messages de validation Laravel sont renvoyés dans la langue demandée (`lang/en`).
- Cache Next : les étiquettes de revalidation couvrent les deux langues (même étiquette `content`).

## 5. Site (Next.js)

- **Routes** : tout passe sous `app/[locale]/…` ; l'aiguillage (`proxy.ts`/middleware) réécrit les adresses sans préfixe vers `fr` et laisse passer `/en/…`. Les adresses françaises actuelles et les redirections de R1 restent identiques ; les redirections valent aussi sous `/en`.
- **Sélecteur FR · EN** dans l'en-tête et le menu mobile : lien vers la même page dans l'autre langue (`alternates`), choix mémorisé en cookie, jamais de redirection automatique selon le navigateur.
- **Dictionnaires** `fr` et `en` typés (clés identiques imposées par TypeScript) pour tous les textes fixes : en-tête, pied de page, formulaires et validations zod, messages, boutons, héros (« Écouter l'œuvre »…), 404, erreurs, lecteur audio, fil d'Ariane, sous-navigation.
- **Formats** : dates et nombres selon la langue (`Intl`), montants toujours en FCFA.
- **Référencement** : `<html lang>`, `hreflang` fr/en/x-default sur chaque page, plan du site dans les deux langues, métadonnées et image de partage dans la langue, JSON-LD avec `inLanguage`.

## 6. Admin (reste en français)

- **Onglet « Anglais »** sur chaque ressource traduite : pour chaque champ, le français (lecture seule) à côté de l'anglais (modifiable), le statut (« Traduction automatique », « Relue », « Échec », « Pas encore traduit »), et les actions « Marquer comme relue », « Retraduire ». Pour les pages, les feuilles texte des blocs sont présentées une à une avec leur contexte (bloc, champ).
- **Page « Traductions à relire »** : liste filtrable (type de contenu, statut), lien vers la fiche ; widget du tableau de bord avec le nombre à relire et les échecs.
- **Paramètres du site** : interrupteur de traduction automatique, lexique (répéteur « Terme français » / « Traduction anglaise », prérempli avec les noms à ne pas traduire), et l'état du quota gratuit du mois (caractères utilisés / 500 000).
- Droits : `directeur` et `communication` relisent et modifient ; les autres rôles consultent.

## 7. Tests

- **Laravel** (service de traduction simulé, aucun appel réel) : migrations aller-retour ; API `?locale=en` avec repli ; extraction des feuilles texte des blocs (liens, images et réglages intacts) ; réapplication des feuilles après modification de la structure ; modification du français → nouvelle tâche, statut `auto` ; champ relu non écrasé sans changement du français, `previous_value` rempli sinon ; brouillon de page non traduit ; clé absente → aucune tâche et saisie manuelle possible ; erreur → échec journalisé sans impact ; quota proche de la limite → tâche reportée ; glossaire recréé quand le lexique change ; validation de la réponse (clés manquantes) ; commande `emsi:translate --dry-run` et décompte des caractères ; formulaires avec `locale` et messages en anglais ; droits de relecture.
- **Site** : dictionnaires complets (test unitaire) ; Playwright : sélecteur FR → EN sur la même page et retour, `hreflang` présents, une page anglaise sans traduction affiche le français, un formulaire affiche ses erreurs en anglais, redirections R1 sous `/en`, aucun défilement horizontal à 360 px en anglais.
- Suites complètes : `php artisan test`, `composer test:mysql`, `npm run lint`, `npm test`, `npm run build`, `npm run e2e`.

## 8. Hors périmètre

- Autres langues (wolof, arabe…) : la structure le permet, non prévu.
- Admin en anglais ; courriels transactionnels bilingues (aucun courriel au candidat aujourd'hui).
- Traduction des fichiers (PDF, sons) et des textes intégrés aux images.
- Tout service de traduction payant (Claude, DeepL Pro, Google) : l'interface `Translator` permet d'en brancher un plus tard sans changer le reste.
- Adresses traduites (`/en/habib-faye-house`).
